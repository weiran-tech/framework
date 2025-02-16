<?php

declare(strict_types = 1);

namespace Weiran\Framework\Facade;

use Illuminate\Support\Facades\Facade;
use Weiran\Framework\Parse\Ini;

/**
 * @see Ini
 */
class IniFacade extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'weiran.ini';
    }
}
