<?php

declare(strict_types=1);

/**
 * Частичное представление: Карточка услуги
 *
 * @package ATKVed
 * @since   3.5.0
 *
 * @var array{
 *     number: string,
 *     icon: string,
 *     title: string,
 *     description: string,
 *     link: string,
 *     delay: int
 * } $data
 */

use ATKVed\Core\View;

?>

<article class="service-card-enhanced" style="--delay: <?php echo (int) $data['delay']; ?>ms">
    <div class="service-icon"><?php echo View::escape($data['icon']); ?></div>
    <span class="service-number"><?php echo View::escape($data['number']); ?></span>
    <h3 class="service-title"><?php echo View::escape($data['title']); ?></h3>
    <p class="service-desc"><?php echo View::escape($data['description']); ?></p>
    <a href="<?php echo View::escapeUrl($data['link']); ?>" class="service-btn">
        <?php _e('Заказать', 'atk-ved'); ?>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M5 12h14M12 5l7 7-7 7"/>
        </svg>
    </a>
</article>
