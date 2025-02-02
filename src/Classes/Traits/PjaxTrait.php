<?php

declare(strict_types = 1);

namespace Weiran\Framework\Classes\Traits;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Weiran\Framework\Classes\Resp;
use Request;

/**
 * Class Helpers.
 */
trait PjaxTrait
{

    /**
     * Pjax 请求错误
     * @param string $message
     * @return Application|ResponseFactory|JsonResponse|RedirectResponse|Response
     */
    public function pjaxError(string $message)
    {
        if (Request::pjax()) {
            return response($message, 416);
        }

        return Resp::web(Resp::PARAM_ERROR, $message);
    }
}