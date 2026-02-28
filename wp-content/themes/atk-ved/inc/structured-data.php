<?php
/**
 * Structured Data (JSON-LD) для SEO
 *
 * @package ATK_VED
 * @since 3.6.0
 */

declared(strict_types=1);

defined('ABSPATH') || exit;

/**
 * Добавление JSON-LD разметки LocalBusiness
 */
function atk_ved_add_local_business_schema(): void {
    if (!is_front_page()) {
        return;
    }
    
    $company = atk_ved_get_company_info();
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'LocalBusiness',
        '@id' => home_url('/#organization'),
        'name' => esc_html($company['name'] ?? get_bloginfo('name')),
        'url' => home_url('/'),
        'logo' => [
            '@type' => 'ImageObject',
            'url' => get_template_directory_uri() . '/images/logo/logo.png',
            'width' => 240,
            'height' => 60,
        ],
        'image' => get_template_directory_uri() . '/images/og-default.jpg',
        'description' => get_bloginfo('description'),
        'telephone' => esc_html($company['phone'] ?? ''),
        'email' => esc_html($company['email'] ?? ''),
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => esc_html($company['address'] ?? ''),
            'addressLocality' => esc_html($company['city'] ?? 'Алматы'),
            'addressCountry' => 'KZ',
        ],
        'geo' => [
            '@type' => 'GeoCoordinates',
            'latitude' => '43.2220',
            'longitude' => '76.8512',
        ],
        'openingHoursSpecification' => [
            [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'opens' => '09:00',
                'closes' => '18:00',
            ],
        ],
        'priceRange' => '$$',
        'aggregateRating' => [
            '@type' => 'AggregateRating',
            'ratingValue' => '4.9',
            'reviewCount' => '127',
            'bestRating' => '5',
            'worstRating' => '1',
        ],
    ];
    
    // Добавляем социальные сети если есть
    $social_links = [];
    if (!empty($company['vk'])) {
        $social_links[] = esc_url($company['vk']);
    }
    if (!empty($company['telegram'])) {
        $social_links[] = esc_url($company['telegram']);
    }
    if (!empty($company['whatsapp'])) {
        $social_links[] = esc_url($company['whatsapp']);
    }
    
    if (!empty($social_links)) {
        $schema['sameAs'] = $social_links;
    }
    
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}
add_action('wp_head', 'atk_ved_add_local_business_schema', 12);

/**
 * Добавление JSON-LD разметки Service
 */
function atk_ved_add_service_schema(): void {
    if (!is_front_page() && !is_page_template('page-services.php')) {
        return;
    }
    
    $services = [
        [
            'name' => 'Авиадоставка из Китая',
            'description' => 'Быстрая доставка грузов из Китая авиатранспортом за 7-10 дней',
            'provider' => get_bloginfo('name'),
            'areaServed' => 'RU',
            'offers' => [
                '@type' => 'Offer',
                'price' => '5.00',
                'priceCurrency' => 'USD',
                'priceSpecification' => [
                    '@type' => 'UnitPriceSpecification',
                    'price' => '5.00',
                    'priceCurrency' => 'USD',
                    'unitText' => 'кг',
                ],
            ],
        ],
        [
            'name' => 'Морская доставка из Китая',
            'description' => 'Экономичная доставка грузов морским транспортом за 35-45 дней',
            'provider' => get_bloginfo('name'),
            'areaServed' => 'RU',
            'offers' => [
                '@type' => 'Offer',
                'price' => '1.50',
                'priceCurrency' => 'USD',
                'priceSpecification' => [
                    '@type' => 'UnitPriceSpecification',
                    'price' => '1.50',
                    'priceCurrency' => 'USD',
                    'unitText' => 'кг',
                ],
            ],
        ],
        [
            'name' => 'Железнодорожная доставка',
            'description' => 'Оптимальная доставка грузов ж/д транспортом за 20-25 дней',
            'provider' => get_bloginfo('name'),
            'areaServed' => 'RU',
            'offers' => [
                '@type' => 'Offer',
                'price' => '3.00',
                'priceCurrency' => 'USD',
                'priceSpecification' => [
                    '@type' => 'UnitPriceSpecification',
                    'price' => '3.00',
                    'priceCurrency' => 'USD',
                    'unitText' => 'кг',
                ],
            ],
        ],
        [
            'name' => 'Таможенное оформление',
            'description' => 'Полное таможенное оформление грузов из Китая',
            'provider' => get_bloginfo('name'),
            'areaServed' => 'RU',
        ],
    ];
    
    foreach ($services as $service) {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'serviceType' => esc_html($service['name']),
            'name' => esc_html($service['name']),
            'description' => esc_html($service['description']),
            'provider' => [
                '@type' => 'Organization',
                'name' => esc_html($service['provider']),
                'url' => home_url('/'),
            ],
            'areaServed' => [
                '@type' => 'Country',
                'name' => $service['areaServed'],
            ],
        ];
        
        if (isset($service['offers'])) {
            $schema['offers'] = $service['offers'];
        }
        
        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }
}
add_action('wp_head', 'atk_ved_add_service_schema', 13);

/**
 * Добавление JSON-LD разметки FAQPage
 */
function atk_ved_add_faq_schema(): void {
    if (!is_front_page()) {
        return;
    }
    
    $faqs = [
        [
            'question' => 'Какой минимальный объем заказа?',
            'answer' => 'Минимального объема нет - работаем с любыми партиями от 1 кг. Однако для оптимизации затрат рекомендуем заказы от 50 кг.',
        ],
        [
            'question' => 'Сколько времени занимает доставка?',
            'answer' => 'Сроки зависят от способа доставки: авиа 7-10 дней, ЖД 20-25 дней, море 35-45 дней от склада в Китае до склада в России.',
        ],
        [
            'question' => 'Как рассчитывается стоимость доставки?',
            'answer' => 'Стоимость зависит от веса, объема и способа доставки. Авиа от $5/кг, ЖД от $3/кг, море от $1.5/кг. Делаем точный расчет индивидуально.',
        ],
        [
            'question' => 'Помогаете ли вы с таможенным оформлением?',
            'answer' => 'Да, мы берем на себя все вопросы таможенного оформления: декларирование, сертификация, уплата пошлин и сборов.',
        ],
        [
            'question' => 'Что делать, если товар пришел с браком?',
            'answer' => 'Мы проверяем качество перед отправкой. Если брак обнаружен при получении - возвращаем деньги или меняем товар согласно договору.',
        ],
        [
            'question' => 'Нужно ли открывать ИП для заказа?',
            'answer' => 'Для коммерческих поставок требуется ИП или ООО. Для личных покупок до 1000 евро в месяц регистрация не нужна.',
        ],
    ];
    
    $faq_items = [];
    foreach ($faqs as $faq) {
        $faq_items[] = [
            '@type' => 'Question',
            'name' => esc_html($faq['question']),
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => esc_html($faq['answer']),
            ],
        ];
    }
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $faq_items,
    ];
    
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}
add_action('wp_head', 'atk_ved_add_faq_schema', 14);

/**
 * Добавление JSON-LD разметки WebSite с поиском
 */
function atk_ved_add_website_schema(): void {
    if (!is_front_page()) {
        return;
    }
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        '@id' => home_url('/#website'),
        'url' => home_url('/'),
        'name' => get_bloginfo('name'),
        'description' => get_bloginfo('description'),
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => home_url('/?s={search_term_string}'),
            ],
            'query-input' => 'required name=search_term_string',
        ],
    ];
    
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}
add_action('wp_head', 'atk_ved_add_website_schema', 11);
