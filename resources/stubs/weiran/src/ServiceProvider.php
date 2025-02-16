<?php

declare(strict_types = 1);

namespace DummyNamespace;

use DummyNamespace\Http\RouteServiceProvider;
use Weiran\Framework\Exceptions\ModuleNotFoundException;
use Weiran\Framework\Support\WeiranServiceProvider;

class ServiceProvider extends WeiranServiceProvider
{

    /**
     * Bootstrap the module services.
     * @return void
     * @throws ModuleNotFoundException
     */
    public function boot(): void
    {
        parent::boot('DummySlug');
    }

    /**
     * Register the module services.
     * @return void
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }
}
