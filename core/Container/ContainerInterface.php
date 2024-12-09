<?php

namespace Core\Container;

use Core\Container\Exceptions\NotFoundException;
use Psr\Container\ContainerInterface as PsrContainerInterface;

interface ContainerInterface extends PsrContainerInterface
{
    /**
     * Регистрация зависимости (singleton или обычная).
     *
     * @param class-string $abstract
     * @param callable|class-string $concrete
     * @return void
     */
    public function bind(string $abstract, callable|string $concrete): void;

    /**
     * Регистрация singleton.
     *
     * @param class-string $abstract
     * @param callable|class-string $concrete
     * @return void
     */
    public function singleton(string $abstract, callable|string $concrete): void;

    /**
     * Регистрация временных (scoped) зависимостей.
     *
     * @param class-string $abstract
     * @param callable|class-string $concrete
     * @return void
     */
    public function scoped(string $abstract, callable|string $concrete): void;

    /**
     * Создание нового экземпляра.
     *
     * @param class-string $abstract
     * @param array<class-string, mixed> $parameters
     * @return mixed
     * @throws NotFoundException
     */
    public function make(string $abstract, array $parameters = []): mixed;

    /**
     * Сброс временных (scoped) сервисов.
     *
     * @return void
     */
    public function resetScoped(): void;

    /**
     * Удаление временных (scoped) сервисов.
     *
     * @param class-string $abstract
     * @return void
     */
    public function removeScope(string $abstract): void;
}