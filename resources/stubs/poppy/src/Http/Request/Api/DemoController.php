<?php

declare(strict_types = 1);

namespace DummyNamespace\Http\Request\Api;

use Weiran\Framework\Application\ApiController;
use Weiran\Framework\Classes\Resp;

class DemoController extends ApiController
{
    public function index()
    {
        return Resp::success('DummyNamespace Api Request Success');
    }
}