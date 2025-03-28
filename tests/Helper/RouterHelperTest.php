<?php

declare(strict_types = 1);

namespace Weiran\Framework\Tests\Helper;

use Weiran\Framework\Application\TestCase;
use Weiran\Framework\Helper\RouterHelper;

/**
 * ArrayHelperTest
 */
class RouterHelperTest extends TestCase
{

    public function testNormalizeUrl(): void
    {
        $url = "demo/content/form";
        $this->assertEquals('/demo/content/form', RouterHelper::normalizeUrl($url));
    }

    public function testSegmentizeUrl(): void
    {
        $url = "demo/content/form";
        $this->assertEquals(['demo', 'content', 'form'], RouterHelper::segmentizeUrl($url));
    }

    public function testRebuildUrl(): void
    {
        $url = ['demo', 'content', 'form'];
        $this->assertEquals('/demo/content/form', RouterHelper::rebuildUrl($url));
    }

    public function testParseValues(): void
    {
        $arr    = ['demo', 'content', 'form'];
        $object = ['demo', 'content', 'form'];
        $this->assertEquals('zh', RouterHelper::parseValues($object, $arr, 'zh'));
    }

    public function testSegmentIsWildcard(): void
    {
        $url = ':xxx.com/*';
        $this->assertEquals(true, RouterHelper::segmentIsWildcard($url));
    }

    public function testSegmentIsOptional(): void
    {
        $url = 'http://xxx.com?a=t';
        $this->assertEquals(true, RouterHelper::segmentIsOptional($url));
    }

    public function testGetParameterName(): void
    {
        $url = 'http://xxx.com?a=t';
        $this->assertEquals('ttp://xxx.com', RouterHelper::getParameterName($url));
    }

    public function testGetSegmentRegExp(): void
    {
        $url = '|weiran.tech?a=t';
        $this->assertEquals('/weiran.tech?a=t/', RouterHelper::getSegmentRegExp($url));
    }

    public function testGetSegmentDefaultValue(): void
    {
        $url = 'weiran.tech?a=t';
        $this->assertEquals('a=t', RouterHelper::getSegmentDefaultValue($url));
    }

}