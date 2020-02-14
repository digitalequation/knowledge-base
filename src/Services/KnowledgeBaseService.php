<?php

namespace DigitalEquation\KnowledgeBase\Services;

use Cache;
use DigitalEquation\KnowledgeBase\Exceptions\TeamworkHttpException;
use Exception;

class KnowledgeBaseService
{
    public string $cacheKey = 'kb.data';

    protected TeamworkService $service;

    public function __construct(TeamworkService $service)
    {
        $this->service = $service;
    }

    /**
     * Return icon for given category
     * @param  string $slug category slug to retrieve icon for
     * @return string
     */
    public function getCategoryIcon($slug)
    {
        switch ($slug) {
            case 'getting-started': return 'stars';
            case 'video-tutorials': return 'file-video';
        }

        return 'book-open';
    }

    /**
     * Return an index collection of article summaries
     * @return Collection
     * @throws TeamworkHttpException
     */
    public function getArticleIndex()
    {
        // The current Teamwork pagination limit
        $perPage = 25;

        $index = [];

        $page  = 0;
        $count = 0;
        do {
            $page++;

            $data  = $this->service->getSiteArticles(config('knowledge-base.site_id'), $page);
            $count = $data['count'];

            foreach ($data['articles'] as $article) {
                if ($article['status'] === 'Published') {
                    $index[] = $article;
                }
            }

        } while (($page * $perPage) < $count);

        return collect($index);
    }

    /**
     * Get a cached array of knowledge base data
     * @return array
     */
    public function getCachedData()
    {
        // Cache knowledge base data forever (the data will be manually purged by the scheduler)
        return Cache::rememberForever($this->cacheKey, function() {

            // 1. Collect categories (along with ID <=> slug category equivalence array)
            $categoriesIDSlug = [];

            $teamworkCategories = $this->service->getSiteCategories(config('knowledge-base.site_id'));
            $categories = collect($teamworkCategories['categories'])
                ->map(function($category) use(&$categoriesIDSlug) {
                    $categoriesIDSlug[$category['id']] = $category['slug'];

                    return collect($category)->only(['id', 'name', 'slug', 'displayOrder']);
                });

            // 2. Collect article IDs to retrieve
            $articleIDs = $this->getArticleIndex()->pluck('id')->toArray();

            // 3. Collect and sort article contents based on ID
            $articles = collect($this->service->getArticles($articleIDs))
                ->map(function($article) {
                    return collect($article)->only([
                        'id', 'categories', 'contents', 'createdAt', 'displayOrder', 'keywords', 'relatedArticles',
                        'slug', 'title', 'updatedAt'
                    ]);
                })
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

    /**
     * Get a cached collection of all knowledge base articles
     * @return Collection
     */
    public function getCachedArticles()
    {
        return $this->getCachedData()['articles'];
    }

    /**
     * Get a cached collection of all knowledge base categories
     * @return Collection
     */
    public function getCachedCategories()
    {
        return $this->getCachedData()['categories'];
    }

    /**
     * Clear knowledge base data cache and reload remote content
     * (this method will revert to the previously cached version if it encounters any errors)
     */
    public function clearCache()
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
}