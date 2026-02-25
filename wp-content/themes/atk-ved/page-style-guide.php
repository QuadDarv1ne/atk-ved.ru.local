<?php
/**
 * Interactive Style Guide Page
 * Интерактивный гид по стилям темы
 * 
 * Template Name: Style Guide
 * 
 * @package ATK_VED
 * @since 3.1.0
 */

get_header();
?>

<div class="style-guide-wrapper">
    <aside class="style-guide-nav">
        <h3>Содержание</h3>
        <ul>
            <li><a href="#colors">Цвета</a></li>
            <li><a href="#typography">Типографика</a></li>
            <li><a href="#buttons">Кнопки</a></li>
            <li><a href="#forms">Формы</a></li>
            <li><a href="#cards">Карточки</a></li>
            <li><a href="#alerts">Уведомления</a></li>
            <li><a href="#animations">Анимации</a></li>
            <li><a href="#components">Компоненты</a></li>
        </ul>
    </aside>
    
    <main class="style-guide-content">
        <header class="style-guide-header">
            <h1>Style Guide АТК ВЭД</h1>
            <p>Документация по стилям и компонентам темы v3.1.0</p>
        </header>
        
        <!-- Colors Section -->
        <section id="colors" class="style-guide-section">
            <h2>🎨 Цветовая палитра</h2>
            
            <h3>Основные цвета</h3>
            <div class="color-grid">
                <div class="color-card">
                    <div class="color-preview" style="background: #e31e24;"></div>
                    <div class="color-info">
                        <strong>Primary</strong>
                        <code>#e31e24</code>
                        <code>rgb(227, 30, 36)</code>
                    </div>
                </div>
                <div class="color-card">
                    <div class="color-preview" style="background: #c01a1f;"></div>
                    <div class="color-info">
                        <strong>Primary Dark</strong>
                        <code>#c01a1f</code>
                    </div>
                </div>
                <div class="color-card">
                    <div class="color-preview" style="background: #ff4d4f;"></div>
                    <div class="color-info">
                        <strong>Primary Light</strong>
                        <code>#ff4d4f</code>
                    </div>
                </div>
            </div>
            
            <h3>Нейтральные цвета</h3>
            <div class="color-grid">
                <?php
                $grays = array(
                    '50' => '#fafafa',
                    '100' => '#f5f5f5',
                    '200' => '#eeeeee',
                    '300' => '#e0e0e0',
                    '400' => '#bdbdbd',
                    '500' => '#9e9e9e',
                    '600' => '#757575',
                    '700' => '#616161',
                    '800' => '#424242',
                    '900' => '#2c2c2c',
                );
                
                foreach ($grays as $key => $value): ?>
                <div class="color-card">
                    <div class="color-preview" style="background: <?php echo $value; ?>;"></div>
                    <div class="color-info">
                        <strong>Gray <?php echo $key; ?></strong>
                        <code><?php echo $value; ?></code>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <h3>Семантические цвета</h3>
            <div class="color-grid">
                <div class="color-card">
                    <div class="color-preview" style="background: #4CAF50;"></div>
                    <div class="color-info">
                        <strong>Success</strong>
                        <code>#4CAF50</code>
                    </div>
                </div>
                <div class="color-card">
                    <div class="color-preview" style="background: #FF9800;"></div>
                    <div class="color-info">
                        <strong>Warning</strong>
                        <code>#FF9800</code>
                    </div>
                </div>
                <div class="color-card">
                    <div class="color-preview" style="background: #F44336;"></div>
                    <div class="color-info">
                        <strong>Error</strong>
                        <code>#F44336</code>
                    </div>
                </div>
                <div class="color-card">
                    <div class="color-preview" style="background: #2196F3;"></div>
                    <div class="color-info">
                        <strong>Info</strong>
                        <code>#2196F3</code>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Typography Section -->
        <section id="typography" class="style-guide-section">
            <h2>📝 Типографика</h2>
            
            <h3>Заголовки</h3>
            <div class="typography-examples">
                <h1>Heading 1 - 60px</h1>
                <h2>Heading 2 - 48px</h2>
                <h3>Heading 3 - 36px</h3>
                <h4>Heading 4 - 30px</h4>
                <h5>Heading 5 - 24px</h5>
                <h6>Heading 6 - 20px</h6>
            </div>
            
            <h3>Основной текст</h3>
            <div class="typography-examples">
                <p class="text-xl">Extra Large text - 20px</p>
                <p class="text-lg">Large text - 18px</p>
                <p class="text-base">Base text - 16px</p>
                <p class="text-sm">Small text - 14px</p>
                <p class="text-xs">Extra Small text - 12px</p>
            </div>
            
            <h3>Начертание</h3>
            <div class="typography-examples">
                <p class="font-light">Light font weight - 300</p>
                <p class="font-normal">Normal font weight - 400</p>
                <p class="font-medium">Medium font weight - 500</p>
                <p class="font-semibold">Semibold font weight - 600</p>
                <p class="font-bold">Bold font weight - 700</p>
            </div>
        </section>
        
        <!-- Buttons Section -->
        <section id="buttons" class="style-guide-section">
            <h2>🔘 Кнопки</h2>
            
            <h3>Основные стили</h3>
            <div class="component-grid">
                <button class="btn-modern btn-primary-modern">Primary</button>
                <button class="btn-modern btn-gradient-modern">Gradient</button>
                <button class="btn-modern btn-glass-modern">Glass</button>
                <button class="btn-modern">Default</button>
            </div>
            
            <h3>Размеры</h3>
            <div class="component-grid">
                <button class="btn-modern btn-modern-sm">Small</button>
                <button class="btn-modern">Medium</button>
                <button class="btn-modern btn-modern-lg">Large</button>
            </div>
            
            <h3>С иконками</h3>
            <div class="component-grid">
                <button class="btn-modern btn-primary-modern btn-icon-modern">
                    С иконкой
                    <span class="btn-icon">→</span>
                </button>
                <button class="btn-modern btn-primary-modern">
                    <span>📦</span>
                    С эмодзи
                </button>
            </div>
            
            <h3>Состояния</h3>
            <div class="component-grid">
                <button class="btn-modern btn-primary-modern">Normal</button>
                <button class="btn-modern btn-primary-modern" disabled>Disabled</button>
                <button class="btn-modern btn-primary-modern btn-loading-modern">
                    <span class="btn-text">Loading</span>
                    <span class="btn-loader">
                        <span></span><span></span><span></span>
                    </span>
                </button>
            </div>
        </section>
        
        <!-- Forms Section -->
        <section id="forms" class="style-guide-section">
            <h2>📋 Формы</h2>
            
            <h3>Поля ввода</h3>
            <div class="form-example">
                <div class="form-group-modern">
                    <label>Обычное поле</label>
                    <input type="text" class="input-modern" placeholder="Введите текст">
                </div>
                
                <div class="form-group-modern input-floating-modern">
                    <input type="text" class="input-modern" placeholder=" " required>
                    <label>Плавающий лейбл</label>
                </div>
                
                <div class="form-group-modern input-icon-modern">
                    <input type="email" class="input-modern" placeholder="Email">
                    <span class="input-icon">✉️</span>
                </div>
            </div>
            
            <h3>Чекбоксы и радиокнопки</h3>
            <div class="form-example">
                <label class="checkbox-modern">
                    <input type="checkbox" checked>
                    <span>Чекбокс</span>
                </label>
                
                <label class="radio-modern">
                    <input type="radio" name="radio" checked>
                    <span>Радиокнопка 1</span>
                </label>
                
                <label class="radio-modern">
                    <input type="radio" name="radio">
                    <span>Радиокнопка 2</span>
                </label>
            </div>
        </section>
        
        <!-- Cards Section -->
        <section id="cards" class="style-guide-section">
            <h2>🃏 Карточки</h2>
            
            <div class="component-grid">
                <div class="card-modern">
                    <div class="card-content-modern">
                        <h3 class="card-title-modern">Обычная карточка</h3>
                        <p class="card-description-modern">Описание карточки</p>
                    </div>
                </div>
                
                <div class="card-modern card-image-modern">
                    <span class="card-badge-modern">New</span>
                    <img src="https://via.placeholder.com/400x250" alt="">
                    <div class="card-content-modern">
                        <h3 class="card-title-modern">С изображением</h3>
                        <p class="card-description-modern">Карточка с картинкой</p>
                    </div>
                </div>
                
                <div class="card-modern card-glass-modern">
                    <div class="card-content-modern">
                        <h3 class="card-title-modern">Стеклянная</h3>
                        <p class="card-description-modern">С эффектом стекла</p>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Alerts Section -->
        <section id="alerts" class="style-guide-section">
            <h2>🔔 Уведомления</h2>
            
            <div class="alert-modern alert-modern-success">
                <span class="alert-icon-modern">✓</span>
                <div class="alert-content-modern">
                    <div class="alert-title-modern">Успешно!</div>
                    <div class="alert-message-modern">Данные успешно сохранены</div>
                </div>
                <button class="alert-close-modern">×</button>
            </div>
            
            <div class="alert-modern alert-modern-warning">
                <span class="alert-icon-modern">⚠️</span>
                <div class="alert-content-modern">
                    <div class="alert-title-modern">Внимание!</div>
                    <div class="alert-message-modern">Требуется ваше внимание</div>
                </div>
                <button class="alert-close-modern">×</button>
            </div>
            
            <div class="alert-modern alert-modern-error">
                <span class="alert-icon-modern">✕</span>
                <div class="alert-content-modern">
                    <div class="alert-title-modern">Ошибка!</div>
                    <div class="alert-message-modern">Произошла ошибка при сохранении</div>
                </div>
                <button class="alert-close-modern">×</button>
            </div>
            
            <div class="alert-modern alert-modern-info">
                <span class="alert-icon-modern">ℹ️</span>
                <div class="alert-content-modern">
                    <div class="alert-title-modern">Информация</div>
                    <div class="alert-message-modern">Полезная информация</div>
                </div>
                <button class="alert-close-modern">×</button>
            </div>
        </section>
        
        <!-- Animations Section -->
        <section id="animations" class="style-guide-section">
            <h2>✨ Анимации</h2>
            
            <h3>Fade анимации</h3>
            <div class="component-grid">
                <div class="animate-fade-in" style="padding: 20px; background: #f0f0f0;">Fade In</div>
                <div class="animate-fade-in-up" style="padding: 20px; background: #f0f0f0;">Fade In Up</div>
                <div class="animate-fade-in-down" style="padding: 20px; background: #f0f0f0;">Fade In Down</div>
            </div>
            
            <h3>Scale анимации</h3>
            <div class="component-grid">
                <div class="animate-scale-in" style="padding: 20px; background: #f0f0f0;">Scale In</div>
                <div class="animate-pulse" style="padding: 20px; background: #f0f0f0;">Pulse</div>
            </div>
            
            <h3>Bounce анимации</h3>
            <div class="component-grid">
                <div class="animate-bounce" style="padding: 20px; background: #f0f0f0;">Bounce</div>
                <div class="animate-bounce-in" style="padding: 20px; background: #f0f0f0;">Bounce In</div>
            </div>
            
            <h3>Другие анимации</h3>
            <div class="component-grid">
                <div class="animate-shake" style="padding: 20px; background: #f0f0f0;">Shake</div>
                <div class="animate-wobble" style="padding: 20px; background: #f0f0f0;">Wobble</div>
                <div class="animate-heartbeat" style="padding: 20px; background: #f0f0f0;">Heartbeat</div>
                <div class="animate-glow" style="padding: 20px; background: #f0f0f0;">Glow</div>
            </div>
        </section>
        
        <!-- Components Section -->
        <section id="components" class="style-guide-section">
            <h2>🧩 Компоненты</h2>
            
            <h3>Бейджи</h3>
            <div class="component-grid">
                <span class="badge-modern badge-modern-primary">Primary</span>
                <span class="badge-modern badge-modern-success">Success</span>
                <span class="badge-modern badge-modern-warning">Warning</span>
                <span class="badge-modern badge-modern-info">Info</span>
            </div>
            
            <h3>Аватары</h3>
            <div class="component-grid">
                <div class="avatar-modern avatar-modern-sm">
                    <img src="https://via.placeholder.com/35" alt="">
                </div>
                <div class="avatar-modern">
                    <img src="https://via.placeholder.com/50" alt="">
                </div>
                <div class="avatar-modern avatar-modern-lg">
                    <img src="https://via.placeholder.com/80" alt="">
                </div>
            </div>
            
            <h3>Прогресс бары</h3>
            <div style="max-width: 500px;">
                <div class="progress-modern">
                    <div class="progress-fill-modern" style="width: 25%;"></div>
                </div>
                <br>
                <div class="progress-modern">
                    <div class="progress-fill-modern" style="width: 50%;"></div>
                </div>
                <br>
                <div class="progress-modern">
                    <div class="progress-fill-modern" style="width: 75%;"></div>
                </div>
                <br>
                <div class="progress-modern progress-modern-striped">
                    <div class="progress-fill-modern" style="width: 100%;"></div>
                </div>
            </div>
        </section>
    </main>
</div>

<style>
.style-guide-wrapper {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 40px;
    padding: 40px;
    max-width: 1400px;
    margin: 0 auto;
}

.style-guide-nav {
    position: sticky;
    top: 100px;
    background: var(--color-white);
    padding: 25px;
    border-radius: var(--radius-xl);
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    height: fit-content;
}

.style-guide-nav h3 {
    margin-bottom: 20px;
    font-size: 18px;
}

.style-guide-nav ul {
    list-style: none;
    padding: 0;
}

.style-guide-nav li {
    margin-bottom: 12px;
}

.style-guide-nav a {
    color: var(--color-gray-700);
    text-decoration: none;
    transition: color var(--duration-fast);
}

.style-guide-nav a:hover {
    color: var(--color-primary);
}

.style-guide-content {
    background: var(--color-white);
    padding: 40px;
    border-radius: var(--radius-xl);
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.style-guide-header {
    margin-bottom: 40px;
    padding-bottom: 30px;
    border-bottom: 2px solid var(--color-gray-100);
}

.style-guide-section {
    margin-bottom: 60px;
    scroll-margin-top: 40px;
}

.style-guide-section h2 {
    font-size: 32px;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 3px solid var(--color-primary);
    display: inline-block;
}

.style-guide-section h3 {
    font-size: 22px;
    margin: 30px 0 20px;
}

.color-grid,
.component-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.color-card {
    border-radius: var(--radius-lg);
    overflow: hidden;
    border: 1px solid var(--color-gray-200);
}

.color-preview {
    height: 100px;
}

.color-info {
    padding: 15px;
}

.color-info code {
    display: block;
    font-size: 12px;
    color: var(--color-gray-600);
    margin-top: 5px;
}

.typography-examples {
    margin: 20px 0;
}

.form-example {
    max-width: 500px;
    margin: 20px 0;
}

@media (max-width: 1024px) {
    .style-guide-wrapper {
        grid-template-columns: 1fr;
    }
    
    .style-guide-nav {
        position: static;
    }
}
</style>

<?php get_footer(); ?>
