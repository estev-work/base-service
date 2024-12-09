<?php

declare(strict_types=1);

namespace Core\Config\Generator\Parser;

use Core\Config\Generator\Model\ConfigStructure;
use Core\Config\Generator\Model\PropertyDefinition;
use Core\Config\Generator\Model\ValueObjectDefinition;

final class YamlStructureParser
{
    /**
     * @param array<string, mixed> $data
     * @param string $filename
     * @return ConfigStructure
     */
    public function parse(array $data, string $filename): ConfigStructure
    {
        $className = ucfirst($filename) . 'Config';
        $interfaceName = ucfirst($filename) . 'ConfigInterface';
        $valueObjects = [];
        $properties = $this->parseLevel($data, $valueObjects, []);

        $voDefs = [];
        foreach ($valueObjects as $vo) {
            /** @var array{className: string, properties: array<int, array<string, mixed>>, path: array<int, string>} $vo */
            $voDefs[] = new ValueObjectDefinition(
                $vo['className'],
                array_map(fn(array $p): PropertyDefinition => $this->arrToProperty($p), $vo['properties']),
                array_map(fn(string $elem): string => ucfirst($this->normalizeName($elem)), $vo['path'])
            );
        }

        return new ConfigStructure(
            $className,
            $interfaceName,
            array_map(fn(array $p): PropertyDefinition => $this->arrToProperty($p), $properties),
            $voDefs
        );
    }

    /**
     * @param array<string, mixed> $properties
     * @return PropertyDefinition
     */
    private function arrToProperty(array $properties): PropertyDefinition
    {
        return new PropertyDefinition(
            (string) $properties['name'],
            (string) $properties['originalName'],
            (string) $properties['type'],
            (bool) $properties['complex'],
            (bool) ($properties['isArray'] ?? false),
            isset($properties['voClass']) ? (string) $properties['voClass'] : null
        );
    }

    /**
     * @param array<string, mixed> $data
     * @param array<int, array{className: string, properties: array<int, array<string, mixed>>, path: array<int, string>}> $valueObjects
     * @param array<int, string> $path
     * @return array<int, array<string, mixed>>
     */
    private function parseLevel(array $data, array &$valueObjects, array $path): array
    {
        $props = [];
        /** @var array<string, mixed>|null $value */
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
                    'isArray' => false,
                ];
            }
        }
        return $props;
    }

    /**
     * @param string $key
     * @param array<string, mixed> $value
     * @param array<int, array{className: string, properties: array<int, array<string, mixed>>, path: array<int, string>}> $valueObjects
     * @param array<int, string> $path
     * @return array<string, mixed>
     */
    private function resolveArrayOrObject(string $key, array $value, array &$valueObjects, array $path): array
    {
        if ($this->isAssocArray($value)) {
            $voClassName = $this->generateVoClassName($key);
            $voProps = $this->parseLevel($value, $valueObjects, $path);
            $valueObjects[] = [
                'className' => $voClassName,
                'properties' => $voProps,
                'path' => $path,
            ];
            return [
                'name' => $this->normalizeName($key),
                'originalName' => $key,
                'type' => $voClassName,
                'complex' => true,
                'voClass' => $voClassName,
                'isArray' => false,
                'voPath' => $path,
            ];
        }

        $first = reset($value);
        /** @var array<string, mixed>|false $first */
        if (is_array($first)) {
            $voClassName = $this->generateVoClassName($key);
            $voProps = $this->parseLevel($first, $valueObjects, $path);
            $valueObjects[] = [
                'className' => $voClassName,
                'properties' => $voProps,
                'path' => $path,
            ];
            return [
                'name' => $this->normalizeName($key),
                'originalName' => $key,
                'type' => 'array',
                'complex' => true,
                'voClass' => $voClassName,
                'isArray' => true,
                'voPath' => $path,
            ];
        }

        return [
            'name' => $this->normalizeName($key),
            'originalName' => $key,
            'type' => 'array',
            'complex' => false,
            'isArray' => true,
        ];
    }

    /**
     * @param string $key
     * @return string
     */
    private function normalizeName(string $key): string
    {
        $parts = explode('-', $key);
        $parts = array_map('ucfirst', $parts);
        return lcfirst(implode('', $parts));
    }

    /**
     * @param array<string, mixed> $arr
     * @return bool
     */
    private function isAssocArray(array $arr): bool
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * @param mixed $value
     * @return string
     */
    private function getPrimitiveType(mixed $value): string
    {
        return match (gettype($value)) {
            'string' => 'string',
            'integer' => 'int',
            'double' => 'float',
            'boolean' => 'bool',
            default => 'mixed',
        };
    }

    /**
     * @param string $key
     * @return string
     */
    private function generateVoClassName(string $key): string
    {
        $parts = explode('-', $key);
        $parts = array_map('ucfirst', $parts);
        return implode('', $parts);
    }
}
