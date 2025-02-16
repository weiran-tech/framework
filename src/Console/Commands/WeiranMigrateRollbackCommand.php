<?php

declare(strict_types = 1);

namespace Weiran\Framework\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Database\Migrations\Migrator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Weiran\Framework\Classes\Traits\MigrationTrait;
use Weiran\Framework\Weiran\Weiran;

/**
 * Weiran Migrate Rollback
 */
class WeiranMigrateRollbackCommand extends Command
{
    use MigrationTrait, ConfirmableTrait;

    /**
     * The console command name.
     * @var string
     */
    protected $name = 'weiran:migrate:rollback';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Rollback the last database migrations for a specific or all modules';

    /**
     * The migrator instance.
     * @var Migrator
     */
    protected Migrator $migrator;

    /**
     * @var Weiran
     */
    protected Weiran $weiran;

    /**
     * Create a new command instance.
     * @param Migrator $migrator
     * @param Weiran   $weiran
     */
    public function __construct(Migrator $migrator, Weiran $weiran)
    {
        parent::__construct();

        $this->migrator = $migrator;
        $this->weiran   = $weiran;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->confirmToProceed()) {
            return 1;
        }

        $this->migrator->setConnection($this->option('database'));

        $this->migrator->setOutput($this->output)->rollback(
            $this->getMigrationPaths(), [
                'pretend' => $this->option('pretend'),
                'step'    => (int) $this->option('step'),
            ]
        );

        foreach ($this->migrator->setOutput($this->output) as $note) {
            $this->output->writeln($note);
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
            ['step', null, InputOption::VALUE_OPTIONAL, 'The number of migrations to be reverted.'],
        ];
    }

    /**
     * Get all the migration paths.
     * @return array
     */
    protected function getMigrationPaths(): array
    {
        $slug  = $this->argument('slug');
        $paths = [];

        if ($slug) {
            $paths[] = $this->getMigrationPath($slug);
        }
        else {
            foreach ($this->weiran->slugs() as $module) {
                $paths[] = $this->getMigrationPath($module);
            }
        }

        return $paths;
    }
}
