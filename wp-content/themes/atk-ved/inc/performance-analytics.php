<?php
/**
 * Performance Analytics - Core Web Vitals и другие метрики производительности
 *
 * @package ATK_VED
 * @since 3.2.0
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Класс для аналитики производительности
 */
class ATK_VED_Performance_Analytics {

    private static ?self $instance = null;

    public static function get_instance(): self {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Отправка метрик производительности
        add_action( 'wp_footer', [ $this, 'output_performance_analytics' ], 20 );
        
        // Регистрация метрик для сбора
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_performance_tracker' ] );
    }

    /**
     * Подключение скрипта для отслеживания производительности
     */
    public function enqueue_performance_tracker(): void {
        wp_enqueue_script(
            'atk-performance-tracker',
            get_template_directory_uri() . '/js/performance-tracker.js',
            [],
            ATK_VED_VERSION,
            true
        );
    }

    /**
     * Вывод скрипта для анализа производительности
     */
    public function output_performance_analytics(): void {
        ?>
        <script>
        (function() {
            'use strict';

            // Сбор метрик Core Web Vitals
            let webVitals = {
                CLS: 0,
                FID: 0,
                LCP: 0,
                FCP: 0,
                TTFB: 0,
                INP: 0,
                EL: 0
            };

            // Функция для отправки метрик
            function sendMetrics(metrics) {
                // Только если пользователь согласился на аналитику
                if (navigator.sendBeacon && typeof atkVed !== 'undefined' && atkVed.analyticsEnabled) {
                    const payload = JSON.stringify({
                        page: location.pathname,
                        timestamp: Date.now(),
                        metrics: metrics,
                        userAgent: navigator.userAgent,
                        viewport: {
                            width: window.innerWidth,
                            height: window.innerHeight
                        }
                    });

                    navigator.sendBeacon('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', new Blob([payload], { type: 'application/json' }));
                }
            }

            // Измерение Cumulative Layout Shift (CLS)
            let clsValue = 0;
            let clsEntries = [];

            new PerformanceObserver((entryList) => {
                for (const entry of entryList.getEntries()) {
                    // Только layout shifts, которые не являются результатом ввода пользователя
                    if (!entry.hadRecentInput) {
                        clsValue += entry.value;
                        clsEntries.push(entry);
                    }
                }
            }).observe({entryTypes: ['layout-shift']});

            // Измерение Largest Contentful Paint (LCP)
            new PerformanceObserver((entryList) => {
                const entry = entryList.getEntries().pop();
                if (entry) {
                    webVitals.LCP = entry.startTime;
                    
                    // Отправляем LCP сразу, так как это последний из основных CWV
                    sendMetrics({
                        LCP: webVitals.LCP,
                        CLS: clsValue,
                        timestamp: Date.now()
                    });
                }
            }).observe({entryTypes: ['largest-contentful-paint']});

            // Измерение First Input Delay (FID) / Interaction to Next Paint (INP)
            new PerformanceObserver((entryList) => {
                for (const entry of entryList.getEntries()) {
                    const fid = entry.processingStart - entry.startTime;
                    if (fid > webVitals.FID) {
                        webVitals.FID = fid;
                    }
                }
            }).observe({entryTypes: ['first-input']});

            // Измерение First Contentful Paint (FCP)
            new PerformanceObserver((entryList) => {
                const entry = entryList.getEntries().pop();
                if (entry) {
                    webVitals.FCP = entry.startTime;
                }
            }).observe({entryTypes: ['paint'], buffered: true});

            // Измерение Time to First Byte (TTFB)
            new PerformanceObserver((entryList) => {
                const entry = entryList.getEntries().pop();
                if (entry) {
                    webVitals.TTFB = entry.responseStart - entry.requestStart;
                }
            }).observe({entryTypes: ['navigation']});

            // Обработка загрузки ресурсов
            new PerformanceObserver((entryList) => {
                for (const entry of entryList.getEntries()) {
                    if (entry.name.includes('analytics.js') || entry.name.includes('tracker.js')) {
                        webVitals.EL = (webVitals.EL || 0) + entry.duration;
                    }
                }
            }).observe({entryTypes: ['resource']});

            // Отправка метрик при разгрузке страницы
            window.addEventListener('beforeunload', () => {
                sendMetrics({
                    CLS: clsValue,
                    FID: webVitals.FID,
                    LCP: webVitals.LCP,
                    FCP: webVitals.FCP,
                    TTFB: webVitals.TTFB,
                    EL: webVitals.EL,
                    pageLoadTime: performance.now(),
                    timestamp: Date.now()
                });
            });

            // Отправка метрик каждые 30 секунд для долгих сессий
            setInterval(() => {
                sendMetrics({
                    CLS: clsValue,
                    activeTime: performance.now(),
                    timestamp: Date.now()
                });
            }, 30000);

        })();
        </script>
        <?php
    }
}

// Инициализация
function atk_ved_init_performance_analytics(): void {
    ATK_VED_Performance_Analytics::get_instance();
}
add_action( 'after_setup_theme', 'atk_ved_init_performance_analytics' );