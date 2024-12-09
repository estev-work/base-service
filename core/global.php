<?php

use Core\Container\ContainerInterface;

function container(): ContainerInterface
{
    if (array_key_exists('container', $GLOBALS)){
        if ( $GLOBALS['container'] instanceof ContainerInterface ){
            return $GLOBALS['container'];
        }
    }
    throw new RuntimeException('Container is not defined');
}