<?php
/**
 * Reusable Section Header Component
 *
 * @package ATK_VED
 * @since 3.6.0
 * 
 * @var string $title Section title
 * @var string $subtitle Section subtitle (optional)
 * @var string $align Text alignment: 'left', 'center', 'right' (default: 'center')
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

$title = $data['title'] ?? '';
$subtitle = $data['subtitle'] ?? '';
$align = $data['align'] ?? 'center';

if (empty($title)) {
    return;
}
?>

<div class="section-header section-header--<?php echo esc_attr($align); ?>">
    <h2 class="section-title"><?php echo esc_html($title); ?></h2>
    <?php if (!empty($subtitle)): ?>
        <p class="section-subtitle"><?php echo esc_html($subtitle); ?></p>
    <?php endif; ?>
</div>
