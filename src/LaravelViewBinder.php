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

use Illuminate\Contracts\Events\Dispatcher;

class LaravelViewBinder implements ViewBinder
{
    /**
     * The event dispatcher implementation.
     *
     * @var Dispatcher
     */
    protected $event;

    /**
     * The name of the view to bind JS variables to.
     *
     * @var string
     */
    protected $views;

    /**
     * Create a new Laravel view binder instance.
     *
     * @param Dispatcher   $event
     * @param string|array $views
     */
    public function __construct(Dispatcher $event, $views)
    {
        $this->event = $event;
        $this->views = str_replace('/', '.', (array)$views);
    }

    /**
     * Bind the given JavaScript to the view.
     *
     * @param string $js
     */
    public function bind($js)
    {
        foreach ($this->views as $view) {
            $this->event->listen("composing: {$view}", function () use ($js) {
                echo "<script>{$js}</script>";
            });
        }
    }
}
