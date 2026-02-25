<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-col">
                <h3>АТК ВЭД</h3>
                <p>Товары для маркетплейсов из Китая оптом. Полный цикл работы от поиска до доставки.</p>
                <?php if (get_theme_mod('atk_ved_whatsapp') || get_theme_mod('atk_ved_telegram') || get_theme_mod('atk_ved_vk')) : ?>
                <div class="social-links">
                    <?php if (get_theme_mod('atk_ved_whatsapp')) : ?>
                        <a href="<?php echo esc_url(get_theme_mod('atk_ved_whatsapp')); ?>" target="_blank" rel="noopener" class="social-link" aria-label="WhatsApp">
                            <span class="social-icon">📱</span>
                        </a>
                    <?php endif; ?>
                    <?php if (get_theme_mod('atk_ved_telegram')) : ?>
                        <a href="<?php echo esc_url(get_theme_mod('atk_ved_telegram')); ?>" target="_blank" rel="noopener" class="social-link" aria-label="Telegram">
                            <span class="social-icon">✈️</span>
                        </a>
                    <?php endif; ?>
                    <?php if (get_theme_mod('atk_ved_vk')) : ?>
                        <a href="<?php echo esc_url(get_theme_mod('atk_ved_vk')); ?>" target="_blank" rel="noopener" class="social-link" aria-label="VK">
                            <span class="social-icon">🔵</span>
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="footer-col">
                <h3>Услуги</h3>
                <ul>
                    <li><a href="#services">Поиск товаров</a></li>
                    <li><a href="#services">Проверка качества</a></li>
                    <li><a href="#delivery">Доставка</a></li>
                    <li><a href="#services">Таможня</a></li>
                    <li><a href="#services">Складская логистика</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Информация</h3>
                <ul>
                    <li><a href="#steps">Как мы работаем</a></li>
                    <li><a href="#faq">Вопросы и ответы</a></li>
                    <li><a href="#reviews">Отзывы</a></li>
                    <li><a href="#contact">Контакты</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Контакты</h3>
                <ul class="footer-contacts">
                    <li>
                        <span class="contact-icon">📞</span>
                        <a href="tel:<?php echo esc_attr(str_replace([' ', '(', ')', '-'], '', get_theme_mod('atk_ved_phone', '+7 (XXX) XXX-XX-XX'))); ?>">
                            <?php echo esc_html(get_theme_mod('atk_ved_phone', '+7 (XXX) XXX-XX-XX')); ?>
                        </a>
                    </li>
                    <li>
                        <span class="contact-icon">✉️</span>
                        <a href="mailto:<?php echo esc_attr(get_theme_mod('atk_ved_email', 'info@atk-ved.ru')); ?>">
                            <?php echo esc_html(get_theme_mod('atk_ved_email', 'info@atk-ved.ru')); ?>
                        </a>
                    </li>
                    <?php if (get_theme_mod('atk_ved_address')) : ?>
                    <li>
                        <span class="contact-icon">📍</span>
                        <span><?php echo esc_html(get_theme_mod('atk_ved_address')); ?></span>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> АТК ВЭД. Все права защищены.</p>
            <p class="footer-developer">Разработано с <?php echo esc_html__('заботой', 'atk-ved'); ?> о вашем бизнесе</p>
        </div>
    </div>
</footer>

<!-- Кнопка "Наверх" -->
<button class="scroll-to-top" id="scrollToTop" aria-label="<?php echo esc_attr__('Вернуться наверх', 'atk-ved'); ?>" style="display: none;">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M18 15l-6-6-6 6"/>
    </svg>
</button>

<?php wp_footer(); ?>
</body>
</html>
