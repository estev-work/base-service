<?php

declare(strict_types=1);

namespace Core\Config\Generator\Model;

final class ConfigStructure
{
    /**
     * @param PropertyDefinition[] $properties
     * @param ValueObjectDefinition[] $valueObjects
     */
    public function __construct(
        public string $className,
        public string $InterfaceName,
        public array $properties,
        public array $valueObjects
    ) {}
}
