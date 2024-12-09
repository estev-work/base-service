<?php

declare(strict_types=1);

namespace Core\Config\Generator;

use Core\Config\Generator\FileGen\ClassGenerator;
use Core\Config\Generator\FileGen\InterfaceGenerator;
use Core\Config\Generator\FileGen\ValueObjectGenerator;
use Core\Config\Generator\Parser\YamlStructureParser;
use Core\Config\Generator\Util\DirectoryManager;
use Core\Config\Generator\Util\TemplateRenderer;
use Symfony\Component\Yaml\Yaml;

final readonly class YamlConfigGenerator
{
    public function __construct(
        private string $yamlDir,
        private string $outputDir,
        private string $baseNamespace
    ) {
    }

    public function generate(): void
    {
        $dirManager = new DirectoryManager();
        $dirManager->clearDirectory($this->outputDir);

        $renderer = new TemplateRenderer();
        $parser = new YamlStructureParser();
        $interfaceGen = new InterfaceGenerator($renderer);
        $classGen = new ClassGenerator($renderer);
        $voGen = new ValueObjectGenerator($renderer);

        /** @var list<string>|false $files */
        $files = glob($this->yamlDir . '/*.yaml');

        if (!$files) return;

        /** @var string $filePath */
        foreach ($files as $filePath) {
            $filename = pathinfo($filePath, PATHINFO_FILENAME);

            /** @var array<string, mixed> $data */
            $data = Yaml::parseFile($filePath);

            $structure = $parser->parse($data, $filename);

            $className = $structure->className;
            $interfaceName = $structure->InterfaceName;
            $classNamespace = $this->baseNamespace . '\\' . $className;
            $interfaceNamespace = $this->baseNamespace;
            $interfaceOutputDir = $this->outputDir;
            $classOutputDir = $this->outputDir . '/' . $className;
            if (!is_dir($classOutputDir)) {
                mkdir($classOutputDir, 0777, true);
            }

            $interfaceCode = $interfaceGen->generate($structure, $filename, $interfaceNamespace);
            file_put_contents($interfaceOutputDir . '/' . $interfaceName . '.php', $interfaceCode);

            $voBaseDir = $classOutputDir . '/VO';
            if (!is_dir($voBaseDir)) {
                mkdir($voBaseDir, 0777, true);
            }

            foreach ($structure->valueObjects as $vo) {
                $voGen->generate(vo: $vo,
                    fileName: $filename,
                    baseVoNamespace: $classNamespace . '\\VO',
                    baseVoDir: $voBaseDir
                );
            }

            $classCode = $classGen->generate($structure, $filename, $classNamespace, $interfaceNamespace, $interfaceName);
            file_put_contents($classOutputDir . '/' . $className . '.php', $classCode);
        }
    }
}
