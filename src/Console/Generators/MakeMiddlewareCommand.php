<?php

declare(strict_types = 1);

namespace Weiran\Framework\Console\Generators;

use Weiran\Framework\Console\GeneratorCommand;

/**
 * Make Middleware
 */
class MakeMiddlewareCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'poppy:middleware
    	{slug : The slug of the module.}
    	{name : The name of the middleware class.}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Create a new module middleware class';

    /**
     * String to store the command type.
     * @var string
     */
    protected $type = 'Module middleware';

    /**
     * Get the stub file for the generator.
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . '/stubs/middleware.stub';
    }

    /**
     * Get the default namespace for the class.
     * @param string $rootNamespace namespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return weiran_class($this->argument('slug'), 'Http\\Middlewares');
    }
}
