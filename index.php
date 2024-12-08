<?php


use Config\TestConfigInterface;
use Project\Bootloader\ConfigBootloader;

require 'vendor/autoload.php';
require 'core/bootstrap.php';

bootstrap();

new ConfigBootloader()->init($container);

try {
    /** @var TestConfigInterface $class */
    $class = $container->get(TestConfigInterface::class);
    $res = $class->getSecurity()->getJwt();
    var_dump($res);
} catch (\Psr\Container\NotFoundExceptionInterface|\Psr\Container\ContainerExceptionInterface $e) {
    echo $e->getMessage();
}
