<?php

declare(strict_types = 1);

namespace Weiran\Framework\Console\Generators;

use Illuminate\Console\Command;
use Weiran\Framework\Classes\Traits\MigrationTrait;

/**
 * Make Migration
 */
class MakeMigrationCommand extends Command
{

    use MigrationTrait;

    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'weiran:migration
    	{slug : The slug of the module.}
    	{name : The name of the migration.}
    	{--create= : The table to be created.}
        {--table= : The table to migrate.}
    ';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Create a new module migration file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $arguments = $this->argument();
        $option    = $this->option();
        $options   = [];

        array_walk($option, function ($value, $key) use (&$options) {
            $options['--' . $key] = $value;
        });

        unset($arguments['slug']);

        $options['--path'] = str_replace(
            realpath(base_path()),
            '',
            $this->getMigrationPath($this->argument('slug'))
        );
        $options['--path'] = ltrim($options['--path'], '/');

        if (!app('files')->exists(base_path($options['--path']))) {
            $this->error('Path `' . $options['--path'] . '` not exists');
            return 1;
        }

        $this->call('make:migration', array_merge($arguments, $options));

        return 0;
    }
}
