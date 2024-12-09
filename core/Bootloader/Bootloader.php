<?php

declare(strict_types=1);

namespace Core\Bootloader;

abstract class Bootloader implements BootloaderInterface
{
    /**
     */
    abstract public function init(): void;
}