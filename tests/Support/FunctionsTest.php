<?php

declare(strict_types = 1);

namespace Weiran\Framework\Tests\Support;

use Weiran\Framework\Application\TestCase;

class FunctionsTest extends TestCase
{

    public function testPoppyPath(): void
    {
        // module - system
        $systemPath = poppy_path('system', 'src/sample.php');
        $this->assertEquals(base_path('modules/system/src/sample.php'), $systemPath);

        $systemPath = poppy_path('module.system', 'src/sample.php');
        $this->assertEquals(base_path('modules/system/src/sample.php'), $systemPath);

        // poppy - system
        $poppySystemPath = poppy_path('weiran.system', 'src/sample.php');
        $this->assertEquals(app('path.weiran') . '/system/src/sample.php', $poppySystemPath);

        // base Path = root/modules
        $poppyRoot = poppy_path();
        $this->assertEquals(base_path('modules/'), $poppyRoot);
    }

    public function testPoppyClass()
    {
        $poppyCoreModel = poppy_class('weiran.core', 'Models');
        $this->assertEquals('Weiran\\Core\\Models', $poppyCoreModel);

        $moduleSiteModal = poppy_class('module.site', 'Models');
        $this->assertEquals('Site\\Models', $moduleSiteModal);
    }

    public function testPoppyFriendly()
    {
        $name = poppy_friendly('\Weiran\Framework\Weiran\Weiran');
        $this->assertEquals(trans('poppy::util.classes.poppy.poppy'), $name);
        $name = poppy_friendly('\Demo\Models\NotExistModel');
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