<?php

namespace DigitalEquation\KnowledgeBase\Contracts\Repositories;

use DigitalEquation\KnowledgeBase\Services\KnowledgeBaseService;
use Illuminate\Contracts\Routing\ResponseFactory;
use Symfony\Component\HttpFoundation\Response;

interface KnowledgeBaseRepository
{
    /**
     * TicketRepository constructor.
     * @param KnowledgeBaseService $kbService
     */
    public function __construct(KnowledgeBaseService $kbService);

    /**
     * Return category index or article index for a given category.
     *
     * @param null $categorySlug
     *
     * @return ResponseFactory|Response
     */
    public function getCategory($categorySlug = null);

    /**
     * Get a single article.
     *
     * @param string $categorySlug category slug of the article to retrieve
     * @param string $articleSlug article slug to retrieve
     *
     * @return ResponseFactory|Response
     */
    public function getArticle($categorySlug, $articleSlug);

    /**
     * Get article by search term.
     *
     * @param string $term search term to retrieve results for
     *
     * @return mixed
     */
    public function search($term);
}