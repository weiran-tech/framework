<?php

declare(strict_types = 1);

namespace Weiran\Framework\Console;

use Illuminate\Support\ServiceProvider;
use Weiran\Framework\Console\Commands\WeiranDisableCommand;
use Weiran\Framework\Console\Commands\WeiranEnableCommand;
use Weiran\Framework\Console\Commands\WeiranListCommand;
use Weiran\Framework\Console\Commands\WeiranMigrateCommand;
use Weiran\Framework\Console\Commands\WeiranMigrateRefreshCommand;
use Weiran\Framework\Console\Commands\WeiranMigrateResetCommand;
use Weiran\Framework\Console\Commands\WeiranMigrateRollbackCommand;
use Weiran\Framework\Console\Commands\WeiranOptimizeCommand;
use Weiran\Framework\Console\Commands\WeiranSeedCommand;
use Weiran\Framework\Database\Migrations\Migrator;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->registerDisableCommand();
        $this->registerEnableCommand();
        $this->registerListCommand();
        $this->registerMigrateCommand();
        $this->registerMigrateRefreshCommand();
        $this->registerMigrateResetCommand();
        $this->registerMigrateRollbackCommand();
        $this->registerOptimizeCommand();
        $this->registerSeedCommand();
    }

    /**
     * Register the module:disable command.
     */
    protected function registerDisableCommand()
    {
        $this->app->singleton('command.weiran.disable', function () {
            return new WeiranDisableCommand();
        });

        $this->commands('command.weiran.disable');
    }

    /**
     * Register the module:enable command.
     */
    protected function registerEnableCommand()
    {
        $this->app->singleton('command.weiran.enable', function () {
            return new WeiranEnableCommand();
        });

        $this->commands('command.weiran.enable');
    }

    /**
     * Register the module:list command.
     */
    protected function registerListCommand()
    {
        $this->app->singleton('command.weiran.list', function ($app) {
            return new WeiranListCommand($app['weiran']);
        });

        $this->commands('command.weiran.list');
    }

    /**
     * Register the module:migrate command.
     */
    protected function registerMigrateCommand()
    {
        $this->app->singleton('command.weiran.migrate', function ($app) {
            return new WeiranMigrateCommand($app['migrator'], $app['weiran']);
        });

        $this->commands('command.weiran.migrate');
    }

    /**
     * Register the module:migrate:refresh command.
     */
    protected function registerMigrateRefreshCommand()
    {
        $this->app->singleton('command.weiran.migrate.refresh', function () {
            return new WeiranMigrateRefreshCommand();
        });

        $this->commands('command.weiran.migrate.refresh');
    }

    /**
     * Register the module:migrate:reset command.
     */
    protected function registerMigrateResetCommand()
    {
        $this->app->singleton('command.weiran.migrate.reset', function ($app) {
            return new WeiranMigrateResetCommand($app['weiran'], $app['files'], $app['migrator']);
        });

        $this->commands('command.weiran.migrate.reset');
    }

    /**
     * Register the module:migrate:rollback command.
     */
    protected function registerMigrateRollbackCommand()
    {
        $this->app->singleton('command.weiran.migrate.rollback', function ($app) {
            $repository = $app['migration.repository'];
            $table      = $app['config']['database.migrations'];

            $migrator = new Migrator($table, $repository, $app['db'], $app['files']);

            return new WeiranMigrateRollbackCommand($migrator, $app['weiran']);
        });

        $this->commands('command.weiran.migrate.rollback');
    }

    /**
     * Register the module:optimize command.
     */
    protected function registerOptimizeCommand()
    {
        $this->app->singleton('command.weiran.optimize', function () {
            return new WeiranOptimizeCommand();
        });

        $this->commands('command.weiran.optimize');
    }

    /**
     * Register the module:seed command.
     */
    protected function registerSeedCommand()
    {
        $this->app->singleton('command.weiran.seed', function ($app) {
            return new WeiranSeedCommand($app['weiran']);
        });

        $this->commands('command.weiran.seed');
    }
}
