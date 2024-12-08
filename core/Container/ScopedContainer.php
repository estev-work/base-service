<?php

declare(strict_types=1);

namespace Core\Container;

class ScopedContainer
{
    private ContainerInterface $container;
    private array $scopedIds = [];

    public function __construct()
    {
        $this->container = container();
    }

    public function make(string $abstract, array $parameters = []): object
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