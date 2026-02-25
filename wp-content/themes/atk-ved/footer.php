<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-col">
                <h3>АТК ВЭД</h3>
                <p>Товары для маркетплейсов из Китая оптом</p>
            </div>
            <div class="footer-col">
                <h3>Услуги</h3>
                <ul>
                    <li><a href="#services">Поиск товаров</a></li>
                    <li><a href="#services">Проверка качества</a></li>
                    <li><a href="#delivery">Доставка</a></li>
                    <li><a href="#services">Таможня</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Информация</h3>
                <ul>
                    <li><a href="#steps">Как мы работаем</a></li>
                    <li><a href="#faq">Вопросы и ответы</a></li>
                    <li><a href="#reviews">Отзывы</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Контакты</h3>
                <p>Телефон: <?php echo get_theme_mod('atk_ved_phone', '+7 (XXX) XXX-XX-XX'); ?></p>
                <p>Email: <?php echo get_theme_mod('atk_ved_email', 'info@atk-ved.ru'); ?></p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> АТК ВЭД. Все права защищены.</p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
