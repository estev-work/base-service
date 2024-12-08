<?php

namespace Core\Bootloader;

interface BootloaderInterface
{
    /**
     * @return void
     */
    public function init(): void;
}