<?php

declare(strict_types = 1);

namespace Weiran\Framework\Tests\Helper;

use Weiran\Framework\Application\TestCase;
use Weiran\Framework\Helper\EnvHelper;

class EnvHelperTest extends TestCase
{

    public function testIsInternalIp(): void
    {
        $this->assertTrue(EnvHelper::isInternalIp('127.0.0.1'));
        $this->assertTrue(EnvHelper::isInternalIp('192.168.1.1'));
        $this->assertTrue(EnvHelper::isInternalIp('192.168.10.1'));
        $this->assertFalse(EnvHelper::isInternalIp('110.242.68.4'));
    }
}