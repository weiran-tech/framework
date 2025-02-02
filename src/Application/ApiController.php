<?php

declare(strict_types = 1);

namespace Weiran\Framework\Application;

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
        py_container()->setExecutionContext('api');
    }
}