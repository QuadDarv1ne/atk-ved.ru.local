<?php
/**
 * Reusable Stat Card Component
 *
 * @package ATK_VED
 * @since 3.6.0
 * 
 * @var string $number Stat number
 * @var string $label Stat label
 * @var string $icon Icon class (optional)
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

$number = $data['number'] ?? '';
$label = $data['label'] ?? '';
$icon = $data['icon'] ?? '';

if (empty($number) || empty($label)) {
    return;
}
?>

<div class="stat-card">
    <?php if (!empty($icon)): ?>
        <div class="stat-card__icon">
            <i class="<?php echo esc_attr($icon); ?>"></i>
        </div>
    <?php endif; ?>
    <div class="stat-card__number"><?php echo esc_html($number); ?></div>
    <div class="stat-card__label"><?php echo esc_html($label); ?></div>
</div>
