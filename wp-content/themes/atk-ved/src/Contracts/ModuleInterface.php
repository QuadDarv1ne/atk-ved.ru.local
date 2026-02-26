<?php

declare(strict_types=1);

namespace ATKVed\Contracts;

/**
 * Интерфейс для модулей темы
 *
 * @package ATKVed\Contracts
 * @since   3.5.0
 */
interface ModuleInterface
{
    /**
     * Инициализация модуля
     *
     * @return void
     */
    public function init(): void;

    /**
     * Регистрация хуков WordPress
     *
     * @return void
     */
    public function registerHooks(): void;
}
