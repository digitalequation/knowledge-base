<?php

namespace DigitalEquation\KnowledgeBase\Http\Controllers\API;

use DigitalEquation\KnowledgeBase\Contracts\Repositories\KnowledgeBaseRepository;
use DigitalEquation\KnowledgeBase\Services\KnowledgeBaseService;

class KnowledgeBaseAPIController
{
    protected KnowledgeBaseRepository $kb;

    protected KnowledgeBaseService $service;

    public function __construct(KnowledgeBaseRepository $kb, KnowledgeBaseService $service)
    {
        $this->kb      = $kb;
        $this->service = $service;
    }

    /**
     * Return category index or article index for a given category.
     *
     * @param string $categorySlug (optional) category slug to retrieve index for (else fallback to category index)
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getIndex($categorySlug = null)
    {
        return $this->kb->getCategory($categorySlug);
    }

    /**
     * Display knowledge base article index for a category
     *
     * @param string $slug category slug to get article index for
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategory(string $slug): \Illuminate\Http\JsonResponse
    {
        $category = $this->service->getCachedCategories()->firstWhere('slug', $slug);

        abort_if(empty($category), 403, 'Knowledge base category does not exist...');

        return response()->json([
            'success'  => true,
            'category' => $category,
        ]);
    }

    /**
     * Return a single article.
     *
     * @param string $categorySlug category slug of the article to retrieve
     * @param string $articleSlug  article slug to retrieve
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getArticle(string $categorySlug, string $articleSlug)
    {
        return $this->kb->getArticle($categorySlug, $articleSlug);
    }

    /**
     * Return article search results for given search term.
     *
     * @param string $term search term to retrieve results for
     *
     * @return mixed
     */
    public function getSearch(string $term)
    {
        return $this->kb->search($term);
    }

    /**
     * Force an immediate cache update.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUpdateCache(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'updated' => $this->service->clearCache(),
        ]);
    }
}