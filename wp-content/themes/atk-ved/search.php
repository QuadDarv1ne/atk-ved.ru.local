<?php get_header(); ?>

<section class="search-results-section">
    <div class="container">
        <?php echo atk_ved_breadcrumbs(); ?>
        
        <div class="search-header">
            <h1 class="search-title">
                <?php
                printf(
                    'Результаты поиска: <span class="search-query">%s</span>',
                    get_search_query()
                );
                ?>
            </h1>
            <?php if (have_posts()) : ?>
                <p class="search-count">Найдено результатов: <?php echo $wp_query->found_posts; ?></p>
            <?php endif; ?>
        </div>
        
        <div class="search-content">
            <?php if (have_posts()) : ?>
                <div class="search-results">
                    <?php while (have_posts()) : the_post(); ?>
                        <article class="search-result-item">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="search-result-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <div class="search-result-content">
                                <h2 class="search-result-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                
                                <div class="search-result-meta">
                                    <span class="post-type"><?php echo get_post_type_object(get_post_type())->labels->singular_name; ?></span>
                                    <span class="post-date"><?php echo get_the_date(); ?></span>
                                </div>
                                
                                <div class="search-result-excerpt">
                                    <?php the_excerpt(); ?>
                                </div>
                                
                                <a href="<?php the_permalink(); ?>" class="read-more">
                                    Подробнее →
                                </a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                    
                    <?php
                    the_posts_pagination(array(
                        'mid_size' => 2,
                        'prev_text' => '← Назад',
                        'next_text' => 'Вперед →',
                    ));
                    ?>
                </div>
            <?php else : ?>
                <div class="no-results">
                    <div class="no-results-icon">🔍</div>
                    <h2>Ничего не найдено</h2>
                    <p>По вашему запросу ничего не найдено. Попробуйте изменить поисковый запрос.</p>
                    
                    <div class="search-form-wrapper">
                        <?php get_search_form(); ?>
                    </div>
                    
                    <div class="suggestions">
                        <h3>Возможно, вас заинтересует:</h3>
                        <ul>
                            <li><a href="<?php echo home_url('/#services'); ?>">Наши услуги</a></li>
                            <li><a href="<?php echo home_url('/#delivery'); ?>">Способы доставки</a></li>
                            <li><a href="<?php echo home_url('/#faq'); ?>">Часто задаваемые вопросы</a></li>
                            <li><a href="<?php echo home_url('/#contact'); ?>">Связаться с нами</a></li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
