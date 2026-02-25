<?php
/**
 * Переводимые строки темы АТК ВЭД
 * 
 * @package ATK_VED
 * @since 1.7.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Получить переводимые строки для главной страницы
 * 
 * @return array Массив переводимых строк
 */
function atk_ved_get_landing_strings() {
    return array(
        // Hero Section
        'hero_feature_1' => __('Опытные менеджеры', 'atk-ved'),
        'hero_feature_2' => __('Прозрачные цены', 'atk-ved'),
        'hero_feature_3' => __('Без минимальной цены', 'atk-ved'),
        'hero_feature_4' => __('База поставщиков', 'atk-ved'),
        'hero_title_part1' => __('ТОВАРЫ', 'atk-ved'),
        'hero_title_part2' => __('ДЛЯ МАРКЕТПЛЕЙСОВ', 'atk-ved'),
        'hero_title_highlight' => __('ИЗ КИТАЯ', 'atk-ved'),
        'hero_title_part3' => __('ОПТОМ', 'atk-ved'),
        'marketplace_megamarket' => __('МЕГАМАРКЕТ', 'atk-ved'),
        'marketplace_alibaba' => __('Alibaba', 'atk-ved'),
        'marketplace_wildberries' => __('WILDBERRIES', 'atk-ved'),
        'marketplace_aliexpress' => __('AliExpress', 'atk-ved'),
        'marketplace_ozon' => __('OZON', 'atk-ved'),
        
        // Services Section
        'services_title' => __('НАШИ УСЛУГИ', 'atk-ved'),
        'service_1_title' => __('Поиск товаров', 'atk-ved'),
        'service_1_desc' => __('Помогаем найти нужные товары на китайских площадках по вашим требованиям и бюджету', 'atk-ved'),
        'service_2_title' => __('Проверка качества', 'atk-ved'),
        'service_2_desc' => __('Контролируем качество продукции перед отправкой, делаем фото и видео отчеты', 'atk-ved'),
        'service_3_title' => __('Доставка грузов', 'atk-ved'),
        'service_3_desc' => __('Организуем доставку любым удобным способом: авиа, море, ж/д, авто', 'atk-ved'),
        'service_4_title' => __('Таможенное оформление', 'atk-ved'),
        'service_4_desc' => __('Берем на себя все вопросы таможенного оформления и сертификации', 'atk-ved'),
        'service_5_title' => __('Складская логистика', 'atk-ved'),
        'service_5_desc' => __('Предоставляем услуги хранения и обработки грузов на наших складах', 'atk-ved'),
        'service_6_title' => __('Консультации', 'atk-ved'),
        'service_6_desc' => __('Консультируем по всем вопросам работы с Китаем и маркетплейсами', 'atk-ved'),
        
        // Search Section
        'search_title' => __('НАЙДЕМ ТОВАР В КИТАЕ ПО ВАШЕМУ ЗАПРОСУ И ПОЛУЧИМ САМОЕ ВЫГОДНОЕ ПРЕДЛОЖЕНИЕ ОТ ПОСТАВЩИКА', 'atk-ved'),
        'search_subtitle' => __('Мы поможем найти, выкупить и доставить товары из Китая на самых выгодных условиях', 'atk-ved'),
        'search_name_placeholder' => __('Ваше имя', 'atk-ved'),
        'search_phone_placeholder' => __('Ваш номер телефона', 'atk-ved'),
        'search_button' => __('ОСТАВИТЬ ЗАЯВКУ', 'atk-ved'),
        'search_privacy' => __('Отправляя ваши данные, вы соглашаетесь с политикой конфиденциальности', 'atk-ved'),
        
        // Delivery Section
        'delivery_title' => __('СПОСОБЫ И СРОКИ ДОСТАВКИ ГРУЗОВ', 'atk-ved'),
        'delivery_intro' => __('Мы предлагаем различные варианты доставки грузов из Китая в зависимости от ваших потребностей, сроков и бюджета.', 'atk-ved'),
        'delivery_feature_1' => __('Полное таможенное оформление', 'atk-ved'),
        'delivery_feature_2' => __('Страхование грузов', 'atk-ved'),
        'delivery_feature_3' => __('Отслеживание на всех этапах', 'atk-ved'),
        'delivery_feature_4' => __('Оптимальные маршруты', 'atk-ved'),
        'delivery_method_air' => __('АВИА', 'atk-ved'),
        'delivery_method_air_time' => __('5-10 дней', 'atk-ved'),
        'delivery_method_air_desc' => __('Самый быстрый способ', 'atk-ved'),
        'delivery_method_sea' => __('МОРЕ', 'atk-ved'),
        'delivery_method_sea_time' => __('35-45 дней', 'atk-ved'),
        'delivery_method_sea_desc' => __('Самый экономичный', 'atk-ved'),
        'delivery_method_rail' => __('Ж/Д', 'atk-ved'),
        'delivery_method_rail_time' => __('20-30 дней', 'atk-ved'),
        'delivery_method_rail_desc' => __('Оптимальное соотношение', 'atk-ved'),
        
        // Advantages Section
        'advantages_title' => __('НАШИ ПРЕИМУЩЕСТВА', 'atk-ved'),
        'advantage_1_title' => __('500+ клиентов', 'atk-ved'),
        'advantage_1_desc' => __('Нам доверяют свой бизнес', 'atk-ved'),
        'advantage_2_title' => __('10 лет опыта', 'atk-ved'),
        'advantage_2_desc' => __('Работаем с 2016 года', 'atk-ved'),
        'advantage_3_title' => __('98% успеха', 'atk-ved'),
        'advantage_3_desc' => __('Положительных отзывов', 'atk-ved'),
        'advantage_4_title' => __('24/7 поддержка', 'atk-ved'),
        'advantage_4_desc' => __('Всегда на связи', 'atk-ved'),
        
        // Steps Section
        'steps_title' => __('ЭТАПЫ СОТРУДНИЧЕСТВА', 'atk-ved'),
        'step_1_title' => __('Заявка', 'atk-ved'),
        'step_1_desc' => __('Вы оставляете заявку на сайте или связываетесь с нами', 'atk-ved'),
        'step_2_title' => __('Обсуждение', 'atk-ved'),
        'step_2_desc' => __('Обсуждаем детали, подбираем товары и рассчитываем стоимость', 'atk-ved'),
        'step_3_title' => __('Договор', 'atk-ved'),
        'step_3_desc' => __('Заключаем договор и согласовываем все условия', 'atk-ved'),
        'step_4_title' => __('Поиск и выкуп', 'atk-ved'),
        'step_4_desc' => __('Находим поставщиков, выкупаем товар и проверяем качество', 'atk-ved'),
        'step_5_title' => __('Доставка', 'atk-ved'),
        'step_5_desc' => __('Организуем доставку и таможенное оформление', 'atk-ved'),
        'step_6_title' => __('Получение', 'atk-ved'),
        'step_6_desc' => __('Вы получаете товар на нашем складе или с доставкой до двери', 'atk-ved'),
        
        // FAQ Section
        'faq_title' => __('ЧАСТО ЗАДАВАЕМЫЕ ВОПРОСЫ', 'atk-ved'),
        'faq_question_1' => __('Какой минимальный объем заказа?', 'atk-ved'),
        'faq_answer_1' => __('Мы работаем с заказами от 100 кг. Однако, для некоторых категорий товаров возможны исключения. Уточняйте у наших менеджеров.', 'atk-ved'),
        'faq_question_2' => __('Сколько времени занимает доставка?', 'atk-ved'),
        'faq_answer_2' => __('Сроки доставки зависят от выбранного способа: авиа — 5-10 дней, ж/д — 20-30 дней, море — 35-45 дней. Также учитывайте время на выкуп и таможенное оформление.', 'atk-ved'),
        'faq_question_3' => __('Как происходит оплата?', 'atk-ved'),
        'faq_answer_3' => __('Оплата производится в два этапа: 70% предоплата за товар и услуги, 30% после получения и проверки товара перед окончательной отгрузкой.', 'atk-ved'),
        'faq_question_4' => __('Работаете ли вы с юридическими лицами?', 'atk-ved'),
        'faq_answer_4' => __('Да, мы работаем как с юридическими, так и с физическими лицами. Предоставляем полный пакет документов для бухгалтерии.', 'atk-ved'),
        'faq_question_5' => __('Можно ли посетить ваш склад?', 'atk-ved'),
        'faq_answer_5' => __('Да, вы можете посетить наш склад в рабочее время. Рекомендуем предварительно записаться по телефону.', 'atk-ved'),
        'faq_question_6' => __('Предоставляете ли вы документы для маркетплейсов?', 'atk-ved'),
        'faq_answer_6' => __('Да, мы предоставляем все необходимые документы для работы с Wildberries, Ozon и другими маркетплейсами.', 'atk-ved'),
        
        // Reviews Section
        'reviews_title' => __('ОТЗЫВЫ КЛИЕНТОВ', 'atk-ved'),
        'review_1_name' => __('Александр М.', 'atk-ved'),
        'review_1_city' => __('Москва', 'atk-ved'),
        'review_1_text' => __('Работаю с АТК ВЭД уже больше года. Никаких нареканий, все четко и в срок. Особенно радует прозрачность ценообразования.', 'atk-ved'),
        'review_2_name' => __('Елена К.', 'atk-ved'),
        'review_2_city' => __('Санкт-Петербург', 'atk-ved'),
        'review_2_text' => __('Заказывала первую партию товара для WB. Ребята помогли с поиском поставщика, проверили качество. Все прошло гладко!', 'atk-ved'),
        'review_3_name' => __('Дмитрий В.', 'atk-ved'),
        'review_3_city' => __('Екатеринбург', 'atk-ved'),
        'review_3_text' => __('Доставка заняла чуть больше времени, чем обещали, но качество товара и сервис на высоте. Буду заказывать еще.', 'atk-ved'),
        'review_4_name' => __('Ольга С.', 'atk-ved'),
        'review_4_city' => __('Казань', 'atk-ved'),
        'review_4_text' => __('Отличная компания! Помогли с сертификацией товара. Рекомендую всем, кто планирует работать с Китаем.', 'atk-ved'),
        
        // Contact Section
        'contact_title' => __('ОСТАВЬТЕ ЗАЯВКУ', 'atk-ved'),
        'contact_subtitle' => __('Мы свяжемся с вами в течение 15 минут и ответим на все вопросы', 'atk-ved'),
        'contact_name_placeholder' => __('Ваше имя', 'atk-ved'),
        'contact_phone_placeholder' => __('Телефон', 'atk-ved'),
        'contact_email_placeholder' => __('Email', 'atk-ved'),
        'contact_message_placeholder' => __('Сообщение', 'atk-ved'),
        'contact_button' => __('ОТПРАВИТЬ', 'atk-ved'),
        'contact_privacy' => __('Нажимая кнопку, вы соглашаетесь с политикой конфиденциальности', 'atk-ved'),
        
        // Footer
        'footer_description' => __('Товары для маркетплейсов из Китая оптом. Полный цикл работы от поиска до доставки.', 'atk-ved'),
        'footer_services_title' => __('Услуги', 'atk-ved'),
        'footer_info_title' => __('Информация', 'atk-ved'),
        'footer_contacts_title' => __('Контакты', 'atk-ved'),
        'footer_copyright' => __('Все права защищены.', 'atk-ved'),
        'footer_developer' => __('заботой', 'atk-ved'),
        
        // Accessibility
        'aria_main_nav' => __('Главное меню', 'atk-ved'),
        'aria_telegram' => __('Telegram', 'atk-ved'),
        'aria_whatsapp' => __('WhatsApp', 'atk-ved'),
        'aria_scroll_top' => __('Вернуться наверх', 'atk-ved'),
        'aria_menu_toggle' => __('Меню', 'atk-ved'),
    );
}

/**
 * Получить конкретную строку перевода
 * 
 * @param string $key Ключ строки
 * @return string Переведенная строка
 */
function atk_ved_get_string($key) {
    $strings = atk_ved_get_landing_strings();
    return isset($strings[$key]) ? $strings[$key] : '';
}
