<?php

declare(strict_types = 1);

namespace Weiran\Framework\Events;

use Illuminate\Support\Collection;
use Weiran\Framework\Application\Event;

/**
 * 禁用一个模块
 */
class WeiranDisabledEvent extends Event
{
    public function __construct(
        public readonly Collection $module
    ) {}
}
