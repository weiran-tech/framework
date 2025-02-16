<?php
declare(strict_types = 1);

/*
|--------------------------------------------------------------------------
| Backend Demo, 这里调用的是为后台进行服务的, 也就是管理界面
|--------------------------------------------------------------------------
|
*/

use DummyNamespace\Http\Request\Backend\DemoController;

Route::group([], function (Illuminate\Routing\Router $route) {
    $route->get('/', [DemoController::class, 'index']);;
});