/**
 * Map Integration
 * @package ATK_VED
 */

(function() {
    'use strict';

    // Проверяем, есть ли элемент карты на странице
    const mapElement = document.getElementById('contact-map');
    if (!mapElement) return;

    // Координаты (замените на реальные)
    const coordinates = [43.238949, 76.889709]; // Алматы, центр
    
    // Функция инициализации карты
    function initMap() {
        // Проверяем, загружен ли API Яндекс.Карт
        if (typeof ymaps === 'undefined') {
            console.warn('Яндекс.Карты не загружены');
            // Показываем заглушку
            mapElement.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;background:#f0f0f0;color:#666;font-size:14px;">Карта временно недоступна</div>';
            return;
        }

        ymaps.ready(function() {
            // Создаем карту
            const map = new ymaps.Map('contact-map', {
                center: coordinates,
                zoom: 15,
                controls: ['zoomControl', 'fullscreenControl']
            });

            // Создаем метку
            const placemark = new ymaps.Placemark(coordinates, {
                balloonContentHeader: 'АТК-ВЭД',
                balloonContentBody: 'Профессиональное таможенное оформление',
                balloonContentFooter: 'г. Алматы',
                hintContent: 'АТК-ВЭД'
            }, {
                preset: 'islands#redIcon',
                iconColor: '#e31e24'
            });

            // Добавляем метку на карту
            map.geoObjects.add(placemark);

            // Отключаем скролл карты колесиком мыши
            map.behaviors.disable('scrollZoom');
        });
    }

    // Если API уже загружен
    if (typeof ymaps !== 'undefined') {
        initMap();
    } else {
        // Загружаем API Яндекс.Карт
        const script = document.createElement('script');
        script.src = 'https://api-maps.yandex.ru/2.1/?apikey=YOUR_API_KEY&lang=ru_RU';
        script.onload = initMap;
        script.onerror = function() {
            console.error('Не удалось загрузить Яндекс.Карты');
            mapElement.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;background:#f0f0f0;color:#666;font-size:14px;">Карта временно недоступна</div>';
        };
        document.head.appendChild(script);
    }

})();
