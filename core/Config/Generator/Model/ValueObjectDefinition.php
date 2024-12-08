<?php

declare(strict_types=1);

namespace Core\Config\Generator\Model;

final class ValueObjectDefinition
{
    /**
     * @param PropertyDefinition[] $properties
     */
    public function __construct(
        public string $className,
        public array $properties
    ) {}
}
