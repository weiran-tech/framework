<?php

declare(strict_types = 1);

namespace Weiran\Framework\Parse;

use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Parser;

/**
 * Yaml helper class
 *
 * @author  Alexey Bobkov, Samuel Georges
 */
class Yaml
{
    /**
     * Parses supplied YAML contents in to a PHP array.
     * @param string $contents YAML contents to parse
     * @return mixed the YAML contents as an array
     */
    public function parse(string $contents): mixed
    {
        return (new Parser())->parse($contents);
    }

    /**
     * Parses YAML file contents in to a PHP array.
     * @param string $fileName file to read contents and parse
     * @return array the YAML contents as an array
     */
    public function parseFile(string $fileName): array
    {
        $contents = file_get_contents($fileName);

        return $this->parse($contents);
    }

    /**
     * Renders a PHP array to YAML format.
     * @param array $vars vars
     * @param array $options options
     *
     * Supported options:
     * - inline: The level where you switch to inline YAML.
     * - exceptionOnInvalidType: if an exception must be thrown on invalid types.
     * - objectSupport: if object support is enabled.
     * @return string
     */
    public function render(array $vars = [], array $options = []): string
    {
        $options = array_merge([
            'inline' => 20,
        ], $options);

        return (new Dumper())->dump($vars, $options['inline']);
    }
}
