<?php

declare(strict_types = 1);

namespace Weiran\Framework\Events;

use Weiran\Framework\Application\Event;

/**
 * PoppyMake
 */
class PoppyMake extends Event
{
    /**
     * @var string
     */
    public $slug;

    /**
     * PoppyMake constructor.
     * @param string $slug slug
     */
    public function __construct(string $slug)
    {
        $this->slug = $slug;
    }
}