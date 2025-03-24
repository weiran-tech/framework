<?php

declare(strict_types = 1);

namespace Weiran\Framework\Foundation;

use Illuminate\Foundation\Application as ApplicationBase;
use Throwable;

/**
 * Weiran Application
 */
class Application extends ApplicationBase
{
    /**
     * 请求执行上下文
     */
    protected string $executionContext = '';


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
    public function setExecutionContext(string $context): void
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
        } catch (Throwable) {
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


    public function getCachedRoutesPath(): string
    {
        return $this->storagePath() . '/framework/routes.php';
    }

    public function getCachedPackagesPath(): string
    {
        return $this->storagePath() . '/framework/packages.php';
    }

    public function getCachedServicesPath(): string
    {
        return $this->storagePath() . '/framework/services.php';
    }

    public function bootstrapPath($path = ''): string
    {
        return $this->basePath() . '/bootstrap' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the path to the cached classes.php file.
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
    protected function bindPathsInContainer(): void
    {
        parent::bindPathsInContainer();

        $this->instance('path.framework', $this->frameworkPath());
        $this->instance('path.weiran', dirname($this->frameworkPath()));
        $this->instance('path.module', $this->modulePath());
    }
}
