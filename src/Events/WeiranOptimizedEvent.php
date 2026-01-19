<?php

declare(strict_types = 1);

namespace Weiran\Framework\Events;

use Illuminate\Support\Collection;
use Weiran\Framework\Application\Event;

/**
 * WeiranOptimizedEvent
 */
class WeiranOptimizedEvent extends Event
{
    /**
     * WeiranOptimizedEvent constructor.
     */
    public function __construct(
        public readonly Collection $modules
    ) {}

    /**
     * @return Collection
     *
     * @deprecated 1.0 使用 readonly 属性
     */
    public function modules()
    {
        return $this->modules;
    }
}
