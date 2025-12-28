<?php

declare(strict_types = 1);

namespace Weiran\Framework\Exceptions;

/**
 * PolicyException
 */
class PolicyException extends BaseException
{
    /**
     * @var int
     */
    protected $code = 101;
}
