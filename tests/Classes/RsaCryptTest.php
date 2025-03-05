<?php

declare(strict_types = 1);

namespace Weiran\Framework\Tests\Classes;

use Illuminate\Support\Str;
use Weiran\Framework\Application\TestCase;
use Weiran\Framework\Classes\RsaCrypt;

class RsaCryptTest extends TestCase
{

    /**
     * @var string
     */
    private string $privateKey;

    /**
     * @var string
     */
    private string $pubKey;

    public function setUp(): void
    {
        parent::setUp();
        $this->privateKey = file_get_contents(dirname(__DIR__) . '/files/private.pem');
        $this->pubKey     = file_get_contents(dirname(__DIR__) . '/files/public.pem');
    }

    public function testEncrypt(): void
    {
        $rsa = new RsaCrypt();
        $rsa->setPrivateKey($this->privateKey);
        $rsa->setPublicKey($this->pubKey);
        $encrypt = $rsa->sign('abc');
        $this->outputVariables($encrypt);
        $this->assertTrue($rsa->verify('abc', $encrypt), 'crypt is not correct!');
    }

    public function testDecrypt(): void
    {
        $rsa = new RsaCrypt();
        $rsa->setPrivateKey($this->privateKey);
        $rsa->setPublicKey($this->pubKey);

        // 加密有长度限制
        $length = (1024 / 8) - 11 - 35;
        $ori    = Str::random((1024 / 8) - 11 - 35);
        $this->outputVariables($length);
        $content = $rsa->publicEncrypt($ori);
        $de      = $rsa->privateDecrypt($content);
        $this->outputVariables($de);
        $this->assertEquals($ori, $de);
    }
}
