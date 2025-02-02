<?php

declare(strict_types = 1);

namespace Weiran\Framework\Exceptions;

use Exception;
use Weiran\Framework\Classes\Resp;
use Throwable;

/**
 * BaseException
 */
abstract class BaseException extends Exception
{

    /**
     * Exception Context
     * @var array
     */
    protected array $context = [];

    /**
     * BaseException constructor.
     * @param string         $message message
     * @param int            $code code
     * @param Throwable|null $previous previous
     */
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        if ($message instanceof Resp) {
            parent::__construct($message->getMessage(), $message->getCode(), $previous);
        }
        else {
            parent::__construct($message, $code, $previous);
        }
    }


    public function setContext(array $context = []): self
    {
        $this->context = $context;
        return $this;
    }

    public function context(): array
    {
        return $this->context;
    }
}