<?php

declare(strict_types = 1);

namespace Weiran\Framework\Console\Generators;

use Illuminate\Support\Str;
use Weiran\Framework\Console\GeneratorCommand;

/**
 * MakeController
 */
class MakeControllerCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'weiran:controller
    	{slug : The slug of the module}
    	{type : The type of the controller class}
    	{name : The name of the controller class}
    	{--resource : Generate a module resource controller class}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Create a new poppy module controller class';

    /**
     * String to store the command type.
     * @var string
     */
    protected $type = 'Poppy module controller';

    /**
     * Get the stub file for the generator.
     * @return string
     */
    protected function getStub(): string
    {
        if ($this->option('resource')) {
            return __DIR__ . '/stubs/controller.resource.stub';
        }

        return __DIR__ . '/stubs/controller.stub';
    }

    /**
     * Get the default namespace for the class.
     * @param string $rootNamespace namespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        $type = $this->argument('type');

        if (!in_array($type, ['web', 'api', 'backend'])) {
            $type = 'web';
        }

        return weiran_class($this->argument('slug'), 'Http\\Request\\' . Str::studly($type));
    }
}
