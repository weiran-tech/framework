<?php

namespace DummyNamespace\Http\Request\Web;

use Weiran\Framework\Application\Controller;

class DemoController extends Controller
{
    public function index(): string
    {
        return 'DummyNamespace Web Request Success';
    }
}