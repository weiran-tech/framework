<?php

declare(strict_types = 1);

namespace Weiran\Framework\Http;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

/**
 * BladeServiceProvider
 */
class BladeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     * @return void
     */
    public function register()
    {
        $this->app->afterResolving('blade.compiler', function (BladeCompiler $bladeCompiler) {
            // @poppy($slug)
            $bladeCompiler->directive('weiran', function ($slug) {
                return "<?php if(app('weiran')->exists({$slug}) && app('weiran')->isEnabled({$slug})): ?>";
            });

            $bladeCompiler->directive('endweiran', function () {
                return '<?php endif; ?>';
            });
        });
    }
}