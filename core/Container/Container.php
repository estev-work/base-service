<?php

declare(strict_types=1);

namespace Core\Container;

use Core\Container\Exceptions\NotFoundException;

class Container implements ContainerInterface
{
    /**
     * @var array<class-string, callable|class-string>
     */
    protected array $bindings = [];
    /**
     * @var array<class-string, mixed|null>
     */
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

    /**
     * @param class-string $id
     * @return mixed
     * @throws NotFoundException
     */
    public function get(string $id): mixed
    {
        if ($service = $this->scopeManager->get($id)) {
            return $service;
        }

        if(!$this->has($id)) {
            throw new NotFoundException("Service {$id} not found");
        }
        return $this->resolve($id);
    }

    /**
     * @param class-string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->bindings[$id]) || isset($this->instances[$id]);
    }

    public function make(string $abstract, array $parameters = []): mixed
    {
        if(!$this->bindings[$abstract]) {
            throw new NotFoundException("Cannot resolve  {$abstract}");
        }
        return $this->resolve($abstract, $parameters);
    }

    /**
     * @param class-string $abstract
     * @param array<class-string, mixed> $parameters
     * @return mixed
     */
    protected function resolve(string $abstract, array $parameters = []): mixed
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