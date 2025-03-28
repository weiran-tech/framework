<?php

declare(strict_types = 1);

namespace Weiran\Framework\Events;

use Illuminate\Support\Collection;
use Weiran\Framework\Application\Event;

/**
 * WeiranOptimized
 */
class WeiranOptimized extends Event
{
    /**
     * Optimized module collection
     * @var Collection $modules
     */
    private $modules;

    /**
     * WeiranOptimized constructor.
     * @param Collection $modules
     */
    public function __construct(Collection $modules)
    {
        $this->modules = $modules;
    }

    /**
     * @return Collection
     */
    public function modules()
    {
        return $this->modules;
    }
}