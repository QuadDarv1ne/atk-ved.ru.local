<?php

declare(strict_types=1);

namespace ATKVed\Contracts;

/**
 * Интерфейс для представлений (views)
 *
 * @package ATKVed\Contracts
 * @since   3.5.0
 */
interface ViewInterface
{
    /**
     * Рендеринг представления
     *
     * @param array<string, mixed> $data Данные для представления
     * @return string
     */
    public function render(array $data = []): string;

    /**
     * Отображение представления
     *
     * @param array<string, mixed> $data Данные для представления
     * @return void
     */
    public function display(array $data = []): void;
}
