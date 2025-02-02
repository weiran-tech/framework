<?php

declare(strict_types = 1);

namespace Weiran\Framework\Classes\Traits;

use Exception;
use Illuminate\Support\MessageBag;
use Weiran\Framework\Classes\Resp;

/**
 * AppTrait
 */
trait AppTrait
{
    /**
     * error
     * @var Resp $error
     */
    protected $error;

    /**
     * success
     * @var Resp $success
     */
    protected $success;

    /**
     * 获取错误
     * @return Resp
     */
    public function getError(): Resp
    {
        if (is_null($this->error)) {
            return (new Resp(Resp::INNER_ERROR, '内部错误'));
        }
        return $this->error;
    }

    /**
     * 设置错误
     * @param string|MessageBag $error error
     * @return bool
     */
    public function setError($error): bool
    {
        if ($error instanceof Resp) {
            $this->error = $error;
        }
        elseif ($error instanceof MessageBag) {
            $messages   = $error->messages();
            $strMessage = '';
            foreach ($messages as $message) {
                $strMessage .= implode(',', $message) . PHP_EOL;
            }
            $this->error = new Resp(Resp::PARAM_ERROR, trim($strMessage));
        }
        elseif ($error instanceof Exception) {
            if ($error->getCode()) {
                $code = $error->getCode();
                // Fix Error : ["HY000", "SQLSTATE ..."]
                if (!is_int($code)) {
                    $code = Resp::ERROR;
                }
            }
            else {
                $code = Resp::ERROR;
            }
            $this->error = new Resp($code, $error->getMessage());
        }
        else {
            $this->error = new Resp(Resp::ERROR, $error);
        }

        return false;
    }

    /**
     * Get success messages;
     * @return Resp
     */
    public function getSuccess(): Resp
    {
        if ($this->success instanceof Resp) {
            return $this->success;
        }
        if (is_string($this->success)) {
            if (empty($this->success)) {
                $this->success = '操作成功';
            }
            $this->success = new Resp(Resp::SUCCESS, $this->success);
        }
        return $this->success;
    }

    /**
     * @param Resp|string $success 设置的成功信息
     * @return bool
     */
    public function setSuccess($success): bool
    {
        if ($success instanceof Resp) {
            $this->success = $success;
        }
        else {
            $this->success = new Resp(Resp::SUCCESS, $success);
        }

        return true;
    }
}