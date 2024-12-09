<?php


use Config\TestConfigInterface;
use Core\Container\Exceptions\NotFoundException;
use Project\Bootloader\ConfigBootloader;

require 'vendor/autoload.php';
require 'core/bootstrap.php';

bootstrap();
/** @var Core\Container\ContainerInterface $container */
$container = $GLOBALS['container'];
new ConfigBootloader()->init($container);

try {
    $res = test($container);
    var_dump($res->getSecurity()->getJwt()->getSecretKey());
} catch (\Psr\Container\NotFoundExceptionInterface|\Psr\Container\ContainerExceptionInterface $e) {
    echo $e->getMessage();
}
/**
 * @throws NotFoundException
 */
function test(\Core\Container\ContainerInterface $container): TestConfigInterface
{
    /** @var TestConfigInterface $class */
    $class = new \Core\Container\ScopedContainer()->make(TestConfigInterface::class);
    return $class;
}