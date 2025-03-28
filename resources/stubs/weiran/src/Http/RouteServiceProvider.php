<?php

declare(strict_types = 1);

namespace DummyNamespace\Http;

use Route;
use Weiran\Framework\Application\RouteServiceProvider as WeiranFrameworkRouteServiceProvider;

class RouteServiceProvider extends WeiranFrameworkRouteServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     * @return void
     */
    public function boot()
    {
        $this->routes(function () {
            $this->mapWebRoutes();

            $this->mapApiRoutes();
        });
    }

    /**
     * Define the "web" routes for the module.
     * These routes all receive session state, CSRF protection, etc.
     * @return void
     */
    protected function mapWebRoutes(): void
    {
        Route::group([
            // todo auth
            'prefix' => 'DummySlug',
        ], function () {
            require_once __DIR__ . '/Routes/web.php';
        });

        Route::group([
            'prefix'     => $this->prefix . '/DummySlug',
            'middleware' => 'backend-auth',
        ], function () {
            require_once __DIR__ . '/Routes/backend.php';
        });
    }

    /**
     * Define the "api" routes for the module.
     * These routes are typically stateless.
     * @return void
     */
    protected function mapApiRoutes(): void
    {
        Route::group([
            // todo auth
            'prefix' => 'api/web/DummySlug/v1',
        ], function () {
            require_once __DIR__ . '/Routes/api_v1.php';
        });
    }
}
