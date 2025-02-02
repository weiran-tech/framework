<?php

declare(strict_types = 1);

namespace Weiran\Framework\Console\Commands;

use Illuminate\Console\Command;

/**
 * Poppy Optimize
 */
class PoppyOptimizeCommand extends Command
{
    /**
     * The console command name.
     * @var string
     */
    protected $name = 'poppy:optimize';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Optimize the module cache for better performance';

    /**
     * Execute the console command.
     */
    public function handle():void
    {
        $this->info('Generating optimized module cache');

        $this->laravel['poppy']->optimize();

        event('poppy.optimized', [$this->laravel['poppy']->all()]);
    }
}
