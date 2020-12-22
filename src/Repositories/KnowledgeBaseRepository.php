<?php

namespace DigitalEquation\KnowledgeBase\Repositories;

use DigitalEquation\KnowledgeBase\Contracts\Repositories\KnowledgeBaseRepository as KnowledgeBaseRepositoryContract;
use DigitalEquation\KnowledgeBase\Services\KnowledgeBaseService;

class KnowledgeBaseRepository implements KnowledgeBaseRepositoryContract
{
    protected KnowledgeBaseService $service;

    public function __construct(KnowledgeBaseService $service)
    {
        $this->service = $service;
    }

    public function getCategory($categorySlug = null)
    {
        if (!$categorySlug) {
            // CASE 1 - No category slug given => get category index
            return response()->json([
                'success'    => true,
                'categories' => $this->service->getCachedCategories()
                    ->filter(fn($category) => $category['id'])
                    ->map(fn($category) => collect($category)
                        ->only(['name', 'slug'])
                        ->put('icon', $this->service->getCategoryIcon($category['slug'])))->values(),
            ]);
        }

        // CASE 2 - Category slug given => get article index for category
        $category = $this->service->getCachedCategories()->firstWhere('slug', $categorySlug);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid knowledge base category!',
            ]);
        }

        return response()->json([
            'success'  => true,
            'category' => $category,
            'articles' => $this->service->getCachedArticles()
                ->filter(fn($article) => in_array($categorySlug, $article['categories'], true))
                ->map(fn($article) => collect($article)
                    ->put('url', sprintf('%s/knowledge-base/category/%s#%s',
                        config('app.url'),
                        $categorySlug,
                        urlencode($article['slug'])
                    ))->only(['title', 'url']))->values(),
        ]);
    }

    public function getArticle($categorySlug, $articleSlug)
    {
        $article = $this->service->getCachedArticles()->firstWhere('slug', $articleSlug);

        if (empty($article) || !in_array($categorySlug, $article['categories'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid knowledge base article!',
            ]);
        }

        // Generate standard links for related articles
        foreach ($article['relatedArticles'] as $key => $related) {
            $relatedArticle = $this->service->getCachedArticles()->firstWhere('id', $related['id']);

            $article['relatedArticles'][$key]['url'] = sprintf('%s#%s',
                route('kb.category', ['slug' => $relatedArticle['categories'][0]]),
                urlencode($relatedArticle['slug'])
            );

            unset($article['relatedArticles'][$key]['id']);
        }

        return response()->json([
            'success' => true,
            'article' => collect($article)->only(['contents', 'title', 'keywords', 'relatedArticles']),
        ]);
    }

    public function search($term): \Illuminate\Http\JsonResponse
    {
        // Strip search term of unwanted characters
        $term = preg_replace('[^a-zA-Z0-9- ]', '', $term);

        if (empty($term)) {
            return response()->json([
                'success' => true,
                'results' => [],
            ]);
        }

        $results = $this->service->getCachedArticles()
            ->map(function ($article) use ($term) {
                // Defines a search relevancy score (with 0 meaning the term is not found at all)
                $score = 0;

                // Define a maximum length buffer to use left/right when storing content match summaries
                $contentMatchBuffer = 20;

                // Define a tag stripped version of the article content alongside its length
                $contentText       = html_entity_decode(strip_tags($article['contents']));
                $contentTextLength = strlen($contentText);

                // Define an array for collecting matched content text summaries
                $contentMatchSummaries = [];

                // Define Regex pattern to use for matching
                $regex = sprintf('/\b%s\b/i', $term);

                // CASE I. Increase score by 2.5 points in case of a title match (case insensitive)
                preg_match($regex, $article['title'], $titleMatches);
                if (count($titleMatches)) {
                    $score += 2.5;
                }

                // CASE II.
                // Increase score by 0.8 points for every partial keyword match (case insensitive + trim)
                // Increase score by 1.3 points for every full keyword match (case insensitive + trim)
                $keywordMatch = false;
                foreach ($article['keywords'] as $keyword) {
                    $keyword = trim($keyword);
                    preg_match($regex, $keyword, $keywordMatches);

                    if (!count($keywordMatches)) {
                        continue;
                    }

                    $keywordMatch = true;

                    $score += ($keyword === $term) ? 1.3 : 0.8;
                }

                // CASE III. Increase score by 0.8 points for every full-word content match (case insensitive)
                preg_match_all($regex, $contentText, $contentMatches, PREG_OFFSET_CAPTURE);
                $score += 0.8 * count($contentMatches[0]);

                // Extract content summaries for every match
                foreach ($contentMatches[0] as $key => $match) {
                    // Grab length of matched segment
                    $matchLength = strlen($match[0]);

                    ///////////////////////
                    // STARTING POSITION //
                    ///////////////////////
                    // Calculate starting position
                    $startPosition = $match[1] - $contentMatchBuffer;

                    // Check if starting position is inside the text
                    $startInside = $startPosition > 0;

                    // Return starting position to 0 if negative
                    $startPosition = $startInside ? $startPosition : 0;

                    /////////////////////
                    // ENDING POSITION //
                    /////////////////////
                    // Calculate ending position
                    $endPosition = $match[1] + $matchLength + $contentMatchBuffer;

                    // Check if ending position is inside the text
                    $endInside = $endPosition < $contentTextLength;

                    // Return ending position to content text length if it exceeds the length
                    $endPosition = $endInside ? $endPosition : $contentTextLength;

                    /////////////
                    // EXTRACT //
                    /////////////
                    // Extract buffered left/right match summary from content text
                    $contentMatchSummaries[] = sprintf('%s%s%s',
                        $startInside ? '...' : '',
                        mb_substr($contentText, $startPosition, $endPosition - $startPosition),
                        $endInside ? '...' : ''
                    );
                }

                $article['score']            = $score;
                $article['keywordMatch']     = $keywordMatch;
                $article['contentSummaries'] = $contentMatchSummaries;

                return collect($article)->only([
                    'categories', 'contents', 'slug', 'title', 'keywords', 'score', 'keywordMatch', 'contentSummaries',
                ]);
            })
            ->filter(fn($article) => $article['score'])
            ->values()->toArray();

        // Sort results based on score
        $resultScore = array_column($results, 'score');
        array_multisort($resultScore, SORT_DESC, $results);

        return response()->json([
            'success' => true,
            'results' => $results,
        ]);
    }
}