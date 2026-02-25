<?php get_header(); ?>

<section class="error-404-section">
    <div class="container">
        <div class="error-404-content">
            <div class="error-404-number">404</div>
            <h1 class="error-404-title">Страница не найдена</h1>
            <p class="error-404-text">К сожалению, запрашиваемая страница не существует или была удалена.</p>
            
            <div class="error-404-actions">
                <a href="<?php echo home_url('/'); ?>" class="cta-button">
                    Вернуться на главную
                </a>
                <a href="#contact" class="cta-button secondary">
                    Связаться с нами
                </a>
            </div>
            
            <div class="error-404-search">
                <h3>Попробуйте найти нужную информацию:</h3>
                <?php get_search_form(); ?>
            </div>
            
            <div class="error-404-links">
                <h3>Полезные ссылки:</h3>
                <ul>
                    <li><a href="<?php echo home_url('/#services'); ?>">Наши услуги</a></li>
                    <li><a href="<?php echo home_url('/#delivery'); ?>">Доставка</a></li>
                    <li><a href="<?php echo home_url('/#faq'); ?>">Часто задаваемые вопросы</a></li>
                    <li><a href="<?php echo home_url('/#contact'); ?>">Контакты</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
