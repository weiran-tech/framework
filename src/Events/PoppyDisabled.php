<?php

declare(strict_types = 1);

namespace Weiran\Framework\Events;

use Illuminate\Support\Collection;
use Weiran\Framework\Application\Event;

/**
 * 禁用一个模块
 */
class PoppyDisabled extends Event
{

    /**
     * @var Collection 模块
     */
    public $module;

    /**
     * @param $module
     */
    public function __construct($module)
    {
        $this->module = $module;
    }
}