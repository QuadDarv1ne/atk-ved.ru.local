<?php
/**
 * Шаблон расширенного калькулятора
 * 
 * @package ATK_VED
 */

if (!defined('ABSPATH')) exit;
?>

<div class="advanced-calculator" data-nonce="<?php echo esc_attr($nonce); ?>">
    <div class="calculator-container">
        <h2 class="calculator-main-title"><?php echo esc_html($atts['title']); ?></h2>
        <p class="calculator-subtitle">Получите точный расчет с учетом таможенных платежей</p>
        
        <form id="advancedCalculatorForm" class="advanced-calc-form">
            <!-- Основные параметры -->
            <div class="calc-section">
                <h3 class="calc-section-title">
                    <span class="calc-step">1</span>
                    Параметры груза
                </h3>
                
                <div class="calc-grid">
                    <div class="calc-field">
                        <label for="adv_weight">
                            Вес груза (кг) <span class="required">*</span>
                        </label>
                        <input type="number" id="adv_weight" name="weight" 
                               min="0.1" step="0.1" required
                               placeholder="Например: 500">
                        <small class="field-hint">Фактический вес вашего груза</small>
                    </div>
                    
                    <div class="calc-field">
                        <label for="adv_volume">
                            Объем (м³)
                        </label>
                        <input type="number" id="adv_volume" name="volume" 
                               min="0" step="0.01"
                               placeholder="Например: 2.5">
                        <small class="field-hint">Длина × Ширина × Высота в метрах</small>
                    </div>
                </div>
            </div>
            
            <!-- Товар и стоимость -->
            <div class="calc-section">
                <h3 class="calc-section-title">
                    <span class="calc-step">2</span>
                    Информация о товаре
                </h3>
                
                <div class="calc-grid">
                    <div class="calc-field">
                        <label for="adv_category">
                            Категория товара <span class="required">*</span>
                        </label>
                        <select id="adv_category" name="category" required>
                            <?php foreach ($categories as $key => $cat): ?>
                            <option value="<?php echo esc_attr($key); ?>">
                                <?php echo esc_html($cat['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="field-hint category-examples">
                            <?php echo esc_html($categories['electronics']['examples']); ?>
                        </small>
                    </div>
                    
                    <div class="calc-field">
                        <label for="adv_value">
                            Стоимость товара (₽) <span class="required">*</span>
                        </label>
                        <input type="number" id="adv_value" name="product_value" 
                               min="1" step="1" required
                               placeholder="Например: 100000">
                        <small class="field-hint">Для расчета таможенных платежей</small>
                    </div>
                </div>
            </div>
            
            <!-- Маршрут -->
            <div class="calc-section">
                <h3 class="calc-section-title">
                    <span class="calc-step">3</span>
                    Маршрут доставки
                </h3>
                
                <div class="calc-grid">
                    <div class="calc-field">
                        <label for="adv_from">Откуда</label>
                        <select id="adv_from" name="from_city">
                            <option value="Пекин">Пекин</option>
                            <option value="Шанхай">Шанхай</option>
                            <option value="Гуанчжоу">Гуанчжоу</option>
                            <option value="Шэньчжэнь">Шэньчжэнь</option>
                            <option value="Иу">Иу</option>
                            <option value="Чэнду">Чэнду</option>
                            <option value="Урумчи">Урумчи</option>
                        </select>
                    </div>
                    
                    <div class="calc-field">
                        <label for="adv_to">Куда</label>
                        <select id="adv_to" name="to_city">
                            <option value="Москва">Москва</option>
                            <option value="Санкт-Петербург">Санкт-Петербург</option>
                            <option value="Владивосток">Владивосток</option>
                            <option value="Екатеринбург">Екатеринбург</option>
                            <option value="Новосибирск">Новосибирск</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Способ доставки -->
            <div class="calc-section">
                <h3 class="calc-section-title">
                    <span class="calc-step">4</span>
                    Способ доставки
                </h3>
                
                <div class="delivery-methods">
                    <label class="method-card">
                        <input type="radio" name="method" value="all" checked>
                        <div class="method-content">
                            <div class="method-icon">🌐</div>
                            <div class="method-name">Все варианты</div>
                            <div class="method-desc">Сравнить все способы</div>
                        </div>
                    </label>
                    
                    <label class="method-card">
                        <input type="radio" name="method" value="air">
                        <div class="method-content">
                            <div class="method-icon">✈️</div>
                            <div class="method-name">Авиа</div>
                            <div class="method-desc">5-10 дней</div>
                        </div>
                    </label>
                    
                    <label class="method-card">
                        <input type="radio" name="method" value="sea">
                        <div class="method-content">
                            <div class="method-icon">🚢</div>
                            <div class="method-name">Море</div>
                            <div class="method-desc">35-45 дней</div>
                        </div>
                    </label>
                    
                    <label class="method-card">
                        <input type="radio" name="method" value="rail">
                        <div class="method-content">
                            <div class="method-icon">🚂</div>
                            <div class="method-name">Ж/Д</div>
                            <div class="method-desc">18-28 дней</div>
                        </div>
                    </label>
                    
                    <label class="method-card">
                        <input type="radio" name="method" value="auto">
                        <div class="method-content">
                            <div class="method-icon">🚛</div>
                            <div class="method-name">Авто</div>
                            <div class="method-desc">12-18 дней</div>
                        </div>
                    </label>
                </div>
            </div>
            
            <!-- Дополнительные услуги -->
            <div class="calc-section">
                <h3 class="calc-section-title">
                    <span class="calc-step">5</span>
                    Дополнительные услуги
                </h3>
                
                <label class="calc-checkbox-card">
                    <input type="checkbox" name="insurance" value="1">
                    <div class="checkbox-content">
                        <div class="checkbox-icon">🛡️</div>
                        <div class="checkbox-text">
                            <div class="checkbox-title">Страхование груза</div>
                            <div class="checkbox-desc">3% от стоимости товара</div>
                        </div>
                    </div>
                </label>
            </div>
            
            <!-- Кнопки -->
            <div class="calc-actions">
                <button type="submit" class="calc-btn calc-btn-primary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 11l3 3L22 4"/>
                        <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                    </svg>
                    <span>Рассчитать стоимость</span>
                </button>
                
                <button type="reset" class="calc-btn calc-btn-secondary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 12a9 9 0 019-9 9.75 9.75 0 016.74 2.74L21 8"/>
                        <path d="M21 3v5h-5"/>
                    </svg>
                    <span>Сбросить</span>
                </button>
            </div>
        </form>
        
        <!-- Результаты -->
        <div id="calculatorResults" class="calc-results" style="display: none;">
            <div class="results-header">
                <h3>Результаты расчета</h3>
                <button id="exportPdfBtn" class="calc-btn calc-btn-export">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    <span>Скачать PDF</span>
                </button>
            </div>
            
            <div id="resultsContent" class="results-content"></div>
        </div>
        
        <!-- Загрузка -->
        <div id="calculatorLoader" class="calc-loader" style="display: none;">
            <div class="loader-spinner"></div>
            <p>Рассчитываем стоимость...</p>
        </div>
        
        <!-- Ошибка -->
        <div id="calculatorError" class="calc-error" style="display: none;"></div>
    </div>
</div>

<script>
// Обновление примеров при выборе категории
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('adv_category');
    const examplesHint = document.querySelector('.category-examples');
    
    const categoryExamples = <?php echo json_encode(array_map(function($cat) {
        return $cat['examples'];
    }, $categories)); ?>;
    
    if (categorySelect && examplesHint) {
        categorySelect.addEventListener('change', function() {
            const selectedCategory = this.value;
            if (categoryExamples[selectedCategory]) {
                examplesHint.textContent = categoryExamples[selectedCategory];
            }
        });
    }
});
</script>
