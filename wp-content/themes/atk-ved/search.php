<?php
/**
 * Search Results Template
 *
 * @package ATK_VED
 */

defined('ABSPATH') || exit;

get_header();
?>

<main id="main-content" class="search-page">
    <div class="container">
        
        <header class="search-header">
            <h1 class="search-title">
                <?php
                printf(
                    esc_html__('Результаты поиска: %s', 'atk-ved'),
                    '<span class="search-query">' . esc_html(get_search_query()) . '</span>'
                );
                ?>
            </h1>
            <?php if (have_posts()) : ?>
                <p class="search-count">
                    <?php
                    global $wp_query;
                    printf(
                        esc_html(_n('Найден %s результат', 'Найдено %s результатов', $wp_query->found_posts, 'atk-ved')),
                        '<strong>' . number_format_i18n($wp_query->found_posts) . '</strong>'
                    );
                    ?>
                </p>
            <?php endif; ?>
        </header>
        
        <?php if (have_posts()) : ?>
            
            <div class="search-results">
                <?php while (have_posts()) : the_post(); ?>
                    
                    <article id="post-<?php the_ID(); ?>" <?php post_class('search-result-item'); ?>>
                        
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="search-result-thumbnail">
                                <a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                                    <?php the_post_thumbnail('medium', ['loading' => 'lazy']); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="search-result-content">
                            
                            <div class="search-result-meta">
                                <span class="post-type">
                                    <?php echo esc_html(get_post_type_object(get_post_type())->labels->singular_name); ?>
                                </span>
                                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                    <?php echo esc_html(get_the_date()); ?>
                                </time>
                            </div>
                            
                            <h2 class="search-result-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            
                            <div class="search-result-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            
                            <a href="<?php the_permalink(); ?>" class="search-result-link">
                                <?php _e('Подробнее', 'atk-ved'); ?>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </a>
                            
                        </div>
                        
                    </article>
                    
                <?php endwhile; ?>
            </div>
            
            <?php
            the_posts_pagination([
                'mid_size' => 2,
                'prev_text' => __('← Назад', 'atk-ved'),
                'next_text' => __('Вперёд →', 'atk-ved'),
                'screen_reader_text' => __('Навигация по результатам', 'atk-ved'),
            ]);
            ?>
            
        <?php else : ?>
            
            <div class="no-results">
                <div class="no-results-icon">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                </div>
                <h2><?php _e('Ничего не найдено', 'atk-ved'); ?></h2>
                <p><?php _e('По вашему запросу ничего не найдено. Попробуйте изменить поисковый запрос.', 'atk-ved'); ?></p>
                
                <div class="search-form-wrapper">
                    <?php get_search_form(); ?>
                </div>
                
                <div class="suggestions">
                    <h3><?php _e('Возможно, вас заинтересует:', 'atk-ved'); ?></h3>
                    <ul>
                        <li><a href="<?php echo esc_url(home_url('/services/')); ?>"><?php _e('Наши услуги', 'atk-ved'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/about/')); ?>"><?php _e('О компании', 'atk-ved'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/contacts/')); ?>"><?php _e('Контакты', 'atk-ved'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/')); ?>"><?php _e('Главная страница', 'atk-ved'); ?></a></li>
                    </ul>
                </div>
            </div>
            
        <?php endif; ?>
        
    </div>
</main>

<?php
get_footer();
