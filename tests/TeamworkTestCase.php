<?php

namespace DigitalEquation\KnowledgeBase\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase;

class TeamworkTestCase extends TestCase
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * Define environment setup.
     *
     * @param Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        // Setup the Teamwork domain and API Key
        $app['config']->set('knowledge-base.domain', 'somedomain');
        $app['config']->set('knowledge-base.key', '04983o4krjwlkhoirtht983uytkjhgkjfh');

        $this->app = $app;
    }

    /**
     * Build the client mock.
     *
     * @param $status
     * @param $body
     *
     * @return Client
     */
    protected function mockClient($status, $body): Client
    {
        $mock    = new MockHandler([new Response($status, [], $body)]);
        $handler = HandlerStack::create($mock);

        return new Client(['handler' => $handler]);
    }
}
