<?php

declare(strict_types = 1);

namespace Weiran\Framework\Weiran;

use Illuminate\Support\ServiceProvider;
use Weiran\Framework\Weiran\Contracts\Repository;

/**
 * Module manager
 */
class WeiranServiceProvider extends ServiceProvider
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

            return new Weiran($app, $repository);
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