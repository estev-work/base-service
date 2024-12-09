<?php

declare(strict_types=1);

namespace Core\Container;

class ScopeManager
{
    /**
     * @var array<class-string, mixed>
     */
    private array $scopedServices = [];

    /**
     * @param class-string $abstract
     * @param mixed $service
     * @return void
     */
    public function add(string $abstract, mixed $service): void
    {
        $this->scopedServices[$abstract] = $service;
    }

    /**
     * @param class-string $abstract
     * @return mixed|null
     */
    public function get(string $abstract): mixed
    {
        return $this->scopedServices[$abstract] ?? null;
    }

    public function clear(): void
    {
        $this->scopedServices = [];
    }

    /**
     * @param class-string $abstract
     * @return void
     */
    public function remove(string $abstract): void
    {
        unset($this->scopedServices[$abstract]);
    }
}