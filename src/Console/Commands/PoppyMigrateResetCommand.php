<?php

declare(strict_types = 1);

namespace Weiran\Framework\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Weiran\Framework\Classes\Traits\MigrationTrait;
use Weiran\Framework\Events\PoppyMigrateReset;
use Weiran\Framework\Weiran\Weiran;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Poppy Migrate Reset
 */
class PoppyMigrateResetCommand extends Command
{
    use ConfirmableTrait, MigrationTrait;

    /**
     * The console command name.
     * @var string
     */
    protected $name = 'poppy:migrate:reset';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Rollback all database migrations for a specific or all modules';

    /**
     * @var Weiran
     */
    protected Weiran $poppy;

    /**
     * @var Migrator
     */
    protected Migrator $migrator;

    /**
     * @var Filesystem
     */
    protected Filesystem $files;

    /**
     * Create a new command instance.
     * @param Weiran     $poppy
     * @param Filesystem $files
     * @param Migrator   $migrator
     */
    public function __construct(Weiran $poppy, Filesystem $files, Migrator $migrator)
    {
        parent::__construct();

        $this->poppy    = $poppy;
        $this->files    = $files;
        $this->migrator = $migrator;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->confirmToProceed()) {
            return 1;
        }

        $this->reset();

        return 0;
    }

    /**
     * Run the migration reset for the current list of slugs.
     * Migrations should be reset in the reverse order that they were
     * migrated up as. This ensures the database is properly reversed
     * without conflict.
     */
    protected function reset()
    {
        $this->migrator->setconnection($this->input->getOption('database'));

        $files = $this->migrator->getMigrationFiles($this->getMigrationPaths());

        $migrations = array_reverse($this->migrator->getRepository()->getRan());

        if (count($migrations) == 0) {
            $this->output->writeln('Nothing to rollback.');
        }
        else {
            $this->migrator->requireFiles($files);

            $count = 0;
            foreach ($migrations as $migration) {
                if (!array_key_exists($migration, $files)) {
                    $count++;

                    continue;
                }

                $this->runDown($files[$migration], (object) ['migration' => $migration]);
            }

            if ($count === count($migrations)) {
                $this->output->writeln(count($migrations) . ' has already rolled back, nothing changed!');
            }
        }
    }

    /**
     * Run "down" a migration instance.
     * @param string        $file      migrate file
     * @param string|object $migration migration file
     */
    protected function runDown(string $file, $migration)
    {
        $file     = $this->migrator->getMigrationName($file);
        $instance = $this->migrator->resolve($file);

        $instance->down();

        $this->migrator->getRepository()->delete($migration);

        $this->info('RolledBack: ' . $file);
    }

    /**
     * Generate a list of all migration paths, given the arguments/operations supplied.
     * @return array
     */
    protected function getMigrationPaths(): array
    {
        $migrationPaths = [];

        foreach ($this->getSlugsToReset() as $slug) {
            $migrationPaths[] = $this->getMigrationPath($slug);

            event(new PoppyMigrateReset($this->poppy, $this->option()));
        }

        return $migrationPaths;
    }

    /**
     * Using the arguments, generate a list of slugs to reset the migrations for.
     * @return Collection|array
     */
    protected function getSlugsToReset()
    {
        if ($this->validSlugProvided()) {
            return [$this->argument('slug')];
        }

        if ($this->option('force')) {
            return $this->poppy->all()->pluck('slug');
        }

        return $this->poppy->enabled()->pluck('slug');
    }

    /**
     * Determine if a valid slug has been provided as an argument.
     * We will accept a slug as long as it is not empty and is enabled (or force is passed).
     * @return bool
     */
    protected function validSlugProvided(): bool
    {
        if (empty($this->argument('slug'))) {
            return false;
        }

        if ($this->poppy->isEnabled($this->argument('slug'))) {
            return true;
        }

        if ($this->option('force')) {
            return true;
        }

        return false;
    }

    /**
     * Get the console command parameters.
     * @param string $slug slug
     * @return array
     */
    protected function getParameters(string $slug): array
    {
        $params = [];

        $params['--path'] = $this->getMigrationPath($slug);

        if ($option = $this->option('database')) {
            $params['--database'] = $option;
        }

        if ($option = $this->option('pretend')) {
            $params['--pretend'] = $option;
        }

        if ($option = $this->option('seed')) {
            $params['--seed'] = $option;
        }

        return $params;
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
            ['pretend', null, InputOption::VALUE_OPTIONAL, 'Dump the SQL queries that would be run.'],
            ['seed', null, InputOption::VALUE_OPTIONAL, 'Indicates if the seed task should be re-run.'],
        ];
    }
}
