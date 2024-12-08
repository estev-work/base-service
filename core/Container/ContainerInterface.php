<?php

namespace Core\Container;

use Psr\Container\ContainerInterface as PsrContainerInterface;

interface ContainerInterface extends PsrContainerInterface
{
    /**
     * Регистрация зависимости (singleton или обычная).
     *
     * @param string $abstract
     * @param callable|string $concrete
     * @return void
     */
    public function bind(string $abstract, callable|string $concrete): void;

    /**
     * Регистрация singleton.
     *
     * @param string $abstract
     * @param callable|string $concrete
     * @return void
     */
    public function singleton(string $abstract, callable|string $concrete): void;

    /**
     * Регистрация временных (scoped) зависимостей.
     *
     * @param string $abstract
     * @param callable|string $concrete
     * @return void
     */
    public function scoped(string $abstract, callable|string $concrete): void;

    /**
     * Создание нового экземпляра.
     *
     * @param string $abstract
     * @param array $parameters
     * @return object
     */
    public function make(string $abstract, array $parameters = []): object;

    /**
     * Сброс временных (scoped) сервисов.
     *
     * @return void
     */
    public function resetScoped(): void;
}