<?php

declare(strict_types=1);

namespace Core\Config\Generator\FileGen;

use Core\Config\Generator\Model\ValueObjectDefinition;
use Core\Config\Generator\Util\Assignment;
use Core\Config\Generator\Util\TemplateRenderer;

final readonly class ValueObjectGenerator
{
    public function __construct(private TemplateRenderer $renderer) {}

    public function generate(ValueObjectDefinition $vo, string $fileName, string $baseVoNamespace, string $baseVoDir): void
    {
        $voClassName = $vo->className;
        $voDir = $baseVoDir . '/' . $voClassName;
        if (!is_dir($voDir)) {
            mkdir($voDir, 0777, true);
        }

        $voNamespace = $baseVoNamespace . '\\' . $voClassName;

        $propertiesCode = "";
        $assignmentsCode = "";
        $gettersCode = "";
        $uses = [];

        foreach ($vo->properties as $prop) {
            $propertiesCode .= $this->renderProperty($prop->type, $prop->name) . "\n";
            $assignment = new Assignment()->generateAssignment($prop);
            $assignmentsCode .= $this->renderConstructorAssignment($prop->name, $assignment) . "\n";

            if ($prop->voClass !== null) {
                $fullyQualifiedClassName = $baseVoNamespace . '\\' . $prop->voClass . '\\' . $prop->voClass;
                $uses[$fullyQualifiedClassName] = $fullyQualifiedClassName;
            }

            $gettersCode .= $this->renderGetter($prop->type, ucfirst($prop->name), $prop->name) . "\n";
        }

        $usesSection = $this->buildUseSection($uses);

        $templatePath = __DIR__ . '/../Templates/value_object_template.php';
        $voCode = $this->renderer->render($templatePath, [
            'namespace' => $voNamespace,
            'uses' => rtrim($usesSection),
            'className' => $voClassName,
            'properties' => rtrim($propertiesCode),
            'constructorAssignments' => rtrim($assignmentsCode),
            'getters' => rtrim($gettersCode),
            'yamlFileName' => $fileName,
        ]);

        file_put_contents($voDir . '/' . $voClassName . '.php', $voCode);
    }

    private function renderProperty(string $type, string $name): string
    {
        $template = require __DIR__ . '/../Templates/Parts/property_template.php';
        return str_replace(['{type}', '{name}'], [$type, $name], $template);
    }

    private function renderConstructorAssignment(string $propertyName, string $assignment): string
    {
        $template = require __DIR__ . '/../Templates/Parts/constructor_assignment_template.php';
        return str_replace(['{propertyName}', '{assignment}'], [$propertyName, $assignment], $template);
    }

    private function renderGetter(string $type, string $methodName, string $propertyName): string
    {
        $template = require __DIR__ . '/../Templates/Parts/getter_template.php';
        return str_replace(['{type}', '{methodName}', '{propertyName}'], [$type, $methodName, $propertyName], $template);
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