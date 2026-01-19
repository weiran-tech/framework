<?php

declare(strict_types = 1);

namespace Weiran\Framework\Events;

use Illuminate\Support\Collection;
use Weiran\Framework\Application\Event;

/**
 * 启用一个模块
 */
class WeiranEnabledEvent extends Event
{
    public function __construct(
        public readonly Collection $module
    ) {}
}
