<?php

declare(strict_types=1);

namespace Core\Bootloader;

use Core\Container\ContainerInterface;

abstract class Bootloader implements BootloaderInterface
{
    /**
     * @param ContainerInterface $container
     */
    abstract public function init(ContainerInterface $container): void;
}