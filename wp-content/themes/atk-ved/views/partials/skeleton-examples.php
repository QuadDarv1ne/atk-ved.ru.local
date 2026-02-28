<?php
/**
 * Примеры использования Skeleton Loaders
 *
 * @package ATK_VED
 * @since 3.6.0
 */

defined('ABSPATH') || exit;
?>

<!-- Пример 1: Скелетон для карточки услуги -->
<div class="skeleton-service-card">
    <div class="skeleton skeleton-icon"></div>
    <div class="skeleton skeleton-title"></div>
    <div class="skeleton skeleton-text"></div>
    <div class="skeleton skeleton-text short"></div>
</div>

<!-- Пример 2: Скелетон для этапа работы -->
<div class="skeleton-process-step">
    <div class="skeleton skeleton-badge"></div>
    <div class="skeleton skeleton-icon"></div>
    <div class="skeleton skeleton-title"></div>
    <div class="skeleton skeleton-text"></div>
    <div class="skeleton skeleton-text medium"></div>
</div>

<!-- Пример 3: Скелетон для FAQ -->
<div class="skeleton-faq-item">
    <div class="skeleton skeleton-title"></div>
</div>

<!-- Пример 4: Сетка скелетонов услуг -->
<div class="skeleton-grid">
    <?php for ($i = 0; $i < 6; $i++): ?>
    <div class="skeleton-service-card">
        <div class="skeleton skeleton-icon"></div>
        <div class="skeleton skeleton-title"></div>
        <div class="skeleton skeleton-text"></div>
        <div class="skeleton skeleton-text short"></div>
    </div>
    <?php endfor; ?>
</div>

<!-- Пример 5: Список скелетонов -->
<div class="skeleton-list">
    <?php for ($i = 0; $i < 5; $i++): ?>
    <div class="skeleton-list-item">
        <div class="skeleton skeleton-avatar"></div>
        <div style="flex: 1;">
            <div class="skeleton skeleton-text medium"></div>
            <div class="skeleton skeleton-text short"></div>
        </div>
    </div>
    <?php endfor; ?>
</div>

<!-- Пример 6: Обертка с автоматическим переключением -->
<div class="skeleton-wrapper loading" id="services-wrapper">
    <!-- Скелетоны (показываются при loading) -->
    <div class="skeleton-grid">
        <?php for ($i = 0; $i < 6; $i++): ?>
        <div class="skeleton-service-card">
            <div class="skeleton skeleton-icon"></div>
            <div class="skeleton skeleton-title"></div>
            <div class="skeleton skeleton-text"></div>
            <div class="skeleton skeleton-text short"></div>
        </div>
        <?php endfor; ?>
    </div>
    
    <!-- Реальный контент (показывается при loaded) -->
    <div class="services-grid-enhanced">
        <!-- Контент загружается через AJAX -->
    </div>
</div>

<script>
// Пример использования с AJAX
document.addEventListener('DOMContentLoaded', () => {
    const wrapper = document.getElementById('services-wrapper');
    
    // Загрузка данных
    fetch('/wp-json/atk-ved/v1/services')
        .then(response => response.json())
        .then(data => {
            // Заполнить контент
            const grid = wrapper.querySelector('.services-grid-enhanced');
            grid.innerHTML = data.map(service => `
                <article class="service-card-enhanced">
                    <div class="service-icon">${service.icon}</div>
                    <h3 class="service-title">${service.title}</h3>
                    <p class="service-desc">${service.excerpt}</p>
                </article>
            `).join('');
            
            // Переключить на loaded
            wrapper.classList.remove('loading');
            wrapper.classList.add('loaded');
        })
        .catch(error => {
            console.error('Error loading services:', error);
            wrapper.classList.remove('loading');
        });
});
</script>

<!-- Пример 7: Скелетон для формы -->
<div class="skeleton-card">
    <div class="skeleton-form-group">
        <div class="skeleton skeleton-label"></div>
        <div class="skeleton skeleton-input"></div>
    </div>
    <div class="skeleton-form-group">
        <div class="skeleton skeleton-label"></div>
        <div class="skeleton skeleton-input"></div>
    </div>
    <div class="skeleton-form-group">
        <div class="skeleton skeleton-label"></div>
        <div class="skeleton skeleton-textarea"></div>
    </div>
    <div class="skeleton skeleton-button"></div>
</div>

<!-- Пример 8: Скелетон для таблицы -->
<table class="skeleton-table">
    <tbody>
        <?php for ($i = 0; $i < 4; $i++): ?>
        <tr>
            <td><div class="skeleton"></div></td>
            <td><div class="skeleton"></div></td>
            <td><div class="skeleton"></div></td>
            <td><div class="skeleton"></div></td>
        </tr>
        <?php endfor; ?>
    </tbody>
</table>
