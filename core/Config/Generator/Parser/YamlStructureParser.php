<?php

declare(strict_types=1);

namespace Core\Config\Generator\Parser;

use Core\Config\Generator\Model\ConfigStructure;
use Core\Config\Generator\Model\PropertyDefinition;
use Core\Config\Generator\Model\ValueObjectDefinition;

final class YamlStructureParser
{
    public function parse(array $data, string $filename): ConfigStructure
    {
        $className = ucfirst($filename).'Config';
        $interfaceName = ucfirst($filename) . 'ConfigInterface';
        $valueObjects = [];
        $properties = $this->parseLevel($data, $valueObjects, []);

        $voDefs = [];
        foreach ($valueObjects as $vo) {
            $voDefs[] = new ValueObjectDefinition(
                $vo['className'],
                array_map(fn($p) => $this->arrToProperty($p), $vo['properties'])
            );
        }

        return new ConfigStructure(
            $className,
            $interfaceName,
            array_map(fn($p) => $this->arrToProperty($p), $properties),
            $voDefs
        );
    }

    private function arrToProperty(array $p): PropertyDefinition
    {
        return new PropertyDefinition(
            $p['name'],
            $p['originalName'],
            $p['type'],
            $p['complex'],
            $p['isArray'] ?? false,
            $p['voClass'] ?? null
        );
    }

    private function parseLevel(array $data, array &$valueObjects, array $path): array
    {
        $props = [];
        foreach ($data as $key => $value) {
            $currentPath = array_merge($path, [$key]);
            if (is_array($value)) {
                $props[] = $this->resolveArrayOrObject($key, $value, $valueObjects, $currentPath);
            } else {
                $props[] = [
                    'name' => $this->normalizeName($key),
                    'originalName' => $key,
                    'type' => $this->getPrimitiveType($value),
                    'complex' => false,
                    'isArray' => false
                ];
            }
        }
        return $props;
    }

    private function resolveArrayOrObject(string $key, array $value, array &$valueObjects, array $path): array
    {
        if ($this->isAssocArray($value)) {
            $voClassName = $this->generateVoClassName($key);
            $voProps = $this->parseLevel($value, $valueObjects, $path);
            $valueObjects[] = [
                'className' => $voClassName,
                'properties' => $voProps,
                'path' => $path
            ];
            return [
                'name' => $this->normalizeName($key),
                'originalName' => $key,
                'type' => $voClassName,
                'complex' => true,
                'voClass' => $voClassName,
                'isArray' => false,
                'voPath' => $path
            ];
        }

        $first = reset($value);
        if (is_array($first)) {
            $voClassName = $this->generateVoClassName($key);
            $voProps = $this->parseLevel($first, $valueObjects, $path);
            $valueObjects[] = [
                'className' => $voClassName,
                'properties' => $voProps,
                'path' => $path
            ];
            return [
                'name' => $this->normalizeName($key),
                'originalName' => $key,
                'type' => 'array',
                'complex' => true,
                'voClass' => $voClassName,
                'isArray' => true,
                'voPath' => $path
            ];
        }

        return [
            'name' => $this->normalizeName($key),
            'originalName' => $key,
            'type' => 'array',
            'complex' => false,
            'isArray' => true
        ];
    }

    private function normalizeName(string $key): string
    {
        $parts = explode('-', $key);
        $parts = array_map('ucfirst', $parts);
        return lcfirst(implode('', $parts));
    }

    private function isAssocArray(array $arr): bool
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    private function getPrimitiveType($value): string
    {
        return match (gettype($value)) {
            'string' => 'string',
            'integer' => 'int',
            'double' => 'float',
            'boolean' => 'bool',
            default => 'mixed',
        };
    }

    private function generateVoClassName(string $key): string
    {
        $parts = explode('-', $key);
        $parts = array_map('ucfirst', $parts);
        $parts = implode('', $parts);
        $parts .= 'Data';
        return $parts;
    }
}
