<?php
/**
 * Reusable CTA Section Component
 *
 * @package ATK_VED
 * @since 3.6.0
 * 
 * @var string $title CTA title
 * @var string $description CTA description (optional)
 * @var string $button_text Button text
 * @var string $button_url Button URL
 * @var string $button_style Button style: 'primary', 'secondary' (default: 'primary')
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

$title = $data['title'] ?? 'Готовы начать сотрудничество?';
$description = $data['description'] ?? 'Получите бесплатную консультацию и расчёт стоимости доставки';
$button_text = $data['button_text'] ?? 'Связаться с нами';
$button_url = $data['button_url'] ?? home_url('/contacts/');
$button_style = $data['button_style'] ?? 'primary';
?>

<section class="cta-section">
    <div class="container">
        <div class="cta-section__content">
            <h2><?php echo esc_html($title); ?></h2>
            <?php if (!empty($description)): ?>
                <p><?php echo esc_html($description); ?></p>
            <?php endif; ?>
            <a href="<?php echo esc_url($button_url); ?>" class="btn btn-<?php echo esc_attr($button_style); ?> btn-lg">
                <?php echo esc_html($button_text); ?>
            </a>
        </div>
    </div>
</section>
