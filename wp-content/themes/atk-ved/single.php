<?php
/**
 * Single Post Template
 *
 * @package ATK_VED
 */

defined('ABSPATH') || exit;

get_header();
?>

<main id="main-content" class="single-post">
    <div class="container">
        <?php
        while (have_posts()) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('post-article'); ?>>
                
                <header class="post-header">
                    <div class="post-meta">
                        <time class="post-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            <?php echo esc_html(get_the_date()); ?>
                        </time>
                        <?php if (has_category()) : ?>
                            <span class="post-categories">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                                    <line x1="7" y1="7" x2="7.01" y2="7"/>
                                </svg>
                                <?php the_category(', '); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <h1 class="post-title"><?php the_title(); ?></h1>
                </header>
                
                <?php if (has_post_thumbnail()) : ?>
                    <div class="post-thumbnail">
                        <?php the_post_thumbnail('large', ['class' => 'post-image']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="post-content">
                    <?php the_content(); ?>
                </div>

                <?php if (has_tag()) : ?>
                    <footer class="post-footer">
                        <div class="post-tags">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                                <line x1="7" y1="7" x2="7.01" y2="7"/>
                            </svg>
                            <?php the_tags('', ' '); ?>
                        </div>
                    </footer>
                <?php endif; ?>

            </article>

            <?php
            // Navigation between posts
            $prev_post = get_previous_post();
            $next_post = get_next_post();
            if ($prev_post || $next_post) :
            ?>
                <nav class="post-navigation" aria-label="<?php esc_attr_e('Навигация по записям', 'atk-ved'); ?>">
                    <?php if ($prev_post) : ?>
                        <a href="<?php echo esc_url(get_permalink($prev_post)); ?>" class="nav-link nav-prev">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 12H5M12 19l-7-7 7-7"/>
                            </svg>
                            <span>
                                <small><?php _e('Предыдущая запись', 'atk-ved'); ?></small>
                                <strong><?php echo esc_html(get_the_title($prev_post)); ?></strong>
                            </span>
                        </a>
                    <?php endif; ?>
                    <?php if ($next_post) : ?>
                        <a href="<?php echo esc_url(get_permalink($next_post)); ?>" class="nav-link nav-next">
                            <span>
                                <small><?php _e('Следующая запись', 'atk-ved'); ?></small>
                                <strong><?php echo esc_html(get_the_title($next_post)); ?></strong>
                            </span>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>

        <?php endwhile; ?>
    </div>
</main>

<?php
get_footer();
