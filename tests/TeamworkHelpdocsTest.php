<?php

namespace DigitalEquation\KnowledgeBase\Tests;

use DigitalEquation\KnowledgeBase\KnowledgeBase;
use DigitalEquation\KnowledgeBase\Services\KnowledgeBaseService;
use GuzzleHttp\Exception\ClientException;

class TeamworkHelpdocsTest extends TeamworkTestCase
{
    /** @test */
    public function it_should_throw_an_http_exception_on_sites_request(): void
    {
        $this->app['config']->set('teamwork.desk.domain', 'undefined');

        $this->expectException(ClientException::class);
        (new KnowledgeBase)->getSites();
    }

    /** @test */
    public function it_should_return_an_array_of_sites(): void
    {
        $body     = file_get_contents(__DIR__ . '/Mock/sites-response.json');
        $client   = $this->mockClient(200, $body);
        $response = new KnowledgeBaseService($client);

        self::assertEquals($body, json_encode($response->getSites(), JSON_THROW_ON_ERROR));
    }

    /** @test */
    public function it_should_throw_an_http_exception_on_single_site_request(): void
    {
        $this->app['config']->set('teamwork.desk.domain', 'undefined');

        $this->expectException(ClientException::class);
        (new KnowledgeBase)->getSite(0);
    }

    /** @test */
    public function it_should_get_a_site_by_id(): void
    {
        $body     = file_get_contents(__DIR__ . '/Mock/site-response.json');
        $client   = $this->mockClient(200, $body);
        $response = new KnowledgeBaseService($client);

        self::assertEquals($body, json_encode($response->getSite(546), JSON_THROW_ON_ERROR));
    }

    /** @test */
    public function it_should_throw_an_http_exception_on_site_categories_request(): void
    {
        $this->app['config']->set('teamwork.desk.domain', 'undefined');

        $this->expectException(ClientException::class);
        (new KnowledgeBase)->getSiteCategories(0);
    }

    /** @test */
    public function it_should_get_site_categories(): void
    {
        $body     = file_get_contents(__DIR__ . '/Mock/categories-response.json');
        $client   = $this->mockClient(200, $body);
        $response = new KnowledgeBaseService($client);

        self::assertEquals($body, json_encode($response->getSiteCategories(546), JSON_THROW_ON_ERROR));
    }

    /** @test */
    public function it_should_throw_an_http_exception_on_site_articles_request(): void
    {
        $this->app['config']->set('teamwork.desk.domain', 'undefined');

        $this->expectException(ClientException::class);
        (new KnowledgeBase)->getSiteArticles(0);
    }

    /** @test */
    public function it_should_get_articles_within_a_site(): void
    {
        $body     = file_get_contents(__DIR__ . '/Mock/articles-response.json');
        $client   = $this->mockClient(200, $body);
        $response = new KnowledgeBaseService($client);

        self::assertEquals($body, json_encode($response->getSiteArticles(546), JSON_THROW_ON_ERROR));
    }

    /** @test */
    public function it_should_throw_an_http_exception_on_article_request(): void
    {
        $this->app['config']->set('teamwork.desk.domain', 'undefined');

        $this->expectException(ClientException::class);
        (new KnowledgeBase)->getArticle(0);
    }

    /** @test */
    public function it_should_get_a_single_article(): void
    {
        $body     = file_get_contents(__DIR__ . '/Mock/article-response.json');
        $client   = $this->mockClient(200, $body);
        $response = new KnowledgeBaseService($client);

        self::assertEquals($body, json_encode($response->getArticle(546), JSON_THROW_ON_ERROR));
    }

    /** @test */
    public function it_should_get_all_articles(): void
    {
        $body     = file_get_contents(__DIR__ . '/Mock/all-articles-response.json');
        $client   = $this->mockClient(200, $body);
        $response = new KnowledgeBaseService($client);

        $body = json_encode([json_decode($body)->article], JSON_THROW_ON_ERROR);
        self::assertEquals($body, json_encode($response->getArticles([3342]), JSON_THROW_ON_ERROR));
    }

    /** @test */
    public function it_should_throw_an_http_exception_on_category_articles_request(): void
    {
        $this->app['config']->set('teamwork.desk.domain', 'undefined');

        $this->expectException(ClientException::class);
        (new KnowledgeBase)->getCategoryArticles(0);
    }

    /** @test */
    public function it_should_get_all_articles_within_a_category(): void
    {
        $body     = file_get_contents(__DIR__ . '/Mock/all-articles-within-category-response.json');
        $client   = $this->mockClient(200, $body);
        $response = new KnowledgeBaseService($client);

        self::assertEquals($body, json_encode($response->getCategoryArticles(3342), JSON_THROW_ON_ERROR));
    }
}
