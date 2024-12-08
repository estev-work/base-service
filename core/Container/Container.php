<?php

declare(strict_types=1);

namespace Core\Container;

use Core\Container\Exceptions\ContainerException;
use Core\Container\Exceptions\NotFoundException;

class Container implements ContainerInterface
{
    protected array $bindings = [];
    protected array $instances = [];
    protected ScopeManager $scopeManager;

    public function __construct()
    {
        $this->scopeManager = new ScopeManager();
    }

    public function bind(string $abstract, callable|string $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    public function singleton(string $abstract, callable|string $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
        $this->instances[$abstract] = null;
    }
    public function scoped(string $abstract, callable|string $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }
    public function get(string $id)
    {
        if ($service = $this->scopeManager->get($id)) {
            return $service;
        }

        if(!$this->has($id)) {
            throw new NotFoundException("Service {$id} not found");
        }
        return $this->resolve($id);
    }

    public function has(string $id): bool
    {
        return isset($this->bindings[$id]) || isset($this->instances[$id]);
    }

    /**
     * @throws NotFoundException
     * @throws ContainerException
     */
    public function make(string $abstract, array $parameters = []): object
    {
        if(!$this->bindings[$abstract]) {
            throw new NotFoundException("Cannot resolve  {$abstract}");
        }
        return $this->resolve($abstract, $parameters);
    }

    /**
     * @throws ContainerException
     */
    protected function resolve(string $abstract, array $parameters = []): object
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        $concrete = $this->bindings[$abstract];

        $object = is_callable($concrete)
            ? $concrete($this, ...$parameters)
            : new $concrete(...$parameters);

        if (array_key_exists($abstract, $this->instances)) {
            $this->instances[$abstract] = $object;
        } elseif (isset($this->scopeManager)) {
            $this->scopeManager->add($abstract, $object);
        }

        return $object;
    }

    public function resetScoped(): void
    {
        $this->scopeManager->clear();
    }

    public function removeScope(string $abstract): void
    {
        $this->scopeManager->remove($abstract);
    }
}