<?php

declare(strict_types=1);

namespace Core\Container;

use Core\Container\Exceptions\NotFoundException;

class ScopedContainer
{
    private ContainerInterface $container;

    /**
     * @var class-string[]
     */
    private array $scopedIds = [];

    public function __construct()
    {
        $this->container = container();
    }

    /**
     * @param class-string $abstract
     * @param array<class-string, callable|mixed> $parameters
     * @return mixed
     * @throws NotFoundException
     */
    public function make(string $abstract, array $parameters = []): mixed
    {
        $object = $this->container->make($abstract, $parameters);
        $this->scopedIds[] = $abstract;
        return $object;
    }

    public function __destruct()
    {
        foreach ($this->scopedIds as $id) {
            $this->container->removeScope($id);
        }
    }
}