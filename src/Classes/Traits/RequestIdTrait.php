<?php

declare(strict_types = 1);

namespace Weiran\Framework\Classes\Traits;

use Psr\Log\LoggerInterface;
use Weiran\System\Classes\Logger\Logging;

/**
 * RequestId
 */
trait RequestIdTrait
{
    /**
     * 请求ID
     */
    protected string $requestId = '';

    public function logger(): LoggerInterface
    {
        $logger = Logging::logger(static::class);
        if ($this->requestId) {
            $logger->withContext(['requestId' => $this->requestId]);
        }
        return $logger;
    }

    /**
     * 设置请求ID
     */
    public function setRequestId(string $requestId): self
    {
        $this->requestId = $requestId;
        return $this;
    }
}
