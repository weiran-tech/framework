<?php

declare(strict_types = 1);

namespace Weiran\Framework\Console\Commands;

use Illuminate\Console\Command;
use Weiran\Framework\Events\PoppyDisabled;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Poppy Disable
 */
class PoppyDisableCommand extends Command
{
    /**
     * The console command name.
     * @var string
     */
    protected $name = 'poppy:disable';

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

        if ($this->laravel['poppy']->isEnabled($slug)) {
            $this->laravel['poppy']->disable($slug);

            $module = $this->laravel['poppy']->where('slug', $slug);

            event(new PoppyDisabled($module));

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
