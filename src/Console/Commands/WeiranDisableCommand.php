<?php

declare(strict_types = 1);

namespace Weiran\Framework\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Weiran\Framework\Events\WeiranDisabled;

/**
 * Weiran Disable
 */
class WeiranDisableCommand extends Command
{
    /**
     * The console command name.
     * @var string
     */
    protected $name = 'weiran:disable';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Disable a module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $slug = $this->argument('slug');

        if ($this->laravel['weiran']->isEnabled($slug)) {
            $this->laravel['weiran']->disable($slug);

            $module = $this->laravel['weiran']->where('slug', $slug);

            event(new WeiranDisabled($module));

            $this->info('Module was disabled successfully.');
        }
        else {
            $this->comment('Module is already disabled.');
        }

        return 0;
    }

    /**
     * Get the console command arguments.
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['slug', InputArgument::REQUIRED, 'Module slug.'],
        ];
    }
}
