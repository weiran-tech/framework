<?php
declare(strict_types = 1);

/*
|--------------------------------------------------------------------------
| Demo
|--------------------------------------------------------------------------
|
*/

use DummyNamespace\Http\Request\Api\DemoController;

Route::group([
    'middleware' => ['cross'],
], function (Illuminate\Routing\Router $route) {
    $route->get('/', [DemoController::class, 'index']);
});