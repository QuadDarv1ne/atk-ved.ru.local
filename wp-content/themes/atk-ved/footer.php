<footer class="site-footer modern-footer">
    <div class="footer-top">
        <div class="container">
            <div class="footer-newsletter">
                <div class="newsletter-content">
                    <h3>Получайте лучшие предложения</h3>
                    <p>Подпишитесь на нашу рассылку и получайте эксклюзивные скидки и новости</p>
                </div>
                <form class="newsletter-form">
                    <div class="form-group">
                        <input type="email" placeholder="Ваш email" required>
                        <button type="submit" class="cta-button">Подписаться</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="footer-main">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col company-info">
                    <div class="logo">
                        <img src="<?php echo get_template_directory_uri(); ?>/images/logo-white.png" alt="АТК ВЭД" loading="lazy">
                    </div>
                    <p class="company-description">Товары для маркетплейсов из Китая оптом. Полный цикл работы от поиска до доставки с гарантией качества.</p>
                    
                    <div class="trust-badges">
                        <div class="badge">
                            <span class="badge-icon">🏆</span>
                            <span class="badge-text">5 лет на рынке</span>
                        </div>
                        <div class="badge">
                            <span class="badge-icon">🚚</span>
                            <span class="badge-text">1000+ доставок</span>
                        </div>
                        <div class="badge">
                            <span class="badge-icon">⭐</span>
                            <span class="badge-text">4.9/5 рейтинг</span>
                        </div>
                    </div>
                </div>
                
                <div class="footer-col services-links">
                    <h3 class="footer-title">Услуги</h3>
                    <ul class="footer-links">
                        <li><a href="#services">Поиск и подбор товаров</a></li>
                        <li><a href="#services">Проверка качества</a></li>
                        <li><a href="#services">Консолидация грузов</a></li>
                        <li><a href="#delivery">Международная доставка</a></li>
                        <li><a href="#services">Таможенное оформление</a></li>
                        <li><a href="#services">Складская логистика</a></li>
                    </ul>
                </div>
                
                <div class="footer-col company-links">
                    <h3 class="footer-title">Компания</h3>
                    <ul class="footer-links">
                        <li><a href="#about">О нас</a></li>
                        <li><a href="#steps">Как мы работаем</a></li>
                        <li><a href="#reviews">Отзывы клиентов</a></li>
                        <li><a href="#faq">Вопросы и ответы</a></li>
                        <li><a href="#blog">Блог</a></li>
                        <li><a href="#contact">Контакты</a></li>
                    </ul>
                </div>
                
                <div class="footer-col contact-info">
                    <h3 class="footer-title">Контакты</h3>
                    <div class="contact-details">
                        <?php if (get_theme_mod('atk_ved_phone')) : ?>
                        <div class="contact-item">
                            <span class="contact-icon">📞</span>
                            <div class="contact-text">
                                <span class="contact-label">Телефон</span>
                                <a href="tel:<?php echo esc_attr(str_replace([' ', '(', ')', '-'], '', get_theme_mod('atk_ved_phone'))); ?>">
                                    <?php echo esc_html(get_theme_mod('atk_ved_phone')); ?>
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (get_theme_mod('atk_ved_email')) : ?>
                        <div class="contact-item">
                            <span class="contact-icon">✉️</span>
                            <div class="contact-text">
                                <span class="contact-label">Email</span>
                                <a href="mailto:<?php echo esc_attr(get_theme_mod('atk_ved_email')); ?>">
                                    <?php echo esc_html(get_theme_mod('atk_ved_email')); ?>
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (get_theme_mod('atk_ved_address')) : ?>
                        <div class="contact-item">
                            <span class="contact-icon">📍</span>
                            <div class="contact-text">
                                <span class="contact-label">Адрес</span>
                                <span><?php echo esc_html(get_theme_mod('atk_ved_address')); ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (get_theme_mod('atk_ved_whatsapp') || get_theme_mod('atk_ved_telegram') || get_theme_mod('atk_ved_vk')) : ?>
                    <div class="social-section">
                        <h4>Мы в соцсетях</h4>
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
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="footer-bottom">
        <div class="container">
            <div class="footer-bottom-content">
                <div class="copyright">
                    <p>&copy; <?php echo date('Y'); ?> АТК ВЭД. Все права защищены.</p>
                    <div class="legal-links">
                        <a href="/privacy-policy">Политика конфиденциальности</a>
                        <a href="/terms-of-service">Условия использования</a>
                    </div>
                </div>
                <div class="payment-methods">
                    <span class="payment-label">Мы принимаем:</span>
                    <div class="payment-icons">
                        <span class="payment-icon">💳</span>
                        <span class="payment-icon">💰</span>
                        <span class="payment-icon">🏦</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Современная кнопка "Наверх" -->
<div class="scroll-to-top-container">
    <button class="scroll-to-top modern" id="scrollToTop" aria-label="<?php echo esc_attr__('Вернуться наверх', 'atk-ved'); ?>">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 15l-6-6-6 6"/>
        </svg>
        <span class="progress-ring">
            <svg viewBox="0 0 36 36">
                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                      fill="none" stroke="currentColor" stroke-width="2" stroke-dasharray="100, 100"/>
            </svg>
        </span>
    </button>
</div>

<?php wp_footer(); ?>
</body>
</html>
