<?php

declare(strict_types = 1);

namespace Weiran\Framework\Http\Pagination;

use Request;

/**
 * 分页信息
 */
class PageInfo
{
    /**
     * @var int 页码
     */
    private int $page;

    /**
     * @var int 每页的分页数
     */
    private int $size;

    /**
     * 分页构造器
     *
     * @param array $page_info 分页信息
     */
    public function __construct(array $page_info)
    {
        $sizeConfig = abs(config('weiran.framework.page_size')) ?: 15;
        $page       = abs((int) ($page_info['page'] ?? 1));
        $size       = abs((int) ($page_info['size'] ?? $sizeConfig));
        $this->page = $page ?: 1;
        $this->size = $size ?: $sizeConfig;
    }

    /**
     * 返回分页的大小
     */
    public static function pagesize(): int
    {
        // pagesize
        $size        = (int) config('weiran.framework.page_size', 15);
        $maxPagesize = (int) config('weiran.framework.page_max');
        if (Request::input('pagesize')) {
            $pagesize = abs((int) Request::input('pagesize'));
            $pagesize = ($pagesize <= $maxPagesize) ? $pagesize : $maxPagesize;
            if ($pagesize > 0) {
                $size = $pagesize;
            }
        }

        return (int) $size;
    }

    /**
     * 分页大小
     */
    public function size(): int
    {
        return $this->size;
    }

    /**
     * 页码
     */
    public function page(): int
    {
        return $this->page;
    }
}
