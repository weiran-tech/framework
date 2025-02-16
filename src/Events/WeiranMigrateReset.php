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
    public $poppy;

    /**
     * @var array|mixed
     */
    private $option;

    /**
     * @param Weiran $poppy
     * @param array  $option
     */
    public function __construct(Weiran $poppy, $option = [])
    {
        $this->poppy  = $poppy;
        $this->option = $option;
    }
}