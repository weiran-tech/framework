<?php

declare(strict_types = 1);

namespace Weiran\Framework\Support\Abstracts;

use Illuminate\Support\Collection;

/**
 * Class Repository.
 */
abstract class Repository extends Collection
{
    /**
     * Initialize.
     * @param Collection $collection collection
     */
    abstract public function initialize(Collection $collection);
}