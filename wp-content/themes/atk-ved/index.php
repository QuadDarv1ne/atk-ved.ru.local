<?php
/**
 * Index Template - Блог и архивы
 *
 * @package ATK_VED
 */

defined('ABSPATH') || exit;

get_header();
?>

<main id="main-content" class="blog-page">

    <div class="container">
        
        <?php if (have_posts()) : ?>

            <header class="blog-header">
                <?php
                if (is_home() && !is_front_page()) :
                    ?>
                    <h1 class="blog-title"><?php single_post_title(); ?></h1>
                    <?php
                elseif (is_archive()) :
                    the_archive_title('<h1 class="blog-title">', '</h1>');
                    the_archive_description('<div class="archive-description">', '</div>');
                else :
                    ?>
                    <h1 class="blog-title"><?php esc_html_e('Блог', 'atk-ved'); ?></h1>
                    <?php
                endif;
                ?>
            </header>
            </header>

            <div class="blog-grid">
                <?php
                while (have_posts()) :
                    the_post();
                    ?>
                    
                    <article id="post-<?php the_ID(); ?>" <?php post_class('blog-card'); ?>>
                        
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="blog-card__thumbnail">
                                <a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                                    <?php the_post_thumbnail('medium', [
                                        'loading' => 'lazy',
                                        'alt' => get_the_title(),
                                    ]); ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="blog-card__content">
                            
                            <div class="blog-card__meta">
                                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6"/>
                                        <line x1="8" y1="2" x2="8" y2="6"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                    <?php echo esc_html(get_the_date()); ?>
                                </time>
                                <?php
                                $categories = get_the_category();
                                if ($categories) :
                                    ?>
                                    <span class="blog-card__category">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                                            <line x1="7" y1="7" x2="7.01" y2="7"/>
                                        </svg>
                                        <?php echo esc_html($categories[0]->name); ?>
                                    </span>
                                    <?php
                                endif;
                                ?>
                            </div>

                            <h2 class="blog-card__title">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h2>

                            <div class="blog-card__excerpt">
                                <?php the_excerpt(); ?>
                            </div>

                            <a href="<?php the_permalink(); ?>" class="blog-card__link">
                                <?php esc_html_e('Читать далее', 'atk-ved'); ?>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </a>

                        </div>

                    </article>

                    <?php
                endwhile;
                ?>
            </div>

            <?php
            // Пагинация
            the_posts_pagination([
                'mid_size' => 2,
                'prev_text' => __('← Назад', 'atk-ved'),
                'next_text' => __('Вперёд →', 'atk-ved'),
                'screen_reader_text' => __('Навигация по записям', 'atk-ved'),
            ]);
            ?>

        <?php else : ?>

            <div class="no-results">
                <h1 class="page-title"><?php esc_html_e('Ничего не найдено', 'atk-ved'); ?></h1>
                
                <?php if (is_search()) : ?>
                    <p><?php esc_html_e('По вашему запросу ничего не найдено. Попробуйте изменить поисковый запрос.', 'atk-ved'); ?></p>
                    <?php get_search_form(); ?>
                <?php else : ?>
                    <p><?php esc_html_e('Записей пока нет.', 'atk-ved'); ?></p>
                <?php endif; ?>
            </div>

        <?php endif; ?>

    </div>

</main>

<?php
get_footer();
