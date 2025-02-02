<?php

declare(strict_types = 1);

namespace Weiran\Framework\Foundation\Http;

use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;
use Illuminate\Foundation\Http\Middleware\TrimStrings as IlluminateTrimStrings;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize as IlluminateValidatePostSize;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Weiran\Framework\Http\Middlewares\EnableCrossRequest;
use Weiran\Framework\Http\Middlewares\EncryptCookies;
use Weiran\Framework\Http\Middlewares\VerifyCsrfToken;

/**
 * poppy http kernel
 */
class Kernel extends HttpKernel
{

    /**
     * The application's global HTTP middleware stack.
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        PreventRequestsDuringMaintenance::class,
        IlluminateValidatePostSize::class,
        IlluminateTrimStrings::class,
    ];

    /**
     * The application's route middleware.
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        // 'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        // 'can' => \Illuminate\Auth\Middleware\Authorize::class,
        // 'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle'   => ThrottleRequests::class,
        'cross'      => EnableCrossRequest::class,
        'csrf_token' => VerifyCsrfToken::class,
    ];

    /**
     * The application's route middleware groups.
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            StartSession::class,
            ShareErrorsFromSession::class,
            SubstituteBindings::class,
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
        ],
        'api' => [
            'throttle:api',
            SubstituteBindings::class,
        ],
    ];
}