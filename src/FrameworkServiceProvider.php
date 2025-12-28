<?php

declare(strict_types = 1);

namespace Weiran\Framework;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Weiran\Framework\Helper\TimeHelper;
use Weiran\Framework\Helper\UtilHelper;

/**
 * FrameworkServiceProvider
 */
class FrameworkServiceProvider extends ServiceProvider
{
    protected static bool $registered = false;

    /**
     * Bootstrap the application events.
     */
    public function boot(): void
    {
        // 注册 api 文档配置
        $this->publishes([
            framework_path('config/weiran.php') => config_path('weiran.php'),
        ], 'weiran');

        // framework register
        if (!self::$registered) {
            app('weiran')->register();
            self::$registered = true;
        }

        // views an lang
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'weiran');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'weiran');

        $this->bootValidation();

        // Carbon
        Carbon::setLocale(config('app.locale'));
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            framework_path('config/framework.php'),
            'weiran.framework'
        );

        $this->app->register(Console\ConsoleServiceProvider::class);
        $this->app->register(Console\GeneratorServiceProvider::class);
        $this->app->register(Http\BladeServiceProvider::class);
        $this->app->register(Weiran\WeiranServiceProvider::class);
        $this->app->register(Parse\ParseServiceProvider::class);
        $this->app->register(Translation\TranslationServiceProvider::class);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            'path.framework',
            'path.weiran',
            'path.module',
        ];
    }

    private function bootValidation(): void
    {
        app('validator')->extend('mobile', fn ($attribute, $value) => UtilHelper::isMobile($value));
        app('validator')->extend('json', fn ($attribute, $value) => UtilHelper::isJson($value));
        app('validator')->extend('date', fn ($attribute, $value) => UtilHelper::isDate($value));
        app('validator')->extend('chid', fn ($attribute, $value) => UtilHelper::isChId($value));
        app('validator')->extend('simple_pwd', fn ($attribute, $value) => UtilHelper::isPwd($value));
        app('validator')->extend('username', function ($attribute, $value, $parameters) {
            $first = Arr::first($parameters);

            return UtilHelper::isUsername($value, $first === 'sub');
        });
        app('validator')->extend('date_range', fn ($attribute, $value) => TimeHelper::isDateRange($value));
        app('validator')->extend('urls', function ($attribute, $value) {
            if (!is_array($value)) {
                return false;
            }
            foreach ($value as $val) {
                if (!UtilHelper::isUrl($val)) {
                    return false;
                }
            }

            return true;
        });
    }
}
