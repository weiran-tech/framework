<?php

declare(strict_types = 1);

namespace Weiran\Framework\Events;

use Weiran\Framework\Application\Event;
use Weiran\Framework\Weiran\Weiran;

/**
 * Migrate Refresh
 */
class WeiranMigrateResetEvent extends Event
{
    public function __construct(
        public readonly Weiran $weiran,
        public readonly array  $option = []
    ) {}
}
