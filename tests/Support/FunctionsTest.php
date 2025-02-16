<?php

declare(strict_types = 1);

namespace Weiran\Framework\Tests\Support;

use Weiran\Framework\Application\TestCase;

class FunctionsTest extends TestCase
{

    public function testWeiranPath(): void
    {
        // module - system
        $systemPath = weiran_path('system', 'src/sample.php');
        $this->assertEquals(base_path('modules/system/src/sample.php'), $systemPath);

        $systemPath = weiran_path('module.system', 'src/sample.php');
        $this->assertEquals(base_path('modules/system/src/sample.php'), $systemPath);

        // weiran - system
        $weiranSystemPath = weiran_path('weiran.system', 'src/sample.php');
        $this->assertEquals(app('path.weiran') . '/system/src/sample.php', $weiranSystemPath);

        // base Path = root/modules
        $weiranRoot = weiran_path();
        $this->assertEquals(base_path('modules/'), $weiranRoot);
    }

    public function testWeiranClass()
    {
        $weiranCoreModel = weiran_class('weiran.core', 'Models');
        $this->assertEquals('Weiran\\Core\\Models', $weiranCoreModel);

        $moduleSiteModal = weiran_class('module.site', 'Models');
        $this->assertEquals('Site\\Models', $moduleSiteModal);
    }

    public function testWeiranFriendly()
    {
        $name = weiran_friendly('\Weiran\Framework\Weiran\Weiran');
        $this->assertEquals(trans('weiran::util.classes.weiran.weiran'), $name);
        $name = weiran_friendly('\Demo\Models\NotExistModel');
        $this->assertEquals('demo::util.classes.models.not_exist_model', $name);
    }


    public function testParseSeo()
    {
        $seo = parse_seo();
        $this->assertEquals(['', ''], $seo);

        $seo = parse_seo('title');
        $this->assertEquals(['title', ''], $seo);

        $seo = parse_seo('title', 'description');
        $this->assertEquals(['title', 'description'], $seo);

        $seo = parse_seo(['title']);
        $this->assertEquals(['title', ''], $seo);

        $seo = parse_seo([
            'title'       => 'title-t',
            'description' => 'description-d',
        ]);
        $this->assertEquals(['title-t', 'description-d'], $seo);
    }


    public function testJwtToken()
    {
        // assert empty
        $this->assertEquals('', jwt_token());
    }
}