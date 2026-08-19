<?php

/*
 * This file is part of the simpmelie/Helper-utils package.
 *
 * (c) simpmelie <melie647@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simpmelie\Utils;

interface ViewBinder
{
    /**
     * Bind the JavaScript variables to the view.
     *
     * @param string $js
     */
    public function bind($js);
}
