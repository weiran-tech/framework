<?php

declare(strict_types = 1);

namespace Weiran\Framework\Tests\Helper;

use Weiran\Framework\Application\TestCase;
use Weiran\Framework\Helper\HtmlHelper;

/**
 * ArrayHelperTest
 */
class HtmlHelperTest extends TestCase
{

    public function testNameToId(): void
    {
        $string = "user[info][data][zh]";
        $this->assertEquals('user-info-data-zh', HtmlHelper::nameToId($string));
    }

    public function testNameToArray(): void
    {
        $name = "user[city,test][location][zh]";
        $this->assertEquals(['user', 'city,test', 'location', 'zh'], HtmlHelper::nameToArray($name));
    }

}