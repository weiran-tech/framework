<?php

declare(strict_types = 1);

namespace Weiran\Framework\Poppy\Abstracts;

use Exception;
use Illuminate\Config\Repository as Config;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Weiran\Framework\Exceptions\ApplicationException;
use Weiran\Framework\Poppy\Contracts\Repository as RepositoryContract;

/**
 * Repository
 */
abstract class Repository implements RepositoryContract
{
    /**
     * @var Config
     */
    protected Config $config;

    /**
     * @var Filesystem
     */
    protected Filesystem $files;

    /**
     * @var string Path to the defined modules directory
     */
    protected string $path;

    /**
     * Constructor method.
     * @param Config     $config config
     * @param Filesystem $files  files
     */
    public function __construct(Config $config, Filesystem $files)
    {
        $this->config = $config;
        $this->files  = $files;
        $this->path   = app('path.module');
    }

    /**
     * Get a module's manifest contents.
     * @param string $slug slug
     * @return Collection
     * @throws Exception
     */
    public function getManifest(string $slug): Collection
    {
        $path     = $this->getManifestPath($slug);
        $contents = $this->files->get($path);
        @json_decode($contents, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return collect(json_decode($contents, true));
        }
        throw new ApplicationException(
            '[' . $slug . '] Your JSON manifest file was not properly formatted. ' .
            'Check for formatting issues and try again.'
        );
    }

    /**
     * Get modules path.
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Get path of module manifest file.
     * @param $slug
     * @return string
     */
    protected function getManifestPath($slug): string
    {
        return $this->getModulePath($slug) . '/manifest.json';
    }

    /**
     * 获取所有模块的基本名称
     * Get all module base names.
     * module.{mod}, poppy.{mod}
     * @return Collection
     */
    protected function getAllBaseNames(): Collection
    {
        try {
            $collection = collect($this->files->directories(app('path.module')));

            $baseNames = $collection->map(function ($item) {
                return 'module.' . basename($item);
            });

            // poppy path
            $collection = collect($this->files->directories(app('path.poppy')));
            $collection->each(function ($item) use ($baseNames) {
                if ($this->files->exists($item . '/manifest.json')) {
                    $baseNames->push('poppy.' . basename($item));
                }
            });
            return $baseNames;
        } catch (InvalidArgumentException $e) {
            return collect([]);
        }
    }

    /**
     * Get path for the specified module.
     * @param string $slug
     * @return string
     */
    private function getModulePath(string $slug): string
    {
        $type   = Str::before($slug, '.');
        $module = Str::after($slug, '.');
        if ($type === 'poppy') {
            return home_path($module);
        }
        $modulePath = app('path.module');
        return $modulePath . "/{$module}";
    }
}
