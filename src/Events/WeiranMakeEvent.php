<?php

declare(strict_types = 1);

namespace Weiran\Framework\Events;

use Weiran\Framework\Application\Event;

/**
 * WeiranMakeEvent
 */
class WeiranMakeEvent extends Event
{
    /**
     * WeiranMakeEvent constructor.
     *
     * @param string $slug slug
     */
    public function __construct(
        public readonly string $slug
    ) {}
}
