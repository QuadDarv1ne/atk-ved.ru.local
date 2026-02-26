<?php
/**
 * Template Name: Чистый минималистичный дизайн
 * Template Post Type: page
 * 
 * Шаблон с чистым дизайном на основе макета
 * 
 * @package ATK_VED
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

get_header();
?>

<main id="main-content" class="clean-design-page">
    <?php get_template_part('template-parts/clean-landing'); ?>
</main>

<?php
get_footer();
