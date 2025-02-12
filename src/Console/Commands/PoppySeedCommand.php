<?php

declare(strict_types = 1);

namespace Weiran\Framework\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Weiran\Framework\Weiran\Weiran;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Poppy Seed
 */
class PoppySeedCommand extends Command
{
    /**
     * The console command name.
     * @var string
     */
    protected $name = 'poppy:seed';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Seed the database with records for a specific or all modules';

    /**
     * @var Weiran
     */
    protected Weiran $poppy;

    /**
     * @inheritDoc
     */
    public function __construct(Weiran $weiran)
    {
        parent::__construct();

        $this->poppy = $weiran;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $slug = $this->argument('slug');

        if (isset($slug)) {
            if (!$this->poppy->exists($slug)) {
                $this->error('Module does not exist.');
                return 1;
            }

            if ($this->poppy->isEnabled($slug)) {
                $this->seed($slug);
            }
            elseif ($this->option('force')) {
                $this->seed($slug);
            }

            return 0;
        }

        if ($this->option('force')) {
            $modules = $this->poppy->all();
        }
        else {
            $modules = $this->poppy->enabled();
        }

        foreach ($modules as $module) {
            $this->seed($module['slug']);
        }

        return 0;
    }

    /**
     * Seed the specific module.
     * @param string $slug slug
     */
    protected function seed(string $slug)
    {
        $module        = $this->poppy->where('slug', $slug);
        $params        = [];
        $namespacePath = poppy_class($slug);

        $rootSeeder = ucfirst(Str::camel(Str::after($module['slug'], '.'))) . 'DatabaseSeeder';
        $fullPath   = ucfirst(Str::camel($namespacePath)) . '\Seeders\\' . $rootSeeder;

        // 设置读取的命名空间问题
        Factory::guessFactoryNamesUsing(function ($modelName) use ($namespacePath) {
            $appNamespace = ucfirst(Str::camel($namespacePath)) . '\\';

            $modelName = Str::startsWith($modelName, $appNamespace . 'Models\\')
                ? Str::after($modelName, $appNamespace . 'Models\\')
                : Str::after($modelName, $appNamespace);

            // 这里不应该写死 namespace prefix，以后看看有没有机会去掉它。
            return 'Database\\Factories\\' . $modelName . 'Factory';
        });

        if ($this->option('class')) {
            $params['--class'] = $this->option('class');
        }
        elseif (class_exists($fullPath)) {
            $params['--class'] = $fullPath;
        }
        else {
            $this->error($fullPath . ' of ' . $module['slug'] . 'not exist');
            return;
        }

        if ($option = $this->option('database')) {
            $params['--database'] = $option;
        }

        if ($option = $this->option('force')) {
            $params['--force'] = $option;
        }

        $this->call('db:seed', $params);

        event($slug . '.seeded', [$module, $this->option()]);
    }

    /**
     * @inheritDoc
     */
    protected function getArguments(): array
    {
        return [
            ['slug', InputArgument::OPTIONAL, 'Module slug.'],
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getOptions(): array
    {
        return [
            ['class', null, InputOption::VALUE_OPTIONAL, 'The class name of the module\'s root seeder.'],
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to seed.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run while in production.'],
        ];
    }
}
