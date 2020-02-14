<?php

namespace DigitalEquation\KnowledgeBase;

use DigitalEquation\KnowledgeBase\Console\{ConfigCommand, InstallCommand};
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class KnowledgeBaseServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (!config('knowledge-base.enabled')) {
            return;
        }

        $this->registerRoutes();
        $this->registerPublishing();
        $this->registerCommands();
    }

    public function register(): void
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/knowledge-base.php', 'knowledge-base');

        // Register the main class to use with the facade
        $this->app->singleton('knowledge-base', function () {
            return new KnowledgeBase;
        });

        // Register tickets service
        $services = [
            'Contracts\Repositories\KnowledgeBaseRepository' => 'Repositories\KnowledgeBaseRepository'
        ];

        foreach ($services as $key => $value) {
            $this->app->singleton('DigitalEquation\KnowledgeBase\\' . $key, 'DigitalEquation\KnowledgeBase\\' . $value);
        }
    }

    /**
     * Register the package routes.
     */
    protected function registerRoutes(): void
    {
        Route::group($this->routeApiConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        });

        Route::group($this->routeWebConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }

    /**
     * Get the Knowledge Base [api] route group configuration array.
     */
    protected function routeApiConfiguration(): array
    {
        return [
            'namespace'  => 'DigitalEquation\KnowledgeBase\Http\Controllers\API',
            'domain'     => config('knowledge-base.route_group.api.domain', null),
            'as'         => config('knowledge-base.route_group.api.as', 'api.'),
            'prefix'     => config('knowledge-base.route_group.api.prefix', 'api'),
            'middleware' => config('knowledge-base.route_group.api.middleware', ['api', 'auth:api']),
        ];
    }

    /**
     * Get the Knowledge Base [web] route group configuration array.
     */
    protected function routeWebConfiguration(): array
    {
        return [
            'namespace'  => 'DigitalEquation\KnowledgeBase\Http\Controllers',
            'domain'     => config('knowledge-base.route_group.web.domain', null),
            'as'         => config('knowledge-base.route_group.web.as', null),
            'prefix'     => config('knowledge-base.route_group.web.prefix', '/'),
            'middleware' => config('knowledge-base.route_group.web.middleware', 'web'),
        ];
    }

    /**
     * Register the package artisan commands.
     */
    private function registerCommands(): void
    {
        $this->commands([
            ConfigCommand::class,
            InstallCommand::class,
        ]);
    }

    /**
     * Register the package's publishable resources.
     */
    private function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/knowledge-base.php' => config_path('knowledge-base.php'),
            ], 'knowledge-base-config');
        }
    }
}
