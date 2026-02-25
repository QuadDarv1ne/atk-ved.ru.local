/**
 * Галерея товаров и кейсов
 */

(function($) {
    'use strict';
    
    // Данные галереи
    const galleryItems = [
        {
            category: 'electronics',
            title: 'Электроника',
            image: 'electronics.svg',
            description: 'Смартфоны, планшеты, аксессуары'
        },
        {
            category: 'clothing',
            title: 'Одежда',
            image: 'clothing.svg',
            description: 'Мужская и женская одежда'
        },
        {
            category: 'toys',
            title: 'Игрушки',
            image: 'toys.svg',
            description: 'Детские игрушки и развивающие игры'
        },
        {
            category: 'home',
            title: 'Товары для дома',
            image: 'home.svg',
            description: 'Посуда, текстиль, декор'
        },
        {
            category: 'beauty',
            title: 'Красота',
            image: 'beauty.svg',
            description: 'Косметика и уход'
        },
        {
            category: 'sports',
            title: 'Спорт',
            image: 'sports.svg',
            description: 'Спортивные товары и инвентарь'
        }
    ];
    
    // Создание секции галереи
    function createGallerySection() {
        const galleryHTML = `
            <section class="gallery-section" id="gallery">
                <div class="container">
                    <h2 class="section-title reveal">КАТЕГОРИИ ТОВАРОВ</h2>
                    <p class="section-subtitle">Мы работаем с любыми категориями товаров из Китая</p>
                    
                    <div class="gallery-grid">
                        ${galleryItems.map(item => `
                            <div class="gallery-item reveal" data-category="${item.category}">
                                <div class="gallery-item-image">
                                    <div class="gallery-icon">${getCategoryIcon(item.category)}</div>
                                </div>
                                <div class="gallery-item-content">
                                    <h3>${item.title}</h3>
                                    <p>${item.description}</p>
                                    <button class="gallery-btn" onclick="atkOpenModal()">
                                        Заказать
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                            <path d="M8 0L6.59 1.41L12.17 7H0V9H12.17L6.59 14.59L8 16L16 8L8 0Z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </section>
        `;
        
        // Вставляем после секции услуг
        $('.services-section').after(galleryHTML);
    }
    
    // Получение иконки категории
    function getCategoryIcon(category) {
        const icons = {
            electronics: '📱',
            clothing: '👕',
            toys: '🧸',
            home: '🏠',
            beauty: '💄',
            sports: '⚽'
        };
        return icons[category] || '📦';
    }
    
    // Инициализация
    $(document).ready(function() {
        createGallerySection();
    });
    
})(jQuery);
