<?php

declare(strict_types = 1);

namespace Weiran\Framework\Application;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

abstract class RouteServiceProvider extends ServiceProvider
{

    /**
     * 前缀
     * @var string
     */
    protected $prefix;


    public function __construct($app)
    {
        parent::__construct($app);
        $this->prefix = config('weiran.framework.prefix') ?: 'mgr-page';
    }
}