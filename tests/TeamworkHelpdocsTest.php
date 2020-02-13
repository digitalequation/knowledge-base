<?php

namespace DigitalEquation\KnowledgeBase\Tests;

use DigitalEquation\KnowledgeBase\Exceptions\TeamworkHttpException;
use DigitalEquation\KnowledgeBase\KnowledgeBase;
use DigitalEquation\KnowledgeBase\Services\TeamworkService;

class TeamworkHelpdocsTest extends TeamworkTestCase
{
    /** @test */
    public function it_should_throw_an_http_exception_on_sites_request()
    {
        $this->app['config']->set('teamwork.desk.domain', 'undefined');

        $this->expectException(TeamworkHttpException::class);
        (new KnowledgeBase)->getSites();
    }

    /** @test */
    public function it_should_return_an_array_of_sites()
    {
        $body     = file_get_contents(__DIR__ . '/Mock/sites-response.json');
        $client   = $this->mockClient(200, $body);
        $response = new TeamworkService($client);

        $this->assertEquals($body, json_encode($response->getSites()));
    }

    /** @test */
    public function it_should_throw_an_http_exception_on_single_site_request()
    {
        $this->app['config']->set('teamwork.desk.domain', 'undefined');

        $this->expectException(TeamworkHttpException::class);
        (new KnowledgeBase)->getSite(0);
    }

    /** @test */
    public function it_should_get_a_site_by_id()
    {
        $body     = file_get_contents(__DIR__ . '/Mock/site-response.json');
        $client   = $this->mockClient(200, $body);
        $response = new TeamworkService($client);

        $this->assertEquals($body, json_encode($response->getSite(546)));
    }

    /** @test */
    public function it_should_throw_an_http_exception_on_site_categories_request()
    {
        $this->app['config']->set('teamwork.desk.domain', 'undefined');

        $this->expectException(TeamworkHttpException::class);
        (new KnowledgeBase)->getSiteCategories(0);
    }

    /** @test */
    public function it_should_get_site_categories()
    {
        $body     = file_get_contents(__DIR__ . '/Mock/categories-response.json');
        $client   = $this->mockClient(200, $body);
        $response = new TeamworkService($client);

        $this->assertEquals($body, json_encode($response->getSiteCategories(546)));
    }

    /** @test */
    public function it_should_throw_an_http_exception_on_site_articles_request()
    {
        $this->app['config']->set('teamwork.desk.domain', 'undefined');

        $this->expectException(TeamworkHttpException::class);
        (new KnowledgeBase)->getSiteArticles(0);
    }

    /** @test */
    public function it_should_get_articles_within_a_site()
    {
        $body     = file_get_contents(__DIR__ . '/Mock/articles-response.json');
        $client   = $this->mockClient(200, $body);
        $response = new TeamworkService($client);

        $this->assertEquals($body, json_encode($response->getSiteArticles(546)));
    }

    /** @test */
    public function it_should_throw_an_http_exception_on_article_request()
    {
        $this->app['config']->set('teamwork.desk.domain', 'undefined');

        $this->expectException(TeamworkHttpException::class);
        (new KnowledgeBase)->getArticle(0);
    }

    /** @test */
    public function it_should_get_a_single_article()
    {
        $body     = file_get_contents(__DIR__ . '/Mock/article-response.json');
        $client   = $this->mockClient(200, $body);
        $response = new TeamworkService($client);

        $this->assertEquals($body, json_encode($response->getArticle(546)));
    }

    /** @test */
    public function it_should_get_all_articles()
    {
        $body     = file_get_contents(__DIR__ . '/Mock/all-articles-response.json');
        $client   = $this->mockClient(200, $body);
        $response = new TeamworkService($client);

        $body = json_encode([json_decode($body)->article]);
        $this->assertEquals($body, json_encode($response->getArticles([3342])));
    }

    /** @test */
    public function it_should_throw_an_http_exception_on_category_articles_request()
    {
        $this->app['config']->set('teamwork.desk.domain', 'undefined');

        $this->expectException(TeamworkHttpException::class);
        (new KnowledgeBase)->getCategoryArticles(0);
    }

    /** @test */
    public function it_should_get_all_articles_within_a_category()
    {
        $body     = file_get_contents(__DIR__ . '/Mock/all-articles-within-category-response.json');
        $client   = $this->mockClient(200, $body);
        $response = new TeamworkService($client);

        $this->assertEquals($body, json_encode($response->getCategoryArticles(3342)));
    }
}
