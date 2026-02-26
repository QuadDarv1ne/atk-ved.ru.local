<?php
/**
 * Accessibility Enhancements - Дополнительные улучшения доступности
 *
 * @package ATK_VED
 * @since 3.2.0
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Класс для дополнительных улучшений доступности
 */
class ATK_VED_Accessibility_Enhancements {

	private static ?self $instance = null;

	public static function get_instance(): self {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		// Улучшенная навигация с клавиатуры
		add_action( 'wp_footer', array( $this, 'output_enhanced_keyboard_navigation' ), 30 );

		// Улучшенная ARIA разметка
		add_filter( 'body_class', array( $this, 'add_enhanced_a11y_classes' ) );

		// Улучшения для вспомогательных технологий
		add_action( 'wp_head', array( $this, 'output_a11y_improvements' ), 20 );
	}

	/**
	 * Добавление улучшенных классов доступности
	 */
	public function add_enhanced_a11y_classes( array $classes ): array {
		$classes[] = 'a11y-ready';

		// Проверка, включена ли навигация с клавиатуры
		if ( isset( $_GET['keyboard-navigation'] ) || $this->is_keyboard_navigation_active() ) {
			$classes[] = 'keyboard-navigation-active';
		}

		return $classes;
	}

	/**
	 * Проверка активности навигации с клавиатуры
	 */
	private function is_keyboard_navigation_active(): bool {
		// Проверяем, был ли последний ввод с клавиатуры
		return isset( $_COOKIE['keyboard-navigation'] ) && $_COOKIE['keyboard-navigation'] === 'true';
	}

	/**
	 * Вывод улучшенной навигации с клавиатуры
	 */
	public function output_enhanced_keyboard_navigation(): void {
		?>
		<script>
		(function() {
			'use strict';
			
			// Обнаружение навигации с клавиатуры
			document.addEventListener('keydown', function(e) {
				if (e.key === 'Tab' || e.keyCode === 9) {
					// Устанавливаем cookie для отслеживания навигации с клавиатуры
					document.body.classList.add('keyboard-navigation');
					document.documentElement.classList.add('keyboard-navigation');
					
					// Устанавливаем cookie на 30 минут
					var date = new Date();
					date.setTime(date.getTime() + (30 * 60 * 1000));
					document.cookie = "keyboard-navigation=true; expires=" + date.toUTCString() + "; path=/";
				}
			});
			
			// Обнаружение мышиной навигации
			document.addEventListener('mousedown', function() {
				document.body.classList.remove('keyboard-navigation');
				document.documentElement.classList.remove('keyboard-navigation');
				
				// Удаляем cookie
				document.cookie = "keyboard-navigation=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
			});
			
			// Улучшенное управление фокусом для модальных окон
			function setupModalFocusManagement() {
				const modals = document.querySelectorAll('[role="dialog"], .modal[aria-modal="true"]');
				
				modals.forEach(function(modal) {
					// Сохраняем элемент, который имел фокус до открытия модального окна
					let previouslyFocusedElement = document.activeElement;
					
					// Находим все интерактивные элементы в модальном окне
					const focusableElements = modal.querySelectorAll(
						'a[href], button:not(:disabled), input:not(:disabled), select:not(:disabled), textarea:not(:disabled), [tabindex]:not([tabindex="-1"])'
					);
					
					if (focusableElements.length === 0) return;
					
					const firstFocusable = focusableElements[0];
					const lastFocusable = focusableElements[focusableElements.length - 1];
					
					// Фокус на первый элемент при открытии модального окна
					if (modal.hasAttribute('aria-hidden') && modal.getAttribute('aria-hidden') === 'false') {
						firstFocusable.focus();
					}
					
					// Обработка Tab и Shift+Tab внутри модального окна
					modal.addEventListener('keydown', function(e) {
						if (e.key !== 'Tab') return;
						
						if (e.shiftKey && document.activeElement === firstFocusable) {
							// Если нажат Shift+Tab на первом элементе, перемещаем фокус на последний
							e.preventDefault();
							lastFocusable.focus();
						} else if (!e.shiftKey && document.activeElement === lastFocusable) {
							// Если нажат Tab на последнем элементе, перемещаем фокус на первый
							e.preventDefault();
							firstFocusable.focus();
						}
					});
					
					// Возвращаем фокус на элемент, который был сфокусирован до открытия модального окна
					modal.addEventListener('transitionend', function() {
						if (this.classList.contains('closed')) {
							if (previouslyFocusedElement && previouslyFocusedElement.focus) {
								previouslyFocusedElement.focus();
							}
						}
					});
				});
			}
			
			// Улучшенное объявление для вспомогательных технологий
			window.AtkA11yAnnouncer = {
				container: null,
				
				init: function() {
					// Создаем контейнер для объявления
					this.container = document.getElementById('a11y-announcer');
					
					if (!this.container) {
						this.container = document.createElement('div');
						this.container.id = 'a11y-announcer';
						this.container.setAttribute('aria-live', 'polite');
						this.container.setAttribute('aria-atomic', 'true');
						this.container.setAttribute('aria-relevant', 'additions text');
						this.container.style.position = 'absolute';
						this.container.style.left = '-10000px';
						this.container.style.top = 'auto';
						this.container.style.width = '1px';
						this.container.style.height = '1px';
						this.container.style.overflow = 'hidden';
						
						document.body.appendChild(this.container);
					}
				},
				
				announce: function(message, priority) {
					if (!this.container) {
						this.init();
					}
					
					// Устанавливаем приоритет
					if (priority === 'assertive') {
						this.container.setAttribute('aria-live', 'assertive');
					} else {
						this.container.setAttribute('aria-live', 'polite');
					}
					
					// Очищаем и устанавливаем новое сообщение
					this.container.textContent = '';
					// Даем браузеру немного времени, затем устанавливаем сообщение
					setTimeout(function() {
						window.AtkA11yAnnouncer.container.textContent = message;
					}, 100);
					
					// Сбрасываем приоритет обратно на polite
					if (priority === 'assertive') {
						setTimeout(function() {
							window.AtkA11yAnnouncer.container.setAttribute('aria-live', 'polite');
						}, 100);
					}
				}
			};
			
			// Инициализируем при загрузке DOM
			if (document.readyState === 'loading') {
				document.addEventListener('DOMContentLoaded', function() {
					setupModalFocusManagement();
					window.AtkA11yAnnouncer.init();
					enhanceKeyboardNavigation();
					enhanceFormButtonFocus();
				});
			} else {
				setupModalFocusManagement();
				window.AtkA11yAnnouncer.init();
				enhanceKeyboardNavigation();
				enhanceFormButtonFocus();
			}
			
			// Улучшенная навигация с клавиатуры для меню
			function enhanceKeyboardNavigation() {
				// Улучшаем навигацию для основного меню
				const mainNav = document.querySelector('.main-nav ul');
				if (mainNav) {
					const menuItems = mainNav.querySelectorAll('a');
					
					menuItems.forEach(item => {
						// Обработка нажатия Enter или Space
						item.addEventListener('keydown', function(e) {
							if (e.key === 'Enter' || e.key === ' ') {
								e.preventDefault();
								this.click();
							}
						});
						
						// Обработка стрелок для навигации
						item.addEventListener('keydown', function(e) {
							if (e.altKey || e.ctrlKey || e.metaKey) return;
							
							if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
								e.preventDefault();
								const nextItem = this.parentNode.nextElementSibling;
								if (nextItem && nextItem.querySelector('a')) {
									nextItem.querySelector('a').focus();
								} else {
									// Если это последний элемент, переходим к первому
									menuItems[0].focus();
								}
							} else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
								e.preventDefault();
								const prevItem = this.parentNode.previousElementSibling;
								if (prevItem && prevItem.querySelector('a')) {
									prevItem.querySelector('a').focus();
								} else {
									// Если это первый элемент, переходим к последнему
									menuItems[menuItems.length - 1].focus();
								}
							}
						});
					});
				}
				
				// Улучшаем доступность для мобильного меню
				const menuToggle = document.querySelector('.menu-toggle');
				if (menuToggle) {
					menuToggle.addEventListener('click', function() {
						const expanded = this.getAttribute('aria-expanded') === 'true';
						this.setAttribute('aria-expanded', !expanded);
						
						// При открытии меню перемещаем фокус на первое элемент меню
						if (!expanded) {
							setTimeout(() => {
								const firstMenuItem = document.querySelector('.main-nav a');
								if (firstMenuItem) {
									firstMenuItem.focus();
								}
							}, 100);
						}
					});
					
					// Обработка клавиш для кнопки переключения меню
					menuToggle.addEventListener('keydown', function(e) {
						if (e.key === 'Enter' || e.key === ' ') {
							e.preventDefault();
							this.click();
						}
					});
				}
			}
			
			// Улучшенная обработка ошибок форм для вспомогательных технологий
			function enhanceFormErrorHandling() {
				// Обновляем обработку ошибок форм
				document.addEventListener('invalid', function(e) {
					const field = e.target;
					const errorMessage = field.validationMessage;
					
					if (errorMessage) {
						// Добавляем ARIA-описание ошибки
						const errorId = field.id + '-error';
						let errorElement = document.getElementById(errorId);
						
						if (!errorElement) {
							errorElement = document.createElement('div');
							errorElement.id = errorId;
							errorElement.className = 'sr-only form-error-message';
							errorElement.setAttribute('aria-live', 'assertive');
							errorElement.setAttribute('role', 'alert');
							document.body.appendChild(errorElement);
						}
						
						errorElement.textContent = errorMessage;
						field.setAttribute('aria-describedby', errorId);
						
						// Объявляем ошибку через вспомогательные технологии
						if (window.AtkA11yAnnouncer && typeof window.AtkA11yAnnouncer.announce === 'function') {
							window.AtkA11yAnnouncer.announce(errorMessage, 'assertive');
						}
					}
				}, true);
				
				// Обработка успешных отправок форм
				document.addEventListener('submit', function(e) {
					const form = e.target;
					if (form.classList.contains('ajax-form')) {
						// При успешной отправке формы объявляем результат
						form.addEventListener('ajaxSuccess', function() {
							if (window.AtkA11yAnnouncer && typeof window.AtkA11yAnnouncer.announce === 'function') {
								window.AtkA11yAnnouncer.announce('<?php esc_attr_e( 'Форма успешно отправлена', 'atk-ved' ); ?>', 'polite');
							}
						});
					}
				});
			}
			
			// Улучшенное управление фокусом для кнопок форм
			function enhanceFormButtonFocus() {
				// Обеспечиваем правильное управление фокусом для кнопок "Оставить заявку"
				const requestButtons = document.querySelectorAll('.cta-button, .open-modal');
				
				requestButtons.forEach(button => {
					button.setAttribute('role', 'button');
					button.setAttribute('tabindex', '0');
					
					// Обработка клавиш для кнопок
					button.addEventListener('keydown', function(e) {
						if (e.key === 'Enter' || e.key === ' ') {
							e.preventDefault();
							this.click();
						}
					});
				});
			}
			
			
			
			// Инициализация улучшенной обработки ошибок форм
			enhanceFormErrorHandling();
			
			// Улучшенное восстановление фокуса после AJAX-запросов
			function enhanceAjaxFocusManagement() {
				// Сохраняем элемент, который имел фокус до AJAX-запроса
				let focusedElementBeforeAjax = null;
				
				// Перехватываем начало AJAX-запросов
				document.addEventListener('ajaxSend', function() {
					focusedElementBeforeAjax = document.activeElement;
				});
				
				// Восстанавливаем фокус после завершения AJAX-запроса
				document.addEventListener('ajaxComplete', function() {
					if (focusedElementBeforeAjax && focusedElementBeforeAjax.focus) {
						// Небольшая задержка для обеспечения обновления DOM
						setTimeout(function() {
							focusedElementBeforeAjax.focus();
						}, 100);
					}
				});
				
				// Также добавляем обработчик для обычных submit событий
				document.addEventListener('submit', function() {
					focusedElementBeforeAjax = document.activeElement;
				});
			}
			
			// Инициализация управления фокусом для AJAX
			enhanceAjaxFocusManagement();
			
			// Улучшенная поддержка пользовательских настроек доступности
			function applyUserAccessibilityPreferences() {
				// Проверяем сохраненные настройки пользователя
				const highContrast = localStorage.getItem('a11y-high-contrast');
				const fontSize = localStorage.getItem('a11y-font-size');
				const reducedMotion = localStorage.getItem('a11y-reduced-motion');
				
				if (highContrast === 'true') {
					document.documentElement.classList.add('high-contrast-mode');
				}
				
				if (fontSize) {
					document.documentElement.style.setProperty('--a11y-font-scale', fontSize);
				}
				
				if (reducedMotion === 'true') {
					document.documentElement.classList.add('reduced-motion-mode');
				}
			}
			
			// Функция переключения режима высокой контрастности
			function toggleHighContrast() {
				const html = document.documentElement;
				const body = document.body;
				const button = document.querySelector('.toggle-high-contrast');
				
				html.classList.toggle('high-contrast-mode');
				body.classList.toggle('high-contrast-mode');
				
				// Сохраняем состояние в localStorage
				const isHighContrast = html.classList.contains('high-contrast-mode');
				localStorage.setItem('a11y-high-contrast', isHighContrast);
				localStorage.setItem('highContrastMode', isHighContrast);
				
				// Обновляем aria-pressed атрибут
				if (button) {
					button.setAttribute('aria-pressed', isHighContrast);
					
					// Обновляем внешний вид кнопки
					if (isHighContrast) {
						button.classList.add('active');
					} else {
						button.classList.remove('active');
					}
				}
			}
			
			// Загружаем состояние высокой контрастности при инициализации
			function loadHighContrastSetting() {
				const savedSetting = localStorage.getItem('highContrastMode') || localStorage.getItem('a11y-high-contrast');
				if (savedSetting === 'true') {
					document.documentElement.classList.add('high-contrast-mode');
					document.body.classList.add('high-contrast-mode');
					const button = document.querySelector('.toggle-high-contrast');
					if (button) {
						button.classList.add('active');
						button.setAttribute('aria-pressed', 'true');
					}
				}
			}
			
			// Применяем настройки при загрузке
			applyUserAccessibilityPreferences();
			loadHighContrastSetting();
			
			// Добавляем функцию в глобальный объект для использования в других скриптах
			window.toggleHighContrast = toggleHighContrast;
		})();
		</script>
		<?php
	}

	/**
	 * Вывод улучшений для вспомогательных технологий
	 */
	public function output_a11y_improvements(): void {
		?>
		<style>
		/* Улучшения для пользователей с нарушениями зрения */
		.high-contrast-mode {
			filter: contrast(1.5) brightness(1.1);
		}
		
		/* Улучшения для пользователей с нарушениями восприятия движений */
		.reduced-motion-mode * {
			animation-duration: 0.01ms !important;
			animation-iteration-count: 1 !important;
			transition-duration: 0.01ms !important;
		}
		
		/* Улучшенные индикаторы фокуса */
		.keyboard-navigation :focus,
		.keyboard-navigation :focus-visible {
			outline: 3px solid #005fcc;
			outline-offset: 2px;
			border-radius: 2px;
		}
		
		/* Улучшенные индикаторы фокуса для темной темы */
		.dark-mode.keyboard-navigation :focus,
		.dark-mode.keyboard-navigation :focus-visible {
			outline: 3px solid #4d9eff;
			outline-offset: 2px;
		}
		
		/* Улучшенная видимость элементов управления */
		.keyboard-navigation .control-element,
		.keyboard-navigation button,
		.keyboard-navigation a,
		.keyboard-navigation input,
		.keyboard-navigation select,
		.keyboard-navigation textarea,
		.keyboard-navigation [tabindex]:not([tabindex="-1"]) {
			position: relative;
		}
		
		.keyboard-navigation .control-element::after,
		.keyboard-navigation button::after,
		.keyboard-navigation a::after,
		.keyboard-navigation input::after,
		.keyboard-navigation select::after,
		.keyboard-navigation textarea::after,
		.keyboard-navigation [tabindex]::after {
			content: '';
			position: absolute;
			top: -2px;
			left: -2px;
			right: -2px;
			bottom: -2px;
			border: 2px solid transparent;
			pointer-events: none;
		}
		
		.keyboard-navigation .control-element:focus::after,
		.keyboard-navigation button:focus::after,
		.keyboard-navigation a:focus::after,
		.keyboard-navigation input:focus::after,
		.keyboard-navigation select:focus::after,
		.keyboard-navigation textarea:focus::after,
		.keyboard-navigation [tabindex]:focus::after {
			border-color: #005fcc;
		}
		
		/* Тема для высокой контрастности */
		.high-contrast-mode {
			background: #000 !important;
			color: #fff !important;
		}
		
		.high-contrast-mode a,
		.high-contrast-mode button,
		.high-contrast-mode input,
		.high-contrast-mode select,
		.high-contrast-mode textarea {
			background: #fff !important;
			color: #000 !important;
			border: 2px solid #fff !important;
		}
		
		.high-contrast-mode .btn,
		.high-contrast-mode .button {
			background: #fff !important;
			color: #000 !important;
			border: 2px solid #000 !important;
		}
		
		.high-contrast-mode .btn:hover,
		.high-contrast-mode .button:hover {
			background: #000 !important;
			color: #fff !important;
		}
		
		/* Скрытие вспомогательных элементов */
		.sr-only {
			position: absolute !important;
			width: 1px !important;
			height: 1px !important;
			padding: 0 !important;
			margin: -1px !important;
			overflow: hidden !important;
			clip: rect(0, 0, 0, 0) !important;
			white-space: nowrap !important;
			border: 0 !important;
		}
		
		.sr-only.focusable:active,
		.sr-only.focusable:focus {
			position: static !important;
			width: auto !important;
			height: auto !important;
			overflow: visible !important;
			clip: auto !important;
			white-space: normal !important;
		}
		</style>
		<?php
	}
}

// Инициализация
function atk_ved_init_accessibility_enhancements(): void {
	ATK_VED_Accessibility_Enhancements::get_instance();
}
add_action( 'after_setup_theme', 'atk_ved_init_accessibility_enhancements' );

// Функция для улучшения доступности форм
function atk_ved_enhance_form_accessibility( $form ) {
	// Добавляем ARIA-описания к полям формы
	$form = preg_replace_callback(
		'/<input([^>]+)>|<textarea([^>]+)>|<select([^>]+)>/',
		function ( $matches ) {
			$tag        = $matches[0];
			$attributes = isset( $matches[1] ) ? $matches[1] : ( isset( $matches[2] ) ? $matches[2] : $matches[3] );

			// Проверяем, содержит ли тег уже ARIA-атрибуты
			if ( strpos( $attributes, 'aria-describedby' ) !== false || strpos( $attributes, 'aria-label' ) !== false || strpos( $attributes, 'aria-labelledby' ) !== false ) {
				return $tag; // Уже имеет ARIA-описание
			}

			// Находим id поля
			preg_match( '/id=["\']([^"\']*)["\']/', $attributes, $id_matches );
			if ( ! empty( $id_matches[1] ) ) {
				$field_id = $id_matches[1];

				// Находим соответствующий label
				preg_match( '/<label[^>]+for=["\']' . preg_quote( $field_id ) . '["\'][^>]*>(.*?)<\/label>/', $tag . $tag, $label_matches );
				if ( empty( $label_matches[1] ) ) {
					// Ищем label в предыдущем HTML
					preg_match( '/<label[^>]+for=["\']' . preg_quote( $field_id ) . '["\'][^>]*>(.*?)<\/label>/', $tag, $label_matches );
				}

				if ( ! empty( $label_matches[1] ) ) {
					$label_text = trim( strip_tags( $label_matches[1] ) );
					$error_id   = $field_id . '-error';

					// Добавляем aria-labelledby вместо aria-describedby для связи с лейблом
					if ( strpos( $tag, 'aria-labelledby' ) === false && strpos( $tag, 'aria-label' ) === false ) {
						$label_id    = $field_id . '-label';
						$updated_tag = str_replace( '<' . substr( $tag, 1, strpos( $tag, ' ' ) - 1 ), '<' . substr( $tag, 1, strpos( $tag, ' ' ) - 1 ) . ' aria-labelledby="' . $label_id . '"', $tag );
						return $updated_tag;
					}
				}
			}

			return $tag;
		},
		$form
	);

	// Обновляем label'ы, чтобы они имели id для связи с полями
	$form = preg_replace_callback(
		'/<label([^>]*)for=["\']([^"\']*)["\']([^>]*)>/',
		function ( $matches ) {
			$full_tag   = $matches[0];
			$before_for = $matches[1];
			$field_id   = $matches[2];
			$after_for  = $matches[3];

			$label_id = $field_id . '-label';

			// Проверяем, есть ли уже id у label
			if ( strpos( $before_for, 'id=' ) === false && strpos( $after_for, 'id=' ) === false ) {
				$updated_tag = str_replace( '<label', '<label id="' . $label_id . '"', $full_tag );
				return $updated_tag;
			}

			return $full_tag;
		},
		$form
	);

	return $form;
}

// Хук для улучшения доступности форм
add_filter( 'the_content', 'atk_ved_enhance_form_accessibility' );
add_filter( 'widget_text', 'atk_ved_enhance_form_accessibility' );
