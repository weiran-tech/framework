<?php

declare(strict_types = 1);

namespace Weiran\Framework\Console;

use Illuminate\Support\ServiceProvider;
use Weiran\Framework\Console\Generators\MakeCommandCommand;
use Weiran\Framework\Console\Generators\MakeControllerCommand;
use Weiran\Framework\Console\Generators\MakeEventCommand;
use Weiran\Framework\Console\Generators\MakeListenerCommand;
use Weiran\Framework\Console\Generators\MakeMiddlewareCommand;
use Weiran\Framework\Console\Generators\MakeMigrationCommand;
use Weiran\Framework\Console\Generators\MakeModelCommand;
use Weiran\Framework\Console\Generators\MakePolicyCommand;
use Weiran\Framework\Console\Generators\MakePoppyCommand;
use Weiran\Framework\Console\Generators\MakeProviderCommand;
use Weiran\Framework\Console\Generators\MakeRequestCommand;
use Weiran\Framework\Console\Generators\MakeSeederCommand;
use Weiran\Framework\Console\Generators\MakeTestCommand;

class GeneratorServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register()
    {
        $this->commands([
            MakePoppyCommand::class,
            MakeControllerCommand::class,
            MakeMiddlewareCommand::class,
            MakeMigrationCommand::class,
            MakeModelCommand::class,
            MakePolicyCommand::class,
            MakeProviderCommand::class,
            MakeRequestCommand::class,
            MakeSeederCommand::class,
            MakeTestCommand::class,
            MakeCommandCommand::class,
            MakeEventCommand::class,
            MakeListenerCommand::class,
        ]);
    }
}
