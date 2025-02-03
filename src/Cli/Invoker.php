<?php

declare(strict_types = 1);

namespace Weiran\Framework\Cli;

use Symfony\Component\Finder\Finder;

class Invoker
{
    private string $path;

    public function __construct($base_path)
    {
        $this->path = $base_path;
    }

    public function __invoke(...$parameters): bool
    {
        $param = $parameters[1] ?? 'clear';

        if ($param !== 'clear') {
            echo 'Error Param.';
        }

        $Finder = Finder::create()
            ->name('*.php')
            ->in([
                $this->path . '/storage/framework/',
            ])
            ->depth('== 0');


        // check if there are any search results
        if ($Finder->hasResults()) {
            foreach ($Finder as $file) {
                $absoluteFilePath = $file->getRealPath();
                @unlink($absoluteFilePath);
            }
        }

        @unlink($this->path . '/storage/app/poppy.json');

        echo 'Weiran Clear succeeded.';
        return true;
    }
}
