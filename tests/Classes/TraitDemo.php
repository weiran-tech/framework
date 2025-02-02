<?php

declare(strict_types = 1);

namespace Weiran\Framework\Tests\Classes;

use Exception;
use Weiran\Framework\Classes\Traits\AppTrait;
use Weiran\Framework\Classes\Traits\KeyParserTrait;
use Weiran\Framework\Exceptions\ApplicationException;

class TraitDemo
{
    use AppTrait, KeyParserTrait;

    public function error(): bool
    {
        return $this->setError('This is an Error String');
    }

    public function exception(): bool
    {
        return $this->setError((new Exception('This is an Exception String')));
    }

    public function exceptionWithCode($code): bool
    {
        return $this->setError((new ApplicationException('This is an Exception With Code', $code)));
    }

    public function success(): bool
    {
        return $this->setSuccess('操作成功');
    }

    public function successWithEmpty(): bool
    {
        return true;
    }
}