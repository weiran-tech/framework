<?php

declare(strict_types = 1);

namespace Weiran\Framework\Console\Generators;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Console\Helper\ProgressBar;
use Weiran\Framework\Events\WeiranMake;
use Weiran\Framework\Weiran\Weiran;

/**
 * Make Weiran
 */
class MakeWeiranCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'weiran:make
        {slug : The slug of the module}
        {--Q|quick : Skip the make:module wizard and use default values}
    ';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Create a new Weiran module and bootstrap it';

    /**
     * The weiran instance.
     * @var Weiran
     */
    protected Weiran $weiran;

    /**
     * The filesystem instance.
     * @var Filesystem
     */
    protected Filesystem $files;

    /**
     * Array to store the configuration details.
     * @var array
     */
    protected array $conf;

    /**
     * Create a new command instance.
     * @param Filesystem $files
     * @param Weiran     $weiran
     */
    public function __construct(Filesystem $files, Weiran $weiran)
    {
        parent::__construct();

        $this->files  = $files;
        $this->weiran = $weiran;
    }

    /**
     * Execute the console command.
     * @throws FileNotFoundException
     */
    public function handle(): void
    {
        $slug = Str::slug($this->argument('slug'));
        if (app('weiran')->exists($slug)) {
            $this->error('Slug `' . $slug . '` exists');
            return;
        }


        $this->conf['slug']        = Str::slug($this->argument('slug'));
        $this->conf['name']        = Str::snake($this->conf['slug']);
        $this->conf['version']     = '1.0';
        $this->conf['description'] = 'This is the description for the weiran ' . $this->conf['name'] . ' module.';

        if ($this->option('quick')) {
            $this->conf['basename']  = Str::snake($this->conf['slug']);
            $this->conf['namespace'] = Str::studly($this->conf['basename']);
            $this->generate();
            return;
        }

        $this->displayHeader('make_module_introduction');

        $this->stepOne();
    }

    /**
     * Step 1: Configure module manifest.
     * @return bool
     * @throws FileNotFoundException
     */
    protected function stepOne(): bool
    {
        $this->displayHeader('make_module_step_1');

        $this->conf['name']        = $this->ask('Please enter the name of the module:', $this->conf['name']);
        $this->conf['slug']        = $this->ask('Please enter the slug for the module:', $this->conf['slug']);
        $this->conf['version']     = $this->ask('Please enter the module version:', $this->conf['version']);
        $this->conf['description'] = $this->ask('Please enter the description of the module:', $this->conf['description']);
        $this->conf['namespace']   = Str::studly($this->conf['slug']);

        $this->comment('You have provided the following manifest information:');
        $this->comment('Name:                       ' . $this->conf['name']);
        $this->comment('Slug:                       ' . $this->conf['slug']);
        $this->comment('Version:                    ' . $this->conf['version']);
        $this->comment('Description:                ' . $this->conf['description']);
        $this->comment('Namespace (auto-generated): ' . $this->conf['namespace']);

        if ($this->confirm('If the provided information is correct, type "yes" to generate.')) {
            $this->comment('Thanks! That\'s all we need.');
            $this->comment('Now relax while your module is generated.');

            $this->generate();
        }
        else {
            return $this->stepOne();
        }

        return true;
    }

    /**
     * Generate the module.
     */
    protected function generate(): void
    {
        $steps = [
            'Generating module...'       => 'generateModule',
            'Optimizing module cache...' => 'optimizeModules',
        ];

        $progress = new ProgressBar($this->output, count($steps));
        $progress->start();

        foreach ($steps as $message => $function) {
            $progress->setMessage($message);

            $this->$function();

            $progress->advance();
        }

        $progress->finish();

        event(new WeiranMake($this->conf['slug']));

        // 移除 js 文件
        $this->weiran->optimize();

        $this->info("\nWeiran Module generated successfully.");
    }

    /**
     * Generate defined module folders.
     */
    protected function generateModule(): void
    {
        if (!$this->files->isDirectory(weiran_path())) {
            $this->files->makeDirectory(weiran_path());
        }

        $directory = weiran_path(null, $this->conf['slug']);
        $source    = __DIR__ . '/../../../resources/stubs/weiran';

        $this->files->makeDirectory($directory);

        $sourceFiles = $this->files->allFiles($source, true);

        foreach ($sourceFiles as $file) {
            $contents = $this->replacePlaceholders($file->getContents());
            $subPath  = $file->getRelativePathname();

            $filePath = $directory . '/' . $subPath;
            $dir      = dirname($filePath);

            if (!$this->files->isDirectory($dir)) {
                $this->files->makeDirectory($dir, 0755, true);
            }

            $this->files->put($filePath, $contents);
        }
    }

    /**
     * Reset module cache of enabled and disabled modules.
     */
    protected function optimizeModules()
    {
        return $this->callSilent('weiran:optimize');
    }

    /**
     * Pull the given stub file contents and display them on screen.
     * @param string $file file
     * @param string $level info type
     * @return mixed
     * @throws FileNotFoundException
     */
    protected function displayHeader(string $file = '', string $level = 'info')
    {
        $stub = $this->files->get(__DIR__ . '/../../../resources/stubs/console/' . $file . '.stub');
        return $this->$level($stub);
    }

    /**
     * Replace Placeholder
     * @param string $contents Replace Content
     * @return string
     */
    protected function replacePlaceholders(string $contents): string
    {
        $find = [
            'DummyNamespace',
            'DummyName',
            'DummySlug',
            'DummyVersion',
            'DummyDescription',
        ];

        $replace = [
            $this->conf['namespace'],
            $this->conf['name'],
            $this->conf['slug'],
            $this->conf['version'],
            $this->conf['description'],
        ];

        return str_replace($find, $replace, $contents);
    }
}
