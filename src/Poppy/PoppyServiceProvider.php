<?php

declare(strict_types = 1);

namespace Weiran\Framework\Poppy;

use Illuminate\Support\ServiceProvider;
use Weiran\Framework\Poppy\Contracts\Repository;

/**
 * Module manager
 */
class PoppyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->bind(Repository::class, FileRepository::class);

        $this->app->singleton('poppy', function ($app) {
            $repository = $app->make(Repository::class);

            return new Poppy($app, $repository);
        });
    }

    /**
     * Get the services provided by the provider.
     * @return array
     */
    public function provides()
    {
        return ['poppy'];
    }
}