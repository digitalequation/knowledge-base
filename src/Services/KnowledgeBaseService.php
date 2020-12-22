<?php

namespace DigitalEquation\KnowledgeBase\Services;

use Cache;
use DigitalEquation\KnowledgeBase\Exceptions\KnowledgeBaseJsonException;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Pool as GuzzlePool;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Illuminate\Support\Collection;
use Psr\Http\Message\StreamInterface;

class KnowledgeBaseService
{
    public string $cacheKey = 'kb.data';

    protected KnowledgeBaseService $service;

    protected Client $client;

    public function __construct($client = null)
    {
        if ($client instanceof Client) {
            $this->client = $client;
        } else {
            $this->client = new Client([
                'base_uri' => sprintf('https://%s.teamwork.com/desk/v1/', config('knowledge-base.domain')),
                'auth'     => [config('knowledge-base.key'), ''],
            ]);
        }
    }

    public function getCategoryIcon(string $slug): string
    {
        switch ($slug) {
            case 'getting-started':
                return 'stars';
            case 'video-tutorials':
                return 'file-video';
        }

        return 'book-open';
    }

    public function getArticleIndex(): Collection
    {
        // The current Teamwork pagination limit
        $perPage = 25;

        $index = [];

        $page  = 0;
        $count = 0;
        do {
            $page++;

            $data  = $this->getSiteArticles(config('knowledge-base.site_id'), $page);
            $count = $data['count'];

            foreach ($data['articles'] as $article) {
                if ($article['status'] === 'Published') {
                    $index[] = $article;
                }
            }

        } while (($page * $perPage) < $count);

        return collect($index);
    }

    public function getCachedData(): array
    {
        // Cache knowledge base data forever (the data will be manually purged by the scheduler)
        return Cache::rememberForever($this->cacheKey, function () {

            // 1. Collect categories (along with ID <=> slug category equivalence array)
            $categoriesIDSlug = [];

            $teamworkCategories = $this->getSiteCategories(config('knowledge-base.site_id'));
            $categories         = collect($teamworkCategories['categories'])
                ->map(function ($category) use (&$categoriesIDSlug) {
                    $categoriesIDSlug[$category['id']] = $category['slug'];

                    return collect($category)->only(['id', 'name', 'slug', 'displayOrder']);
                });

            // 2. Collect article IDs to retrieve
            $articleIDs = $this->getArticleIndex()->pluck('id')->toArray();

            // 3. Collect and sort article contents based on ID
            $articles = collect($this->getArticles($articleIDs))
                ->map(fn($article) => collect($article)->only([
                    'id', 'categories', 'contents', 'createdAt', 'displayOrder', 'keywords', 'relatedArticles',
                    'slug', 'title', 'updatedAt',
                ]))
                ->toArray();

            array_multisort(array_column($articles, 'id'), SORT_DESC, $articles);

            // 4. Replace article category IDs with slugs
            foreach ($articles as $articleKey => $article) {
                foreach ($article['categories'] as $categoryKey => $categoryID) {
                    $articles[$articleKey]['categories'][$categoryKey] = $categoriesIDSlug[$categoryID];
                }
            }

            $articles = collect($articles);

            return compact('articles', 'categories');
        });
    }

    public function getCachedArticles(): Collection
    {
        return $this->getCachedData()['articles'];
    }

    public function getCachedCategories(): Collection
    {
        return $this->getCachedData()['categories'];
    }

    /**
     * Clear knowledge base data cache and reload remote content
     * (this method will revert to the previously cached version if it encounters any errors)
     */
    public function clearCache(): bool
    {
        $previous = Cache::get($this->cacheKey);

        Cache::forget($this->cacheKey);

        try {
            $this->getCachedData();
            return true;
        } catch (Exception $e) {
            Cache::forever($this->cacheKey, $previous);
            return false;
        }
    }

    public function getSites(): array
    {
        return $this->getResponse(
            $this->client->get('helpdocs/sites.json')->getBody()
        );
    }

    public function getSite(string $siteID): array
    {
        return $this->getResponse(
            $this->client->get(sprintf('helpdocs/sites/%s.json', $siteID))->getBody()
        );
    }

    public function getCategoryArticles(int $categoryID, $page = 1): array
    {
        return $this->getResponse(
            $this->client->get(sprintf('helpdocs/categories/%s/articles.json', $categoryID), [
                'query' => compact('page'),
            ])->getBody()
        );
    }

    public function getSiteArticles(int $siteID, $page = 1): array
    {
        return $this->getResponse(
            $this->client->get(sprintf('helpdocs/sites/%s/articles.json', $siteID), [
                'query' => compact('page'),
            ])->getBody()
        );
    }

    public function getArticle(int $articleID): array
    {
        return $this->getResponse(
            $this->client->get(sprintf('helpdocs/articles/%s.json', $articleID))->getBody()
        );
    }

    public function getArticles($articleIDs): array
    {
        $articles = [];

        $requests = array_map(static function ($articleID) {
            return new GuzzleRequest('GET', sprintf('helpdocs/articles/%s.json', $articleID));
        }, $articleIDs);

        $pool = new GuzzlePool($this->client, $requests, [
            'concurrency' => 10,
            'fulfilled'   => function ($response) use (&$articles) {
                $response = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

                $articles[] = $response['article'];
            },
        ]);

        $promise = $pool->promise();
        $promise->wait();

        return $articles;
    }

    public function getSiteCategories(int $siteID): array
    {
        return $this->getResponse(
            $this->client->get(sprintf('helpdocs/sites/%s/categories.json', $siteID))->getBody()
        );
    }

    private function getResponse(StreamInterface $body)
    {
        try {
            return json_decode($body->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new KnowledgeBaseJsonException($e->getMessage());
        }
    }
}