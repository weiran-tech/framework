<?php

declare(strict_types = 1);

namespace Weiran\Framework\Events;

use Illuminate\Support\Collection;
use Weiran\Framework\Application\Event;

/**
 * 集成完成
 */
class WeiranMigratedEvent extends Event
{
    public function __construct(
        public Collection $module,
        public array      $option = []
    ) {}
}
