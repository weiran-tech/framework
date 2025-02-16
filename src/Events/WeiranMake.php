<?php

declare(strict_types = 1);

namespace Weiran\Framework\Events;

use Weiran\Framework\Application\Event;

/**
 * WeiranMake
 */
class WeiranMake extends Event
{
    /**
     * @var string
     */
    public $slug;

    /**
     * WeiranMake constructor.
     * @param string $slug slug
     */
    public function __construct(string $slug)
    {
        $this->slug = $slug;
    }
}