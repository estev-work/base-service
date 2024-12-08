<?php

declare(strict_types=1);

namespace Core\Container;

class ScopeManager
{
    private array $scopedServices = [];

    public function add(string $abstract, object $service): void
    {
        $this->scopedServices[$abstract] = $service;
    }

    public function get(string $abstract): ?object
    {
        return $this->scopedServices[$abstract] ?? null;
    }

    public function clear(): void
    {
        $this->scopedServices = [];
    }

    public function remove(string $abstract): void
    {
        unset($this->scopedServices[$abstract]);
    }
}