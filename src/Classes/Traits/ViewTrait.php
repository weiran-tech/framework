<?php

declare(strict_types = 1);

namespace Weiran\Framework\Classes\Traits;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

/**
 * Trait Viewable.
 */
trait ViewTrait
{
    /**
     * Share variable with view.
     *
     * @param string|array $key   key
     * @param null         $value value
     */
    protected function share($key, $value = null)
    {
        app('view')->share($key, $value);
    }

    /**
     * Share variable with view.
     *
     * @param string $template  template
     * @param array  $data      data
     * @param array  $mergeData mergeData
     *
     * @return View
     * @throws BindingResolutionException
     */
    protected function view($template, array $data = [], $mergeData = [])
    {
        if (Str::contains($template, '::')) {
            return app('view')->make($template, $data, $mergeData);
        }

        return app('view')->make('theme::' . $template, $data, $mergeData);
    }
}