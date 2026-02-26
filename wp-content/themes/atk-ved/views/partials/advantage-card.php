<?php

declare(strict_types=1);

/**
 * Частичное представление: Карточка преимущества
 *
 * @package ATKVed
 * @since   3.5.0
 *
 * @var array{
 *     icon: string,
 *     title: string,
 *     description: string
 * } $data
 */

use ATKVed\Core\View;

?>

<div class="advantage-card-enhanced">
    <span class="adv-icon"><?php echo View::escape($data['icon']); ?></span>
    <h3><?php echo View::escape($data['title']); ?></h3>
    <p><?php echo View::escape($data['description']); ?></p>
</div>
