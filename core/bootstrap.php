<?php

use Core\Container\Container;

require __DIR__ . '/../vendor/autoload.php';
function bootstrap(): void
{
    $container = new Container();
    $GLOBALS['container'] = $container;
}
