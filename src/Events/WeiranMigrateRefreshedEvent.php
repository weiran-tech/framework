<?php

declare(strict_types = 1);

namespace Weiran\Framework\Events;

use Illuminate\Support\Collection;
use Weiran\Framework\Application\Event;

/**
 * Migrate Refresh
 */
class WeiranMigrateRefreshedEvent extends Event
{

    public function __construct(
        public readonly Collection $module,
        public readonly array      $option = []
    ) {}
}
