<?php

declare(strict_types = 1);

namespace Weiran\Framework\Foundation;

use Closure;
use Illuminate\Foundation\Application as ApplicationBase;
use Throwable;

/**
 * poppy Application
 */
class Application extends ApplicationBase
{
    /**
     * 请求执行上下文
     */
    protected string $executionContext = '';

    /**
     * namespace
     * @var string
     */
    protected $namespace = 'app';

    /**
     * register "matched" event
     * @param Closure $callback callback
     * @return void
     */
    public function routeMatched(Closure $callback)
    {
        $this['router']->matched($callback);
    }


    /**
     * 检测运行上下文
     * @return bool
     */
    public function runningInBackend(): bool
    {
        return $this->executionContext === 'backend';
    }

    /**
     * 检测运行环境
     * @param string $context context
     * @return mixed
     */
    public function isRunningIn(string $context): bool
    {
        return $this->executionContext === $context;
    }

    /**
     * 设置运行上下文
     * @param string $context
     * @return void
     */
    public function setExecutionContext(string $context)
    {
        $this->executionContext = $context;
    }

    /**
     * 检测数据库是否链接
     * @return bool
     */
    public function hasDatabase(): bool
    {
        try {
            $this['db.connection']->getPdo();
        } catch (Throwable $ex) {
            return false;
        }

        return true;
    }

    /**
     * Get application installation status.
     * @return bool
     */
    public function isInstalled(): bool
    {
        if ($this->bound('installed')) {
            return true;
        }
        if (!file_exists($this->storagePath() . DIRECTORY_SEPARATOR . 'installed')) {
            return false;
        }
        $this->instance('installed', true);

        return true;
    }

    /**
     * Get cached config path.
     * @return string
     */
    public function getCachedConfigPath(): string
    {
        return $this['path.storage'] . '/framework/config.php';
    }


    /**
     * @inheritDoc
     */
    public function databasePath($path = ''): string
    {
        return $this->resourcePath('database' . ($path ? DIRECTORY_SEPARATOR . $path : $path));
    }

    /*
    |--------------------------------------------------------------------------
    | Laravel framework Config Path
    |--------------------------------------------------------------------------
    */

    /**
     * Get cached routes path.
     * @return string
     */
    public function getCachedRoutesPath(): string
    {
        return $this->storagePath() . '/framework/routes-v7.php';
    }

    /**
     * Get cached packages path.
     * @return string
     */
    public function getCachedPackagesPath(): string
    {
        return $this->storagePath() . '/framework/packages.php';
    }

    /**
     * Get cached services file path.
     * @return string
     */
    public function getCachedServicesPath(): string
    {
        return $this->storagePath() . '/framework/services.php';
    }

    /**
     * Get the path to the bootstrap directory.
     * @param string $path Optionally, a path to append to the bootstrap path
     * @return string
     */
    public function bootstrapPath($path = ''): string
    {
        return $this->storagePath() . '/bootstrap' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the path to the cached packages.php file.
     * @return string
     */
    public function getCachedClassesPath(): string
    {
        return $this->storagePath() . '/framework/classes.php';
    }

    /**
     * Get weiran framework path or assigned path.
     * @param string $path path
     * @return string
     */
    public function frameworkPath(string $path = ''): string
    {
        return dirname(__FILE__, 3) . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /*
    |--------------------------------------------------------------------------
    | Weiran framework Config Path
    |--------------------------------------------------------------------------
    */

    /**
     * Get weiran module path.
     * @return string
     */
    public function modulePath(): string
    {
        return $this->basePath('modules');
    }

    /**
     * 绑定路径到 container
     * @return void
     */
    protected function bindPathsInContainer()
    {
        parent::bindPathsInContainer();

        $this->instance('path.framework', $this->frameworkPath());
        $this->instance('path.poppy', dirname($this->frameworkPath()));
        $this->instance('path.module', $this->modulePath());
    }
}
