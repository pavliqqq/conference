<?php

namespace core;

use eftec\bladeone\BladeOne;

class View
{
    public static function render(string $view): void
    {
        $views = __DIR__ . '/../app/Views';
        $cache = __DIR__ . '/../cache';

        $blade = new BladeOne($views, $cache, BladeOne::MODE_AUTO);

        echo $blade->run($view);
    }
}