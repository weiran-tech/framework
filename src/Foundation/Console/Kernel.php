<?php

declare(strict_types = 1);

namespace Weiran\Framework\Foundation\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Weiran\Framework\Events\PoppySchedule;

/**
 * poppy console kernel
 */
class Kernel extends ConsoleKernel
{
    /**
     * 定义计划命令
     * @param Schedule $schedule schedule
     */
    protected function schedule(Schedule $schedule): void
    {
        $this->app['events']->dispatch(PoppySchedule::class, [$schedule]);
    }
}