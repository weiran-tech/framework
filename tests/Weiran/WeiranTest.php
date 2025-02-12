<?php

declare(strict_types = 1);

namespace Weiran\Framework\Tests\Weiran;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Weiran\Framework\Application\TestCase;
use Weiran\Framework\Helper\ArrayHelper;

class WeiranTest extends TestCase
{
    /**
     * namespace test
     */
    public function testNamespace(): void
    {
        $namespace = poppy_class('module.site', 'ServiceProvider');
        $this->assertEquals('Site\ServiceProvider', $namespace);
        $namespace = poppy_class('module.site');
        $this->assertEquals('Site', $namespace);
        $namespace = poppy_class('weiran.system', 'ServiceProvider');
        $this->assertEquals('Weiran\System\ServiceProvider', $namespace);
        $namespace = poppy_class('weiran.system');
        $this->assertEquals('Weiran\System', $namespace);
    }


    public function testPath()
    {
        $path = poppy_path('module.site', 'src/models/Default.php');
        $this->assertTrue(Str::endsWith($path, 'modules/site/src/models/Default.php'));
    }

    public function testGenKey(): void
    {
        $arr    = [
            'location' => 'https://www.baidu.com',
            'status'   => 'error',
        ];
        $genKey = ArrayHelper::genKey($arr);

        // 组合数组
        $this->assertEquals('location|https://www.baidu.com;status|error', $genKey);

        // 组合空
        $this->assertEquals('', ArrayHelper::genKey([]));
    }

    public function testAll()
    {
        $this->testOptimize();

        /** @var Collection $enabled */
        $enabled = app('weiran')->all();
        $this->assertNotEquals(0, $enabled->count());
    }

    public function testOptimize(): void
    {
        $poppyJson = storage_path('app/weiran.json');
        if (app('files')->exists($poppyJson)) {
            app('files')->delete($poppyJson);
        }
        app('weiran')->optimize();
        $this->assertFileExists($poppyJson);
    }

    /**
     * 测试模块加载
     */
    public function testLoaded(): void
    {
        $folders = glob(base_path('modules/*/src'), GLOB_BRACE);
        collect($folders)->each(function ($folder) {
            $matched = preg_match('/modules\/(?<module>[a-z]*)\/src/', $folder, $matches);
            $name    = 'module.' . $matches['module'];
            if ($matched && !app('weiran')->exists($name)) {
                $this->fail("Module `{$matches['module']}` Not Exist , Please run `php artisan weiran:optimize` to fix.");
            }
            else {
                $this->assertTrue(true, "Module `{$matches['module']}` loaded.");
            }
        });
    }
}