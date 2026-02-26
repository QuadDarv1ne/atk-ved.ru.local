<?php
/**
 * Dark Mode - Темная тема для сайта
 *
 * @package ATK_VED
 * @since 3.2.0
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Класс для управления темной темой
 */
class ATK_VED_Dark_Mode {

	private static ?self $instance = null;

	public static function get_instance(): self {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		// Добавляем поддержку цветовой схемы
		add_action( 'wp_head', array( $this, 'output_dark_mode_toggle' ), 20 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_dark_mode_assets' ) );

		// Добавляем цветовые переменные в CSS
		add_action( 'wp_head', array( $this, 'output_color_scheme_variables' ), 1 );

		// Поддержка системной предпочтительной темы
		add_action( 'wp_head', array( $this, 'output_system_preference_handler' ), 5 );
	}

	/**
	 * Подключение ресурсов для темной темы
	 */
	public function enqueue_dark_mode_assets(): void {
		// Подключаем скрипт переключения темы
		wp_enqueue_script(
			'atk-dark-mode',
			get_template_directory_uri() . '/js/dark-mode.js',
			array( 'jquery' ),
			ATK_VED_VERSION,
			true
		);

		// Локализация скрипта
		wp_localize_script(
			'atk-dark-mode',
			'atkDarkMode',
			array(
				'enabled'     => $this->is_dark_mode_enabled(),
				'toggleLabel' => __( 'Переключить темную тему', 'atk-ved' ),
				'autoDetect'  => true,
			)
		);
	}

	/**
	 * Вывод переключателя темной темы
	 */
	public function output_dark_mode_toggle(): void {
		if ( ! $this->is_dark_mode_option_enabled() ) {
			return;
		}
		?>
		<script>
		(function() {
			'use strict';
			
			// Создание переключателя темной темы
			function initDarkModeToggle() {
				// Проверяем, есть ли уже переключатель
				if (document.querySelector('.dark-mode-toggle')) {
					return;
				}
				
				// Создаем элемент переключателя
				const toggleButton = document.createElement('button');
				toggleButton.className = 'dark-mode-toggle';
				toggleButton.setAttribute('aria-label', '<?php esc_attr_e( 'Переключить темную тему', 'atk-ved' ); ?>');
				toggleButton.innerHTML = `
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
						<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
					</svg>
				`;
				
				// Добавляем к существующему элементу header-actions или создаем новый контейнер
				let container = document.querySelector('.header-actions');
				if (!container) {
					container = document.querySelector('.site-header');
					if (!container) {
						return;
					}
				}
				
				// Вставляем перед кнопкой CTA или в конец контейнера
				const ctaButton = container.querySelector('.cta-button');
				if (ctaButton) {
					container.insertBefore(toggleButton, ctaButton);
				} else {
					container.appendChild(toggleButton);
				}
				
				// Обработчик переключения
				toggleButton.addEventListener('click', function() {
					toggleDarkMode();
				});
			}
			
			// Функция переключения темной темы
			function toggleDarkMode() {
				document.body.classList.toggle('dark-mode');
				const isDark = document.body.classList.contains('dark-mode');
				
				// Сохраняем предпочтение в localStorage
				localStorage.setItem('darkMode', isDark ? 'enabled' : 'disabled');
				
				// Обновляем класс HTML
				if (isDark) {
					document.documentElement.setAttribute('data-theme', 'dark');
				} else {
					document.documentElement.setAttribute('data-theme', 'light');
				}
				
				// Отправляем событие для других скриптов
				window.dispatchEvent(new CustomEvent('darkModeToggle', {
					detail: { enabled: isDark }
				}));
			}
			
			// Установка темы на основе предпочтений
			function setThemeFromPreferences() {
				// Проверяем сохраненные предпочтения
				const savedPreference = localStorage.getItem('darkMode');
				if (savedPreference !== null) {
					if (savedPreference === 'enabled') {
						document.body.classList.add('dark-mode');
						document.documentElement.setAttribute('data-theme', 'dark');
					} else {
						document.body.classList.remove('dark-mode');
						document.documentElement.setAttribute('data-theme', 'light');
					}
					return;
				}
				
				// Автоопределение на основе системных настроек
				if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
					document.body.classList.add('dark-mode');
					document.documentElement.setAttribute('data-theme', 'dark');
					localStorage.setItem('darkMode', 'enabled');
				}
			}
			
			// Инициализация при загрузке DOM
			if (document.readyState === 'loading') {
				document.addEventListener('DOMContentLoaded', function() {
					setThemeFromPreferences();
					initDarkModeToggle();
				});
			} else {
				setThemeFromPreferences();
				initDarkModeToggle();
			}
			
			// Отслеживание изменения системных настроек
			window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
				if (localStorage.getItem('darkMode') === null) {
					if (e.matches) {
						document.body.classList.add('dark-mode');
						document.documentElement.setAttribute('data-theme', 'dark');
					} else {
						document.body.classList.remove('dark-mode');
						document.documentElement.setAttribute('data-theme', 'light');
					}
				}
			});
		})();
		</script>
		<?php
	}

	/**
	 * Вывод CSS переменных для цветовых схем
	 */
	public function output_color_scheme_variables(): void {
		?>
		<style>
		:root {
			/* Светлая тема (по умолчанию) */
			--bg-primary: #ffffff;
			--bg-secondary: #fafafa;
			--bg-tertiary: #f5f5f5;
			--text-primary: #2c2c2c;
			--text-secondary: #666666;
			--border-color: #e0e0e0;
			--accent-primary: #e31e24;
			--accent-secondary: #c01a1f;
			--shadow-light: 0 2px 10px rgba(0, 0, 0, 0.05);
			--shadow-medium: 0 4px 20px rgba(0, 0, 0, 0.1);
			--overlay-bg: rgba(0, 0, 0, 0.5);
		}
		
		.dark-mode,
		[data-theme="dark"] {
			--bg-primary: #1a1a1a;
			--bg-secondary: #222222;
			--bg-tertiary: #2a2a2a;
			--text-primary: #f0f0f0;
			--text-secondary: #cccccc;
			--border-color: #404040;
			--accent-primary: #ff4d4f;
			--accent-secondary: #ff7875;
			--shadow-light: 0 2px 10px rgba(0, 0, 0, 0.3);
			--shadow-medium: 0 4px 20px rgba(0, 0, 0, 0.4);
			--overlay-bg: rgba(0, 0, 0, 0.8);
		}
		
		/* Темная тема для конкретных элементов */
		.dark-mode body,
		[data-theme="dark"] body {
			background-color: var(--bg-primary);
			color: var(--text-primary);
		}
		
		.dark-mode .site-header,
		[data-theme="dark"] .site-header {
			background-color: var(--bg-secondary);
			border-color: var(--border-color);
		}
		
		.dark-mode .hero-section,
		[data-theme="dark"] .hero-section {
			background-color: var(--bg-secondary);
		}
		
		.dark-mode .services-section,
		[data-theme="dark"] .services-section {
			background-color: var(--bg-primary);
		}
		
		.dark-mode .service-card,
		[data-theme="dark"] .service-card {
			background-color: var(--bg-secondary);
			border-color: var(--border-color);
		}
		
		.dark-mode .cta-button,
		[data-theme="dark"] .cta-button {
			background-color: var(--accent-primary);
		}
		
		.dark-mode .cta-button:hover,
		[data-theme="dark"] .cta-button:hover {
			background-color: var(--accent-secondary);
		}
		
		.dark-mode input,
		.dark-mode textarea,
		.dark-mode select,
		[data-theme="dark"] input,
		[data-theme="dark"] textarea,
		[data-theme="dark"] select {
			background-color: var(--bg-tertiary);
			border-color: var(--border-color);
			color: var(--text-primary);
		}
		
		.dark-mode .modal,
		[data-theme="dark"] .modal {
			background-color: var(--overlay-bg);
		}
		
		.dark-mode .modal-content,
		[data-theme="dark"] .modal-content {
			background-color: var(--bg-primary);
			color: var(--text-primary);
		}
		
		/* Изменение цветов ссылок */
		.dark-mode a,
		[data-theme="dark"] a {
			color: var(--accent-primary);
		}
		
		.dark-mode a:hover,
		[data-theme="dark"] a:hover {
			color: var(--accent-secondary);
		}
		
		/* Адаптация для изображений */
		.dark-mode img,
		[data-theme="dark"] img {
			filter: brightness(0.9);
		}
		
		/* Адаптация для таблиц */
		.dark-mode table,
		[data-theme="dark"] table {
			border-color: var(--border-color);
		}
		
		.dark-mode th,
		.dark-mode td,
		[data-theme="dark"] th,
		[data-theme="dark"] td {
			border-color: var(--border-color);
		}
		</style>
		<?php
	}

	/**
	 * Вывод обработчика системных предпочтений
	 */
	public function output_system_preference_handler(): void {
		?>
		<script>
		// Обработчик системных предпочтений цветовой схемы
		(function() {
			'use strict';
			
			// Проверяем поддержку matchMedia
			if (window.matchMedia) {
				// Настраиваем слушатель изменений системных предпочтений
				const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
				
				// При изменении системных настроек
				mediaQuery.addListener(function(e) {
					// Если пользователь не сохранил свои предпочтения, используем системные
					if (localStorage.getItem('darkMode') === null) {
						if (e.matches) {
							document.body.classList.add('dark-mode');
							document.documentElement.setAttribute('data-theme', 'dark');
						} else {
							document.body.classList.remove('dark-mode');
							document.documentElement.setAttribute('data-theme', 'light');
						}
					}
				});
			}
		})();
		</script>
		<?php
	}

	/**
	 * Проверка включена ли темная тема
	 */
	public function is_dark_mode_enabled(): bool {
		$enabled = get_theme_mod( 'atk_ved_dark_mode_enabled', true );
		return (bool) $enabled;
	}

	/**
	 * Проверка включена ли опция переключателя
	 */
	public function is_dark_mode_option_enabled(): bool {
		$show_toggle = get_theme_mod( 'atk_ved_show_dark_mode_toggle', true );
		return (bool) $show_toggle;
	}
}

// Инициализация
function atk_ved_init_dark_mode(): void {
	ATK_VED_Dark_Mode::get_instance();
}
add_action( 'after_setup_theme', 'atk_ved_init_dark_mode' );