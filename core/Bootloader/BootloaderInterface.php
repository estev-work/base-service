<?php

namespace Core\Bootloader;

use Core\Container\ContainerInterface;

interface BootloaderInterface
{
    /**
     * @param ContainerInterface $container
     * @return void
     */
    public function init(ContainerInterface $container): void;
}