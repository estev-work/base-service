<?php

declare(strict_types=1);

namespace Core\Config\Generator\Util;

use Core\Config\Generator\Model\PropertyDefinition;

class Assignment
{
    public function generateAssignment(PropertyDefinition $prop): string
    {
        if ($prop->voClass !== null) {
            if ($prop->isArray) {
                return "array_map(fn(\$item) => new {$prop->voClass}(\$item), (\$data['{$prop->originalName}'] ?? []))";
            }
            return "new {$prop->voClass}((array)(\$data['{$prop->originalName}'] ?? []))";
        }

        if ($prop->isArray && !$prop->complex) {
            return "\$data['{$prop->originalName}'] ?? []";
        }

        $default = match ($prop->type) {
            'string' => "''",
            'int' => "0",
            'float' => "0.0",
            'bool' => "false",
            'array' => "[]",
            default => 'null',
        };

        return "\$data['{$prop->originalName}'] ?? {$default}";
    }
}