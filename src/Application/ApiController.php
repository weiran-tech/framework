<?php

declare(strict_types = 1);

namespace Weiran\Framework\Application;

use Illuminate\Container\Container;

/**
 * Api Controller
 */
class ApiController extends Controller
{
    /**
     * ApiController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        Container::getInstance()->setExecutionContext('api');
    }
}