<?php

declare(strict_types = 1);

namespace Weiran\Framework\Console\Generators;

use Weiran\Framework\Console\GeneratorCommand;

/**
 * Make Request
 */
class MakeRequestCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'poppy:request
    	{slug : The slug of the module.}
    	{name : The name of the form request class.}
    ';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Create a new module form request class';

    /**
     * String to store the command type.
     * @var string
     */
    protected $type = 'Module request';

    /**
     * Get the stub file for the generator.
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . '/stubs/request.stub';
    }

    /**
     * Get the default namespace for the class.
     * @param string $rootNamespace namespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return poppy_class($this->argument('slug'), 'Http\\Validation');
    }
}
