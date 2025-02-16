<?php

declare(strict_types = 1);

namespace Weiran\Framework\Parse;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

/**
 * ParseServiceProvider
 */
class ParseServiceProvider extends ServiceProvider implements DeferrableProvider
{

    /**
     * Register the service provider.
     * @return void
     */
    public function register()
    {
        $this->app->singleton('weiran.yaml', function ($app) {
            return new Yaml();
        });

        $this->app->singleton('weiran.ini', function ($app) {
            return new Ini();
        });

        $this->app->singleton('weiran.xml', function ($app) {
            return new Xml();
        });
    }

    /**
     * @inheritDoc
     */
    public function provides()
    {
        return [
            'weiran.yaml',
            'weiran.ini',
            'weiran.xml',
        ];
    }
}
