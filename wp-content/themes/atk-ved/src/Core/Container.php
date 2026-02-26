<?php

declare(strict_types=1);

namespace ATKVed\Core;

use RuntimeException;

/**
 * Простой DI контейнер для управления зависимостями
 *
 * @package ATKVed\Core
 * @since   3.5.0
 */
final class Container
{
    /**
     * Зарегистрированные сервисы
     *
     * @var array<string, callable>
     */
    private array $services = [];

    /**
     * Синглтон экземпляры
     *
     * @var array<string, object>
     */
    private array $instances = [];

    /**
     * Регистрация сервиса
     *
     * @param string   $id       Идентификатор сервиса
     * @param callable $factory  Фабрика для создания сервиса
     * @param bool     $shared   Создавать как синглтон
     * @return void
     */
    public function register(string $id, callable $factory, bool $shared = true): void
    {
        $this->services[$id] = [
            'factory' => $factory,
            'shared'  => $shared,
        ];
    }

    /**
     * Получение сервиса
     *
     * @param string $id Идентификатор сервиса
     * @return mixed
     * @throws RuntimeException
     */
    public function get(string $id): mixed
    {
        if (!isset($this->services[$id])) {
            throw new RuntimeException("Service '{$id}' not found in container");
        }

        $service = $this->services[$id];

        // Если синглтон и уже создан - возвращаем
        if ($service['shared'] && isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        // Создаём новый экземпляр
        $instance = $service['factory']($this);

        // Сохраняем если синглтон
        if ($service['shared']) {
            $this->instances[$id] = $instance;
        }

        return $instance;
    }

    /**
     * Проверка наличия сервиса
     *
     * @param string $id Идентификатор сервиса
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }
}
