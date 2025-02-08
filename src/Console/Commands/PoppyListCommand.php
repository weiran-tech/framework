<?php

declare(strict_types = 1);

namespace Weiran\Framework\Console\Commands;

use Illuminate\Console\Command;
use Weiran\Framework\Weiran\Weiran;

/**
 * Poppy List
 */
class PoppyListCommand extends Command
{
    /**
     * The console command name.
     * @var string
     */
    protected $name = 'poppy:list';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'List all application modules';

    /**
     * @var Weiran
     */
    protected Weiran $poppy;

    /**
     * The table headers for the command.
     * @var array
     */
    protected array $headers = ['#', 'Name', 'Slug', 'Description', 'Status'];

    /**
     * Create a new command instance.
     * @param Weiran $poppy
     */
    public function __construct(Weiran $poppy)
    {
        parent::__construct();

        $this->poppy = $poppy;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $modules = $this->poppy->all();

        if (count($modules) === 0) {
            $this->error("Your application doesn't have any modules.");

            return 0;
        }

        $this->displayModules($this->getModules());

        return 0;
    }

    /**
     * Get all modules.
     * @return array
     */
    protected function getModules(): array
    {
        $modules = $this->poppy->all();
        $results = [];

        foreach ($modules as $module) {
            $results[] = $this->getModuleInformation($module);
        }

        return array_filter($results);
    }

    /**
     * Returns module manifest information.
     * @param array $module module
     * @return array
     */
    protected function getModuleInformation(array $module): array
    {
        return [
            '#'           => $module['order'],
            'name'        => $module['name'] ?? '',
            'slug'        => $module['slug'],
            'description' => $module['description'] ?? '',
            'status'      => $this->poppy->isEnabled($module['slug']) ? 'Enabled' : 'Disabled',
        ];
    }

    /**
     * Display the module information on the console.
     * @param array $modules modules
     */
    protected function displayModules(array $modules)
    {
        $this->table($this->headers, $modules);
    }
}
