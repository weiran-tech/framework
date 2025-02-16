<?php

declare(strict_types = 1);

namespace Weiran\Framework\Events;

use Illuminate\Support\Collection;
use Weiran\Framework\Application\Event;

/**
 * Migrate Refresh
 */
class WeiranMigrateRefreshed extends Event
{

    /**
     * @var Collection 模块
     */
    public $module;

    /**
     * @var array|mixed
     */
    private $option;

    /**
     * @param Collection $module
     * @param array      $option
     */
    public function __construct(Collection $module, $option = [])
    {
        $this->module = $module;
        $this->option = $option;
    }
}