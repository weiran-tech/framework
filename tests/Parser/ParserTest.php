<?php

declare(strict_types = 1);

namespace Weiran\Framework\Tests\Parser;

use Weiran\Framework\Application\TestCase;

class ParserTest extends TestCase
{

    public function testToArray(): void
    {
        $mobile = <<<YAML
name:
  key : 1
  key2 : '2'
YAML;;
        $array = app('weiran.yaml')->parse($mobile);
        $this->assertEquals(1, $array['name']['key']);
        $this->assertEquals('2', $array['name']['key2']);
    }
}