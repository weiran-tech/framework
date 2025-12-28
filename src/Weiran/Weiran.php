<?php

declare(strict_types = 1);

namespace Weiran\Framework\Weiran;

use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Weiran\Framework\Weiran\Contracts\Repository;

/**
 * @method bool optimize()
 * @method all()
 * @method slugs()
 * @method where($key, $value)
 * @method sortBy($key)
 * @method sortByDesc($key)
 * @method exists($slug)
 * @method count()
 * @method getManifest($slug)
 * @method get($property, $default = null)
 * @method set($property, $value)
 * @method Collection enabled()
 * @method disabled()
 * @method isEnabled($slug)
 * @method isDisabled($slug)
 * @method isWeiran($slug)
 * @method enable(string $slug)
 * @method disable(string $slug)
 */
class Weiran
{
    protected Application $app;

    protected Repository $repository;

    /**
     * Create a new Weiran Modules instance.
     */
    public function __construct(Application $app, Repository $repository)
    {
        $this->app        = $app;
        $this->repository = $repository;
    }

    /**
     * Register the module service provider file from all modules.
     */
    public function register(): void
    {
        $modules = $this->repository->enabled();

        $modules->each(function ($module) {
            $this->registerServiceProvider($module);
        });
    }

    public function repository(): Repository
    {
        return $this->repository;
    }

    /**
     * magical method.
     *
     * @param mixed $arguments
     *
     * @return mixed
     */
    public function __call(string $method, $arguments)
    {
        return call_user_func_array([$this->repository, $method], $arguments);
    }

    /**
     * Register the module service provider.
     *
     * @param array $module module
     */
    private function registerServiceProvider(array $module): void
    {
        $serviceProvider = weiran_class($module['slug'], 'ServiceProvider');

        if (class_exists($serviceProvider)) {
            $this->app->register($serviceProvider);
        }
    }
}
