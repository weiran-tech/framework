<?php

declare(strict_types = 1);

namespace Weiran\Framework\Database\Migrations;

use Illuminate\Database\ConnectionResolverInterface as Resolver;
use Illuminate\Database\Migrations\MigrationRepositoryInterface;
use Illuminate\Database\Migrations\Migrator as BaseMigrator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Migrator
 */
class Migrator extends BaseMigrator
{

    /**
     * @var string Table name
     */
    protected string $table;

    /**
     * Create a new migrator instance.
     *
     * @param string                       $table
     * @param MigrationRepositoryInterface $repository
     * @param Resolver                     $resolver
     * @param Filesystem                   $files
     */
    public function __construct(
        $table,
        MigrationRepositoryInterface $repository,
        Resolver $resolver,
        Filesystem $files
    )
    {
        $this->table = $table;

        parent::__construct($repository, $resolver, $files);
    }

    /**
     * Rollback the last migration operation.
     *
     * @param array|string $paths   路径
     * @param array        $options 选项
     *
     * @return array
     */
    public function rollback($paths = [], array $options = [])
    {
        $rolledBack = [];

        $migrations = $this->getRanMigrations();
        $files      = $this->getMigrationFiles($paths);
        $count      = count($migrations);

        if ($count === 0) {
            $this->note('<info>Nothing to rollback.</info>');
        }
        else {
            $this->requireFiles($files);

            $steps = Arr::get($options, 'step', 0);
            if ($steps == 0) {
                $steps = 1;
            }

            $lastBatch = $this->repository->getLastBatchNumber();
            $stepDown  = false;

            foreach ($migrations as $migration) {
                $migration = (object) $migration;

                if ($lastBatch > $migration->batch && $stepDown) {
                    $steps--;
                    $stepDown  = false;
                    $lastBatch = $migration->batch;
                }

                if ($steps <= 0) {
                    break;
                }

                if (Arr::exists($files, $migration->migration)) {
                    $rolledBack[] = $files[$migration->migration];

                    $stepDown = true;

                    $this->runDown(
                        $files[$migration->migration],
                        $migration,
                        Arr::get($options, 'pretend', false)
                    );
                }
            }
        }

        return $rolledBack;
    }

    /**
     * Get all the ran migrations.
     *
     * @return Collection
     */
    public function getRanMigrations()
    {
        $query = $this->resolveConnection($this->connection)->table($this->table);

        return $query->orderBy('batch', 'desc')->orderBy('migration', 'desc')->get();
    }
}
