<?php

declare(strict_types=1);

/**
 * Частичное представление: Hero секция
 *
 * @package ATKVed
 * @since   3.5.0
 *
 * @var array{
 *     title: string,
 *     subtitle: string,
 *     badges: array<int, array{icon: string, text: string}>,
 *     cta_primary: array{text: string, url: string},
 *     cta_secondary: array{text: string, url: string},
 *     marketplaces: array<int, string>
 * } $data
 */

use ATKVed\Core\View;

?>

<section class="hero-section-enhanced">
    <div class="hero-overlay"></div>
    <div class="container">
        <div class="hero-content-enhanced">
            <div class="hero-text-enhanced">
                
                <?php if (!empty($data['badges'])): ?>
                <div class="hero-badges">
                    <?php foreach ($data['badges'] as $badge): ?>
                        <span class="badge">
                            <?php echo esc_html($badge['icon']); ?>
                            <?php echo View::escape($badge['text']); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <h1 class="hero-title">
                    <?php echo wp_kses_post($data['title']); ?>
                </h1>

                <p class="hero-subtitle">
                    <?php echo View::escape($data['subtitle']); ?>
                </p>

                <div class="hero-cta">
                    <a href="<?php echo View::escapeUrl($data['cta_primary']['url']); ?>" 
                       class="btn btn-primary btn-lg">
                        <?php echo View::escape($data['cta_primary']['text']); ?>
                    </a>
                    <a href="<?php echo View::escapeUrl($data['cta_secondary']['url']); ?>" 
                       class="btn btn-outline btn-lg">
                        <?php echo View::escape($data['cta_secondary']['text']); ?>
                    </a>
                </div>

                <?php if (!empty($data['marketplaces'])): ?>
                <div class="marketplaces-hero">
                    <span class="mp-label"><?php _e('Работаем с:', 'atk-ved'); ?></span>
                    <div class="marketplace-logos">
                        <?php foreach ($data['marketplaces'] as $marketplace): ?>
                            <div class="mp-logo"><?php echo View::escape($marketplace); ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
    
    <div class="scroll-indicator">
        <span class="scroll-text"><?php _e('Листайте вниз', 'atk-ved'); ?></span>
        <div class="scroll-arrow">↓</div>
    </div>
</section>
