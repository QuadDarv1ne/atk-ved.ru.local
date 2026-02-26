<?php
/**
 * SEO Оптимизация — Schema.org, Open Graph, Twitter Cards
 *
 * @package ATK_VED
 * @since   3.2.0
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Класс для управления SEO разметкой
 */
class ATK_VED_SEO {

    private static ?self $instance = null;

    public static function get_instance(): self {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Schema.org
        add_action( 'wp_head', [ $this, 'output_schema_org' ], 1 );
        add_action( 'wp_footer', [ $this, 'output_schema_jsonld' ], 1 );

        // Open Graph
        add_action( 'wp_head', [ $this, 'output_open_graph' ], 1 );

        // Twitter Cards
        add_action( 'wp_head', [ $this, 'output_twitter_cards' ], 1 );

        // Дополнительные SEO мета
        add_action( 'wp_head', [ $this, 'output_seo_meta' ], 1 );

        // Canonical URL
        add_action( 'wp_head', [ $this, 'output_canonical' ], 1 );
    }

    /**
     * Генерация Schema.org разметки
     */
    public function output_schema_org(): void {
        $schema = [];

        // Organization schema для всех страниц
        $schema[] = $this->get_organization_schema();

        // BreadcrumbList schema
        if ( ! is_front_page() ) {
            $schema[] = $this->get_breadcrumb_schema();
        }

        // Page-specific schema
        if ( is_front_page() ) {
            $schema[] = $this->get_webpage_schema();
            $schema[] = $this->get_service_schema();
        } elseif ( is_singular( 'post' ) ) {
            $schema[] = $this->get_article_schema();
        } elseif ( is_singular( 'page' ) ) {
            $schema[] = $this->get_webpage_schema();
        }

        // Вывод JSON-LD
        if ( ! empty( $schema ) ) {
            echo '<script type="application/ld+json">' . "\n";
            echo wp_json_encode( $schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
            echo "\n</script>\n";
        }
    }

    /**
     * Вывод JSON-LD в footer (для динамических данных)
     */
    public function output_schema_jsonld(): void {
        // FAQ Schema для главной
        if ( is_front_page() ) {
            $faq_schema = $this->get_faq_schema();
            if ( $faq_schema ) {
                echo '<script type="application/ld+json">' . "\n";
                echo wp_json_encode( $faq_schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
                echo "\n</script>\n";
            }
        }

        // Review Schema для отзывов
        if ( is_front_page() ) {
            $review_schema = $this->get_review_schema();
            if ( $review_schema ) {
                echo '<script type="application/ld+json">' . "\n";
                echo wp_json_encode( $review_schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
                echo "\n</script>\n";
            }
        }
    }

    /**
     * Organization Schema
     */
    private function get_organization_schema(): array {
        $company_info = atk_ved_get_company_info();
        $social_links = atk_ved_get_social_links();

        $schema = [
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => $company_info['name'],
            'url'      => home_url( '/' ),
            'logo'     => get_custom_logo() ? wp_get_attachment_image_url( get_custom_logo(), 'full' ) : home_url( '/logo.png' ),
            'foundingDate' => (string) $company_info['founded'],
            'description' => $company_info['description'],
        ];

        // Соцсети
        $same_as = [];
        foreach ( $social_links as $platform => $url ) {
            if ( $url ) {
                $same_as[] = $url;
            }
        }

        if ( ! empty( $same_as ) ) {
            $schema['sameAs'] = $same_as;
        }

        // Контактная информация
        $schema['contactPoint'] = [
            [
                '@type'            => 'ContactPoint',
                'telephone'        => get_theme_mod( 'atk_ved_phone', '+7 (XXX) XXX-XX-XX' ),
                'contactType'      => 'customer service',
                'availableLanguage' => [ 'Russian', 'Chinese' ],
                'areaServed'       => 'RU',
            ],
        ];

        // Адрес
        $address = get_theme_mod( 'atk_ved_address', 'Москва, Россия' );
        if ( $address ) {
            $schema['address'] = [
                '@type'           => 'PostalAddress',
                'streetAddress'   => $address,
                'addressCountry'  => 'RU',
            ];
        }

        return $schema;
    }

    /**
     * WebPage Schema
     */
    private function get_webpage_schema(): array {
        return [
            '@context'    => 'https://schema.org',
            '@type'       => 'WebPage',
            'name'        => wp_get_document_title(),
            'description' => $this->get_meta_description(),
            'url'         => home_url( '/' ),
            'isPartOf'    => [
                '@type' => 'WebSite',
                'name'  => get_bloginfo( 'name' ),
                'url'   => home_url( '/' ),
            ],
        ];
    }

    /**
     * Service Schema (для услуг)
     */
    private function get_service_schema(): array {
        return [
            '@context' => 'https://schema.org',
            '@type'    => 'Service',
            'name'     => 'Поставки товаров из Китая для маркетплейсов',
            'provider' => [
                '@type' => 'Organization',
                'name'  => atk_ved_get_company_info()['name'],
                'url'   => home_url( '/' ),
            ],
            'areaServed' => [
                '@type' => 'Country',
                'name'  => 'Россия',
            ],
            'serviceType' => 'Логистика и ВЭД',
        ];
    }

    /**
     * BreadcrumbList Schema
     */
    private function get_breadcrumb_schema(): array {
        $breadcrumbs = $this->get_breadcrumbs();

        $items = [];
        foreach ( $breadcrumbs as $index => $crumb ) {
            $items[] = [
                '@type'    => 'ListItem',
                'position' => $index + 1,
                'name'     => $crumb['label'],
                'item'     => $crumb['url'],
            ];
        }

        return [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    /**
     * Article Schema (для постов)
     */
    private function get_article_schema(): array {
        global $post;

        if ( ! $post ) {
            return [];
        }

        $author_id   = (int) $post->post_author;
        $author_name = get_the_author_meta( 'display_name', $author_id );

        return [
            '@context'    => 'https://schema.org',
            '@type'       => 'BlogPosting',
            'headline'    => get_the_title(),
            'description' => $this->get_meta_description(),
            'image'       => get_the_post_thumbnail_url( $post->ID, 'full' ),
            'datePublished' => get_the_date( 'c' ),
            'dateModified' => get_the_modified_date( 'c' ),
            'author'      => [
                '@type' => 'Person',
                'name'  => $author_name,
                'url'   => get_author_posts_url( $author_id ),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name'  => atk_ved_get_company_info()['name'],
                'logo'  => [
                    '@type' => 'ImageObject',
                    'url'   => get_custom_logo() ? wp_get_attachment_image_url( get_custom_logo(), 'full' ) : home_url( '/logo.png' ),
                ],
            ],
        ];
    }

    /**
     * FAQ Schema
     */
    private function get_faq_schema(): ?array {
        $faqs = get_field( 'faq_items', 'option' ) ?? [];

        if ( empty( $faqs ) ) {
            return null;
        }

        $questions = [];
        foreach ( $faqs as $faq ) {
            $questions[] = [
                '@type'          => 'Question',
                'name'           => $faq['question'] ?? '',
                'acceptedAnswer' => [
                    '@type'      => 'Answer',
                    'text'       => $faq['answer'] ?? '',
                ],
            ];
        }

        return [
            '@context'        => 'https://schema.org',
            '@type'           => 'FAQPage',
            'mainEntity'      => $questions,
        ];
    }

    /**
     * Review Schema
     */
    private function get_review_schema(): ?array {
        $reviews = get_field( 'reviews', 'option' ) ?? [];

        if ( empty( $reviews ) ) {
            return null;
        }

        $total_rating = 0;
        $review_items = [];

        foreach ( $reviews as $review ) {
            $rating = (int) ( $review['rating'] ?? 5 );
            $total_rating += $rating;

            $review_items[] = [
                '@type'         => 'Review',
                'reviewRating'  => [
                    '@type'       => 'Rating',
                    'ratingValue' => $rating,
                ],
                'author' => [
                    '@type' => 'Person',
                    'name'  => $review['name'] ?? 'Клиент',
                ],
                'reviewBody' => $review['text'] ?? '',
            ];
        }

        $avg_rating = $total_rating / count( $reviews );

        return [
            '@context' => 'https://schema.org',
            '@type'    => 'AggregateRating',
            'itemReviewed' => [
                '@type' => 'Service',
                'name'  => atk_ved_get_company_info()['name'],
            ],
            'ratingValue' => round( $avg_rating, 1 ),
            'reviewCount' => count( $reviews ),
            'review'      => $review_items,
        ];
    }

    /**
     * Open Graph мета теги
     */
    public function output_open_graph(): void {
        $title       = $this->get_og_title();
        $description = $this->get_og_description();
        $image       = $this->get_og_image();
        $url         = $this->get_og_url();
        $type        = $this->get_og_type();

        echo '<meta property="og:locale" content="ru_RU">' . "\n";
        echo '<meta property="og:type" content="' . esc_attr( $type ) . '">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
        echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";

        if ( $image ) {
            echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
            echo '<meta property="og:image:alt" content="' . esc_attr( $title ) . '">' . "\n";
            echo '<meta property="og:image:width" content="1200">' . "\n";
            echo '<meta property="og:image:height" content="630">' . "\n";
        }

        // Для статей
        if ( is_singular( 'post' ) ) {
            echo '<meta property="article:published_time" content="' . get_the_date( 'c' ) . '">' . "\n";
            echo '<meta property="article:modified_time" content="' . get_the_modified_date( 'c' ) . '">' . "\n";
        }
    }

    /**
     * Twitter Cards
     */
    public function output_twitter_cards(): void {
        $title       = $this->get_og_title();
        $description = $this->get_og_description();
        $image       = $this->get_og_image();

        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n";

        if ( $image ) {
            echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
            echo '<meta name="twitter:image:alt" content="' . esc_attr( $title ) . '">' . "\n";
        }

        // Twitter username (если настроен)
        $twitter_user = get_theme_mod( 'atk_ved_twitter_username', '' );
        if ( $twitter_user ) {
            echo '<meta name="twitter:site" content="@' . esc_attr( $twitter_user ) . '">' . "\n";
        }
    }

    /**
     * Дополнительные SEO мета теги
     */
    public function output_seo_meta(): void {
        // Robots
        if ( is_singular() ) {
            echo '<meta name="robots" content="index, follow">' . "\n";
        } elseif ( is_archive() || is_search() ) {
            echo '<meta name="robots" content="noindex, follow">' . "\n";
        }

        // Description
        $description = $this->get_meta_description();
        if ( $description ) {
            echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
        }

        // Author
        if ( is_singular() ) {
            echo '<meta name="author" content="' . esc_attr( get_the_author() ) . '">' . "\n";
        }

        // Theme Color
        echo '<meta name="theme-color" content="#e31e24">' . "\n";
        echo '<meta name="msapplication-TileColor" content="#e31e24">' . "\n";
    }

    /**
     * Canonical URL
     */
    public function output_canonical(): void {
        $canonical = $this->get_canonical_url();
        echo '<link rel="canonical" href="' . esc_url( $canonical ) . '">' . "\n";
    }

    /**
     * Получение мета описания
     */
    private function get_meta_description(): string {
        if ( function_exists( 'YoastSEO' ) ) {
            $desc = YoastSEO()->meta->for_current_page()->description;
            if ( $desc ) {
                return $desc;
            }
        }

        if ( is_singular() ) {
            global $post;
            if ( $post && $post->post_excerpt ) {
                return strip_tags( $post->post_excerpt );
            }
        }

        return get_theme_mod( 'atk_ved_meta_description', '' );
    }

    /**
     * Получение OG заголовка
     */
    private function get_og_title(): string {
        if ( function_exists( 'YoastSEO' ) ) {
            $title = YoastSEO()->meta->for_current_page()->open_graph_title;
            if ( $title ) {
                return $title;
            }
        }

        return wp_get_document_title();
    }

    /**
     * Получение OG описания
     */
    private function get_og_description(): string {
        if ( function_exists( 'YoastSEO' ) ) {
            $desc = YoastSEO()->meta->for_current_page()->open_graph_description;
            if ( $desc ) {
                return $desc;
            }
        }

        return $this->get_meta_description();
    }

    /**
     * Получение OG изображения
     */
    private function get_og_image(): string {
        if ( is_singular() ) {
            $thumb_id = get_post_thumbnail_id();
            if ( $thumb_id ) {
                return wp_get_attachment_image_url( $thumb_id, 'full' );
            }
        }

        return get_theme_mod( 'atk_ved_og_image', get_template_directory_uri() . '/images/og-default.jpg' );
    }

    /**
     * Получение OG URL
     */
    private function get_og_url(): string {
        if ( is_singular() ) {
            return get_permalink();
        }

        return home_url( '/' );
    }

    /**
     * Получение OG типа
     */
    private function get_og_type(): string {
        if ( is_front_page() ) {
            return 'website';
        }

        if ( is_singular( 'post' ) ) {
            return 'article';
        }

        return 'website';
    }

    /**
     * Получение canonical URL
     */
    private function get_canonical_url(): string {
        if ( function_exists( 'YoastSEO' ) ) {
            $canonical = YoastSEO()->meta->for_current_page()->canonical;
            if ( $canonical ) {
                return $canonical;
            }
        }

        if ( is_singular() ) {
            return get_permalink();
        }

        return home_url( '/' );
    }

    /**
     * Получение хлебных крошек
     */
    private function get_breadcrumbs(): array {
        $crumbs = [];

        // Главная
        $crumbs[] = [
            'label' => 'Главная',
            'url'   => home_url( '/' ),
        ];

        if ( is_singular() ) {
            global $post;

            if ( $post->post_type === 'post' ) {
                $categories = get_the_category( $post->ID );
                if ( $categories ) {
                    $cat = $categories[0];
                    $crumbs[] = [
                        'label' => $cat->name,
                        'url'   => get_category_link( $cat->term_id ),
                    ];
                }
            }

            $crumbs[] = [
                'label' => get_the_title(),
                'url'   => get_permalink(),
            ];
        } elseif ( is_archive() ) {
            $crumbs[] = [
                'label' => get_the_archive_title(),
                'url'   => get_permalink(),
            ];
        } elseif ( is_search() ) {
            $crumbs[] = [
                'label' => 'Поиск: ' . get_search_query(),
                'url'   => get_search_link(),
            ];
        }

        return $crumbs;
    }
}

// Инициализация
function atk_ved_init_seo(): void {
    ATK_VED_SEO::get_instance();
}
add_action( 'after_setup_theme', 'atk_ved_init_seo' );
