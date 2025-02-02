<?php

declare(strict_types = 1);

namespace Weiran\Framework\Helper;

/**
 * Methods that may be useful for processing HTML tasks
 * @author  Alexey Bobkov, Samuel Georges
 */
class HtmlHelper
{
    /**
     * Converts a HTML array string to an identifier string.
     * HTML: user[location][city]
     * Result: user-location-city
     * @param string $string to process
     * @return string
     */
    public static function nameToId(string $string)
    {
        return rtrim(str_replace('--', '-', str_replace(['[', ']'], '-', $string)), '-');
    }

    /**
     * Converts a HTML named array string to a PHP array. Empty values are removed.
     * HTML: user[location][city]
     * PHP:  ['user', 'location', 'city']
     * @param string $string to process
     * @return array
     */
    public static function nameToArray(string $string)
    {
        $result = [$string];

        if (strpbrk($string, '[]') === false)
            return $result;

        if (preg_match('/^([^\]]+)(?:\[(.+)\])+$/', $string, $matches)) {
            if (count($matches) < 2)
                return $result;

            $result = explode('][', $matches[2]);
            array_unshift($result, $matches[1]);
        }

        $result = array_filter($result, function ($val) {
            return strlen($val);
        });

        return $result;
    }
}

