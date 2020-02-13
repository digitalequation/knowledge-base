<?php

namespace DigitalEquation\KnowledgeBase\Http\Controllers\API;

use App\Http\Controllers\Controller;
use DigitalEquation\KnowledgeBase\Contracts\Repositories\KnowledgeBaseRepository;
use DigitalEquation\KnowledgeBase\Services\KnowledgeBaseService;
use Illuminate\Contracts\Routing\ResponseFactory;
use Symfony\Component\HttpFoundation\Response;

class KnowledgeBaseAPIController extends Controller
{
    protected KnowledgeBaseRepository $kb;

    protected KnowledgeBaseService $service;

    /**
     * KnowledgeBaseAPIController constructor.
     *
     * @param KnowledgeBaseRepository $kb
     * @param KnowledgeBaseService $service
     */
    public function __construct(KnowledgeBaseRepository $kb, KnowledgeBaseService $service)
    {
        $this->middleware('role:admin')->only([
            'getUpdateCache',
        ]);

        $this->kb      = $kb;
        $this->service = $service;
    }

    /**
     * Return category index or article index for a given category.
     *
     * @param string $categorySlug (optional) category slug to retrieve index for (else fallback to category index)
     *
     * @return ResponseFactory|Response
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
     * @return ResponseFactory|Response
     */
    public function getCategory($slug)
    {
        $category = $this->service->getCachedCategories()->where('slug', $slug)->first();

        abort_if(empty($category), 403, 'Knowledge base category does not exist...');

        return success(['category' => $category]);
    }

    /**
     * Return a single article.
     *
     * @param string $categorySlug category slug of the article to retrieve
     * @param string $articleSlug article slug to retrieve
     *
     * @return ResponseFactory|Response
     */
    public function getArticle($categorySlug, $articleSlug)
    {
        return $this->kb->getArticle($categorySlug, $articleSlug);
    }

    /**
     * Return article search results for given search term.
     *
     * @param string $term search term to retrieve results for
     *
     * @return ResponseFactory|Response
     */
    public function getSearch($term)
    {
        return $this->kb->search($term);
    }

    /**
     * Force an immediate cache update.
     *
     * @return ResponseFactory|Response
     */
    public function getUpdateCache()
    {
        return success(['updated' => $this->service->clearCache()]);
    }
}