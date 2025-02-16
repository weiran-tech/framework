<?php

declare(strict_types = 1);

namespace Weiran\Framework\Application;

use Carbon\Carbon;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Weiran\Framework\Helper\EnvHelper;
use Weiran\Framework\Http\Pagination\PageInfo;

/**
 * weiran controller
 */
abstract class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;

    /**
     * @var array 权限(中间件中可以读取, 使用 public 模式)
     */
    public static array $permission = [];

    /**
     * pagesize
     * @var int $pagesize
     */
    protected int $pagesize = 15;

    /**
     * ip
     * @var string $ip
     */
    protected string $ip;

    /**
     * now
     * @var Carbon $now
     */
    protected Carbon $now;

    /**
     * route
     * @var string|null $route
     */
    protected ?string $route;

    /**
     * title
     * @var string|null $title
     */
    protected ?string $title;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->route    = (string) Route::currentRouteName();
        $this->pagesize = PageInfo::pagesize();
        $this->ip       = EnvHelper::ip();
        $this->now      = Carbon::now();
    }

    protected function withViews(): void
    {
        View::share([
            '_ip'       => $this->ip,
            '_now'      => $this->now,
            '_pagesize' => $this->pagesize,
            '_route'    => $this->route,
        ]);
        // 自动计算seo
        // 根据路由名称来转换 seo key
        // system:web.user.index  => system::web_nav_index
        $seoKey = str_replace([':', '.'], ['::', '_'], $this->route);
        if ($seoKey) {
            $seoKey = str_replace('::', '::seo.', $seoKey);
            $this->seo(trans($seoKey));
        }
        else {
            $this->seo();
        }
    }

    /**
     * seo
     * @param mixed ...$args args
     */
    protected function seo(...$args): void
    {
        [$title, $description] = parse_seo($args);
        $title       = $title ? $title . '-' . config('weiran.framework.title') : config('weiran.framework.title');
        $description = $description ?: config('weiran.framework.description');

        $this->title = $title;

        View::share([
            '_title'       => $title,
            '_description' => $description,
        ]);
    }
}