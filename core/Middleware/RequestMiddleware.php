<?php

declare(strict_types=1);

namespace Core\Middleware;

use Core\Container\ScopeManager;

class RequestMiddleware
{
    private ScopeManager $scopeManager;

    public function __construct(ScopeManager $scopeManager)
    {
        $this->scopeManager = $scopeManager;
    }

    public function handle(callable $next): mixed
    {
        $this->scopeManager->clear();

        return $next();
    }
}