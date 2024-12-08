<?php

declare(strict_types=1);

namespace Core\Config\Attributes;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Config
{
    public string $name;
    public string $interface;
    public function __construct(string $name, string $interface)
    {
        $this->name = $name;
        $this->interface = $interface;
    }
}