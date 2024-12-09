<?php

declare(strict_types=1);

namespace Core\PHPStan\Rules;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;

/**
 * @implements Rule<Node\Stmt\Class_>
 */
class StatelessRule implements Rule
{
    private ReflectionProvider $reflectionProvider;

    public function __construct(ReflectionProvider $reflectionProvider)
    {
        $this->reflectionProvider = $reflectionProvider;
    }

    public function getNodeType(): string
    {
        return Node\Stmt\Class_::class;
    }

    /**
     * @throws ShouldNotHappenException
     * @return array<RuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (!isset($node->namespacedName)) {
            return [];
        }
        $className = (string) $node->namespacedName;

        if (!$this->reflectionProvider->hasClass($className)) {
            return [];
        }

        $classReflection = $this->reflectionProvider->getClass($className);

        $attributes = $classReflection->getNativeReflection()->getAttributes(\Core\Container\Attributes\Stateless::class);
        if (empty($attributes)) {
            return [];
        }

        $properties = $classReflection->getNativeReflection()->getProperties();
        if (count($properties) > 0) {
            return [
                RuleErrorBuilder::message("Class {$className} is marked as Stateless but has properties.")->build(),
            ];
        }

        return [];
    }
}