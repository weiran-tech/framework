<?php

declare(strict_types = 1);

namespace Weiran\Framework\Console;

use Illuminate\Console\GeneratorCommand as LaravelGeneratorCommand;
use Illuminate\Support\Str;
use Weiran\Framework\Exceptions\ModuleNotFoundException;

/**
 * Poppy Generator Command
 */
abstract class GeneratorCommand extends LaravelGeneratorCommand
{
    /**
     * Parse the name and format according to the root namespace.
     * @param string $name name
     * @return string
     */
    protected function parseName(string $name): string
    {
        $rootNamespace = '';
        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        if (Str::contains($name, '/')) {
            $name = str_replace('/', '\\', $name);
        }

        return $this->parseName($this->getDefaultNamespace(trim($rootNamespace, '\\')) . '\\' . $name);
    }

    /**
     * Parse the class name and format according to the root namespace.
     * @param string $name classname
     * @return string
     */
    protected function qualifyClass($name): string
    {
        $name = Str::studly(ltrim($name, '\\/'));

        $name = str_replace('/', '\\', $name);

        return $this->getDefaultNamespace('') . '\\' . $name;
    }

    /**
     * @inheritDoc
     */
    protected function getPath($name): string
    {
        $slug = $this->argument('slug');

        if (!$this->laravel['weiran']->exists($slug)) {
            throw new ModuleNotFoundException($slug);
        }

        // take everything after the module name in the given path (ignoring case)
        if ($this->laravel['weiran']->isPoppy($slug)) {
            $trimSlug = Str::after($slug, 'weiran.');
            [, $mid] = explode('\\', $name);
            $midAfter  = str_replace('_', '-', Str::snake($mid));
            $lowerName = strtolower(str_replace($mid, $midAfter, $name));
        }
        else {
            $trimSlug  = Str::after($slug, 'module.');
            $lowerName = strtolower($name);
        }

        $key = array_search(strtolower($trimSlug), explode('\\', $lowerName));
        if ($key === false) {
            $newPath = str_replace('\\', '/', $name);
        }
        else {
            $newPath = implode('/', array_slice(explode('\\', $name), $key + 1));
        }

        $newPath = "{$newPath}.php";

        $pathInfo = pathinfo($newPath);

        if (strpos($pathInfo['dirname'], 'Tests') !== false) {
            $addSrc = '';
        }
        else {
            $addSrc = 'src' . DIRECTORY_SEPARATOR;
        }

        $dirs      = explode('/', $pathInfo['dirname']);
        $lowerDirs = array_map(function ($item) use ($slug) {
            return Str::studly($item);
        }, $dirs);
        $dirname   = implode('/', $lowerDirs);
        return weiran_path(
            $slug,
            $addSrc .
            $dirname . DIRECTORY_SEPARATOR .
            $pathInfo['basename']
        );
    }

    /**
     * Replace the namespace for the given stub.
     * @param string $stub stub file
     * @param string $name name
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name): self
    {
        $stub = str_replace(
            ['DummyNamespace'],
            [$this->getNamespace($name)],
            $stub
        );

        return $this;
    }
}
