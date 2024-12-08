<?php

declare(strict_types=1);

namespace Core\Config\Generator\FileGen;

use Core\Config\Generator\Model\ConfigStructure;
use Core\Config\Generator\Util\TemplateRenderer;

final class InterfaceGenerator
{
    public function __construct(private TemplateRenderer $renderer) {}

    public function generate(ConfigStructure $structure, string $fileName, string $classNamespace): string
    {
        $interfaceMethods = "";
        $uses = [];

        foreach ($structure->properties as $prop) {
            if ($prop->voClass !== null) {
                $fullyQualifiedClassName = $classNamespace . '\\'.$structure->className . '\\VO\\' . $prop->voClass . '\\' . $prop->voClass;
                $uses[$fullyQualifiedClassName] = $fullyQualifiedClassName;
            }

            $interfaceMethods .= $this->renderInterfaceMethod($prop->type, ucfirst($prop->name));
        }

        $usesSection = $this->buildUseSection($uses);

        $templatePath = __DIR__ . '/../Templates/interface_template.php';
        return $this->renderer->render($templatePath, [
            'namespace' => $classNamespace,
            'uses' => $usesSection,
            'interfaceName' => $structure->InterfaceName,
            'properties' => rtrim($interfaceMethods),
            'yamlFileName' => $fileName,
        ]);
    }

    private function renderInterfaceMethod(string $type, string $methodName): string
    {
        $template = require __DIR__ . '/../Templates/Parts/interface_method_template.php';
        return str_replace(['{type}', '{methodName}'], [$type, $methodName], $template) . "\n";
    }

    private function buildUseSection(array $uses): string
    {
        $result = "";
        foreach ($uses as $useClass) {
            $result .= "use {$useClass};\n";
        }
        return $result ? "\n" . $result : '';
    }
}
