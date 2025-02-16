<?php

declare(strict_types = 1);

namespace Weiran\Framework\Events;

use Weiran\Framework\Application\Event;
use Weiran\Framework\Weiran\Weiran;

/**
 * Migrate Refresh
 */
class WeiranMigrateReset extends Event
{

    /**
     * @var Weiran 模块
     */
    public $weiran;

    /**
     * @var array|mixed
     */
    private $option;

    /**
     * @param Weiran $weiran
     * @param array  $option
     */
    public function __construct(Weiran $weiran, $option = [])
    {
        $this->weiran = $weiran;
        $this->option = $option;
    }
}