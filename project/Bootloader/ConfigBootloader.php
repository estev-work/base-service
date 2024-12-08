<?php

declare(strict_types=1);

namespace Project\Bootloader;

use Core\Bootloader\BootloaderInterface;
use Core\Config\Attributes\Config;
use ReflectionClass;
use SplFileInfo;

readonly class ConfigBootloader implements BootloaderInterface
{
    public function __construct(
        private string $configNamespace = 'Config',
        private string $generatedDir = __DIR__ . '/../../config/generated/'
    ) {
    }

    public function init(): void
    {
        try {
            $classes = $this->scanDirectoryForClasses($this->generatedDir, $this->configNamespace);

            foreach ($classes as $class) {
                if (!class_exists($class)) {
                    continue;
                }

                $ref = new ReflectionClass($class);
                $attrs = $ref->getAttributes(Config::class);

                if (!empty($attrs)) {
                    /** @var Config $attrInstance */
                    $attrInstance = $attrs[0]->newInstance();
                    $interface = $attrInstance->interface;
                    $implementation = $class;
                    container()->scoped($interface, fn() => new $implementation());
                }
            }
        } catch (\Throwable $exception) {
            throw new \RuntimeException(message: 'Init config failed', code: 0, previous: $exception);
        }
    }

    private function scanDirectoryForClasses(string $dir, string $namespace): array
    {
        $classes = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $relativePath = str_replace($dir, '', $file->getPathname());
                $className = $this->filenameToClass($relativePath, $namespace);
                $classes[] = $className;
            }
        }
        return $classes;
    }

    private function filenameToClass(string $filePath, string $baseNamespace): string
    {
        $path = trim($filePath, DIRECTORY_SEPARATOR);
        $path = str_replace('/', '\\', $path);
        $path = str_replace('\\', '\\', $path);
        $path = preg_replace('/\.php$/', '', $path);
        return $baseNamespace . '\\' . ltrim($path, '\\');
    }
}