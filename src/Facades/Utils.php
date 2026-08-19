<?php

/*
 * This file is part of the simpmelie/Helper-utils package.
 *
 * (c) simpmelie <melie647@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simpmelie\Utils\Facades;

use Illuminate\Support\Facades\Facade;

class Utils extends Facade
{
    /**
     * The name of the binding in the IoC container.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'utils';
    }
}
