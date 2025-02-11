<?php

declare(strict_types = 1);

namespace Weiran\Framework\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Weiran\Framework\Events\PoppyMigrateRefreshed;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Poppy Migrate Refresh
 */
class PoppyMigrateRefreshCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The console command name.
     * @var string
     */
    protected $name = 'poppy:migrate:refresh';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Reset and re-run all migrations for a specific or all modules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->confirmToProceed()) {
            return 1;
        }

        $slug = $this->argument('slug');

        $this->call('poppy:migrate:reset', [
            'slug'       => $slug,
            '--database' => $this->option('database'),
            '--force'    => $this->option('force'),
            '--pretend'  => $this->option('pretend'),
        ]);

        $this->call('poppy:migrate', [
            'slug'       => $slug,
            '--database' => $this->option('database'),
        ]);

        if ($this->needsSeeding()) {
            $this->runSeeder($slug, $this->option('database'));
        }

        if (isset($slug)) {
            $module = $this->laravel['weiran']->where('slug', $slug);

            event(new PoppyMigrateRefreshed($module, $this->option()));

            $this->info('Module has been refreshed.');
        }
        else {
            $this->info('All modules have been refreshed.');
        }

        return 0;
    }

    /**
     * Determine if the developer has requested database seeding.
     * @return bool
     */
    protected function needsSeeding(): bool
    {
        return $this->option('seed');
    }

    /**
     * Run the module seeder command.
     * @param string|null $slug     slug
     * @param string|null $database database
     */
    protected function runSeeder(string $slug = null, string $database = null)
    {
        $this->call('poppy:seed', [
            'slug'       => $slug,
            '--database' => $database,
        ]);
    }

    /**
     * Get the console command arguments.
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['slug', InputArgument::OPTIONAL, 'Module slug.'],
        ];
    }

    /**
     * Get the console command options.
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run while in production.'],
            ['pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'],
            ['seed', null, InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run.'],
        ];
    }
}
