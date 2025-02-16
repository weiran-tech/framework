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
    public function register(): void
    {
        $this->app->singleton('weiran.yaml', fn() => new Yaml());
    }

    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            'weiran.yaml',
        ];
    }
}
