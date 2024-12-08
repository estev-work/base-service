<?php

declare(strict_types=1);

namespace Core\Config\Generator\Model;

final class PropertyDefinition
{
    public function __construct(
        public string $name,
        public string $originalName,
        public string $type,
        public bool $complex,
        public bool $isArray,
        public ?string $voClass = null
    ) {}
}
