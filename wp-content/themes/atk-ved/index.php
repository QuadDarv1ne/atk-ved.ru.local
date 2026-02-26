<?php
/**
 * Index Template - Блог и архивы
 *
 * @package ATK_VED
 */

defined('ABSPATH') || exit;

// ВРЕМЕННАЯ ОТЛАДКА
error_log('INDEX.PHP LOADED');
error_log('is_home: ' . (is_home() ? 'yes' : 'no'));
error_log('is_front_page: ' . (is_front_page() ? 'yes' : 'no'));
error_log('have_posts: ' . (have_posts() ? 'yes' : 'no'));

get_header();
?>

<main id="main-content" class="site-main">

    <div class="container">
        
        <?php if (have_posts()) : ?>

            <header class="page-header">
                <?php
                if (is_home() && !is_front_page()) :
                    ?>
                    <h1 class="page-title"><?php single_post_title(); ?></h1>
                    <?php
                elseif (is_archive()) :
                    the_archive_title('<h1 class="page-title">', '</h1>');
                    the_archive_description('<div class="archive-description">', '</div>');
                elseif (is_search()) :
                    ?>
                    <h1 class="page-title">
                        <?php
                        printf(
                            esc_html__('Результаты поиска: %s', 'atk-ved'),
                            '<span>' . get_search_query() . '</span>'
                        );
                        ?>
                    </h1>
                    <?php
                else :
                    ?>
                    <h1 class="page-title"><?php esc_html_e('Блог', 'atk-ved'); ?></h1>
                    <?php
                endif;
                ?>
            </header>

            <div class="posts-grid">
                <?php
                while (have_posts()) :
                    the_post();
                    ?>
                    
                    <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
                        
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-card__thumbnail">
                                <a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                                    <?php the_post_thumbnail('medium', [
                                        'loading' => 'lazy',
                                        'alt' => get_the_title(),
                                    ]); ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="post-card__content">
                            
                            <div class="post-card__meta">
                                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                    <?php echo esc_html(get_the_date()); ?>
                                </time>
                                <?php
                                $categories = get_the_category();
                                if ($categories) :
                                    ?>
                                    <span class="post-card__category">
                                        <?php echo esc_html($categories[0]->name); ?>
                                    </span>
                                    <?php
                                endif;
                                ?>
                            </div>

                            <h2 class="post-card__title">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h2>

                            <div class="post-card__excerpt">
                                <?php the_excerpt(); ?>
                            </div>

                            <a href="<?php the_permalink(); ?>" class="post-card__link">
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
