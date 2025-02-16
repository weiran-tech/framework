<?php

declare(strict_types = 1);

namespace Weiran\Framework\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Weiran\Framework\Classes\Traits\MigrationTrait;
use Weiran\Framework\Events\WeiranMigrated;
use Weiran\Framework\Weiran\Weiran;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Weiran Migrate
 */
class WeiranMigrateCommand extends Command
{
    use ConfirmableTrait, MigrationTrait;

    /**
     * The console command name.
     * @var string
     */
    protected $name = 'weiran:migrate';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Run the database migrations for a specific or all modules';

    /**
     * @var Weiran
     */
    protected Weiran $weiran;

    /**
     * @var Migrator
     */
    protected Migrator $migrator;

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
        $this->prepareDatabase();

        if (!empty($this->argument('slug'))) {
            /** @var Collection $module */
            $module = $this->weiran->where('slug', $this->argument('slug'));

            if (!$module->count()) {
                $this->error('Module `' . $this->argument('slug') . '` not found, module need add `module.` prefix');
                return null;
            }

            if ($this->weiran->isEnabled($module['slug'])) {
                $this->migrate($module['slug']);

                return null;
            }

            if ($this->option('force')) {
                $this->migrate($module['slug']);

                return null;
            }

            $this->error('Nothing to migrate.');

            return 0;
        }

        if ($this->option('force')) {
            $modules = $this->weiran->all();
        }
        else {
            $modules = $this->weiran->enabled();
        }

        foreach ($modules as $module) {
            $this->migrate($module['slug']);
        }

        return 0;
    }

    /**
     * Run migrations for the specified module.
     * @param string $slug slug
     * @return null
     */
    protected function migrate(string $slug)
    {
        if ($this->weiran->exists($slug)) {
            $module  = $this->weiran->where('slug', $slug);
            $pretend = Arr::get($this->option(), 'pretend', false);
            $step    = Arr::get($this->option(), 'step', false);
            $path    = $this->getMigrationPath($slug);

            $this->migrator->setOutput($this->output)->run(
                $path, [
                'pretend' => $pretend,
                'step'    => $step,
            ]);

            event(new WeiranMigrated($module, $this->option()));

            // Once the migrator has run we will grab the note output and send it out to
            // the console screen, since the migrator itself functions without having
            // any instances of the OutputInterface contract passed into the class.
            foreach ($this->migrator->setOutput($this->output) as $note) {
                if (!$this->option('quiet')) {
                    $this->line($note);
                }
            }

            // Finally, if the "seed" option has been given, we will re-run the database
            // seed task to re-populate the database, which is convenient when adding
            // a migration and a seed at the same time, as it is only this command.
            if ($this->option('seed')) {
                $this->call('module:seed', ['module' => $slug, '--force' => true]);
            }
        }
        else {
            $this->error('Module does not exist.');

            return null;
        }

        return null;
    }

    /**
     * Prepare the migration database for running.
     */
    protected function prepareDatabase()
    {
        $this->migrator->setConnection($this->option('database'));

        if (!$this->migrator->repositoryExists()) {
            $options = ['--database' => $this->option('database')];

            $this->call('migrate:install', $options);
        }
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
            ['step', null, InputOption::VALUE_NONE, 'Force the migrations to be run so they can be rolled back individually.'],
        ];
    }
}
