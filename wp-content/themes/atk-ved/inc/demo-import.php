<?php
/**
 * Demo Content Importer
 * Импорт демонстрационного контента для быстрой настройки сайта
 *
 * @package ATK_VED
 * @since 2.9.0
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Класс для импорта демо контента
 */
class ATK_VED_Demo_Importer {

	/**
	 * Импорт всех данных
	 */
	public static function import_all(): array {
		$results = array(
			'options'    => self::import_options(),
			'pages'      => self::import_pages(),
			'menu'       => self::import_menu(),
			'widgets'    => self::import_widgets(),
			'customizer' => self::import_customizer(),
		);

		// Очистка кэша
		wp_cache_flush();

		return $results;
	}

	/**
	 * Импорт настроек
	 */
	public static function import_options(): array {
		$options = array(
			'blogname'        => 'АТК ВЭД',
			'blogdescription' => 'Товары для маркетплейсов из Китая оптом',
			'timezone_string' => 'Europe/Moscow',
			'date_format'     => 'd.m.Y',
			'time_format'     => 'H:i',
			'start_of_week'   => 1,
		);

		$imported = 0;
		$errors   = 0;

		foreach ( $options as $option => $value ) {
			if ( update_option( $option, $value ) ) {
				++$imported;
			} else {
				++$errors;
			}
		}

		return array(
			'imported' => $imported,
			'errors'   => $errors,
		);
	}

	/**
	 * Импорт страниц
	 */
	public static function import_pages(): array {
		$pages = array(
			array(
				'post_title'    => 'Главная',
				'post_content'  => '<!-- wp:atk-ved/hero /--><!-- wp:atk-ved/services /-->',
				'post_type'     => 'page',
				'post_status'   => 'publish',
				'page_template' => 'front-page.php',
			),
			array(
				'post_title'   => 'О компании',
				'post_content' => 'Информация о компании АТК ВЭД...',
				'post_type'    => 'page',
				'post_status'  => 'publish',
			),
			array(
				'post_title'   => 'Услуги',
				'post_content' => '<!-- wp:atk-ved/services /-->',
				'post_type'    => 'page',
				'post_status'  => 'publish',
			),
			array(
				'post_title'   => 'Доставка',
				'post_content' => 'Информация о доставке...',
				'post_type'    => 'page',
				'post_status'  => 'publish',
			),
			array(
				'post_title'   => 'Контакты',
				'post_content' => '<!-- wp:atk-ved/contact /-->',
				'post_type'    => 'page',
				'post_status'  => 'publish',
			),
			array(
				'post_title'   => 'Политика конфиденциальности',
				'post_content' => self::get_privacy_policy_content(),
				'post_type'    => 'page',
				'post_status'  => 'publish',
			),
		);

		$imported = 0;
		$errors   = 0;
		$page_ids = array();

		foreach ( $pages as $page ) {
			$page_id = wp_insert_post( $page );

			if ( $page_id && ! is_wp_error( $page_id ) ) {
				++$imported;
				$page_ids[ sanitize_title( $page['post_title'] ) ] = $page_id;

				// Добавляем мета-данные
				update_post_meta( $page_id, '_atk_demo_imported', true );
			} else {
				++$errors;
			}
		}

		// Установка главной страницы
		if ( isset( $page_ids['glavnaia'] ) ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $page_ids['glavnaia'] );
		}

		return array(
			'imported' => $imported,
			'errors'   => $errors,
			'page_ids' => $page_ids,
		);
	}

	/**
	 * Импорт меню
	 */
	public static function import_menu(): array {
		// Создание меню
		$menu_name = 'Главное меню';
		$menu_id   = wp_create_nav_menu( $menu_name );

		if ( is_wp_error( $menu_id ) ) {
			return array(
				'imported' => 0,
				'errors'   => 1,
			);
		}

		// Добавление пунктов меню
		$menu_items = array(
			array(
				'title' => 'Услуги',
				'url'   => '#services',
			),
			array(
				'title' => 'Доставка',
				'url'   => '#delivery',
			),
			array(
				'title' => 'Этапы',
				'url'   => '#steps',
			),
			array(
				'title' => 'FAQ',
				'url'   => '#faq',
			),
			array(
				'title' => 'Контакты',
				'url'   => '#contact',
			),
		);

		$imported = 0;

		foreach ( $menu_items as $item ) {
			$menu_item_id = wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'  => $item['title'],
					'menu-item-url'    => $item['url'],
					'menu-item-status' => 'publish',
					'menu-item-type'   => 'custom',
				)
			);

			if ( $menu_item_id && ! is_wp_error( $menu_item_id ) ) {
				++$imported;
			}
		}

		// Назначение меню на локацию
		$locations            = get_theme_mod( 'nav_menu_locations' );
		$locations['primary'] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );

		return array(
			'imported' => $imported,
			'menu_id'  => $menu_id,
		);
	}

	/**
	 * Импорт виджетов
	 */
	public static function import_widgets(): array {
		$widgets = array(
			'sidebar-1' => array(
				'search'       => array( 'title' => 'Поиск' ),
				'recent-posts' => array( 'title' => 'Последние записи' ),
				'categories'   => array( 'title' => 'Рубрики' ),
			),
		);

		$imported = 0;

		foreach ( $widgets as $sidebar => $sidebar_widgets ) {
			foreach ( $sidebar_widgets as $widget_type => $widget_data ) {
				$widget_key = "{$widget_type}-" . (string) time();

				update_option(
					"widget_{$widget_type}",
					array(
						$widget_key    => $widget_data,
						'_multiwidget' => 1,
					)
				);

				++$imported;
			}
		}

		return array( 'imported' => $imported );
	}

	/**
	 * Импорт настроек Customizer
	 */
	public static function import_customizer(): array {
		$mods = array(
			'atk_ved_phone'                      => '+7 (999) 123-45-67',
			'atk_ved_email'                      => 'info@atk-ved.ru',
			'atk_ved_address'                    => 'Москва, Россия',
			'atk_ved_header_working_hours'       => 'Пн-Пт 9:00-18:00',
			'atk_ved_hero_title'                 => 'ТОВАРЫ ДЛЯ МАРКЕТПЛЕЙСОВ ИЗ КИТАЯ ОПТОМ',
			'atk_ved_hero_subtitle'              => 'Полный цикл поставок от поиска до доставки',
			'atk_ved_exit_popup_enabled'         => false,
			'atk_ved_live_notifications_enabled' => false,
			'atk_ved_chat_enabled'               => false,
		);

		$imported = 0;

		foreach ( $mods as $mod => $value ) {
			set_theme_mod( $mod, $value );
			++$imported;
		}

		return array( 'imported' => $imported );
	}

	/**
	 * Текст политики конфиденциальности
	 */
	private static function get_privacy_policy_content(): string {
		return '
<h1>Политика конфиденциальности</h1>
<p>Дата обновления: ' . date( 'd.m.Y' ) . '</p>

<h2>1. Общие положения</h2>
<p>Настоящая политика обработки персональных данных составлена в соответствии с требованиями Федерального закона от 27.07.2006. №152-ФЗ «О персональных данных»...</p>

<h2>2. Сбор и обработка данных</h2>
<p>Мы собираем следующие персональные данные:</p>
<ul>
    <li>Имя и фамилия</li>
    <li>Номер телефона</li>
    <li>Адрес электронной почты</li>
</ul>

<h2>3. Использование данных</h2>
<p>Собранные данные используются для:</p>
<ul>
    <li>Обработки заказов</li>
    <li>Связи с клиентами</li>
    <li>Отправки уведомлений</li>
</ul>

<h2>4. Защита данных</h2>
<p>Мы принимаем все необходимые меры для защиты ваших персональных данных...</p>

<h2>5. Контакты</h2>
<p>По вопросам обработки персональных данных обращайтесь: ' . get_option( 'admin_email' ) . '</p>
';
	}

	/**
	 * Проверка возможности импорта
	 */
	public static function can_import(): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Проверка уже импортированного контента
	 */
	public static function is_imported(): bool {
		return get_option( 'atk_ved_demo_imported', false );
	}

	/**
	 * Сброс импортированного контента
	 */
	public static function reset_import(): bool {
		// Удаление страниц с меткой demo
		$demo_pages = get_posts(
			array(
				'post_type'      => 'any',
				'meta_key'       => '_atk_demo_imported',
				'posts_per_page' => -1,
			)
		);

		foreach ( $demo_pages as $page ) {
			wp_delete_post( $page->ID, true );
		}

		// Удаление меню
		$menus = wp_get_nav_menus();
		foreach ( $menus as $menu ) {
			if ( $menu->name === 'Главное меню' ) {
				wp_delete_nav_menu( $menu->term_id );
			}
		}

		// Сброс опций
		delete_option( 'atk_ved_demo_imported' );

		return true;
	}
}

/**
 * AJAX обработчик импорта
 */
function atk_ved_ajax_import_demo(): void {
	check_ajax_referer( 'atk_ved_nonce', 'nonce' );

	if ( ! ATK_VED_Demo_Importer::can_import() ) {
		wp_send_json_error( array( 'message' => __( 'Недостаточно прав', 'atk-ved' ) ) );
	}

	$results = ATK_VED_Demo_Importer::import_all();

	update_option( 'atk_ved_demo_imported', true );

	wp_send_json_success(
		array(
			'message' => __( 'Демо контент успешно импортирован!', 'atk-ved' ),
			'results' => $results,
		)
	);
}
add_action( 'wp_ajax_atk_ved_import_demo', 'atk_ved_ajax_import_demo' );

/**
 * AJAX обработчик сброса
 */
function atk_ved_ajax_reset_demo(): void {
	check_ajax_referer( 'atk_ved_nonce', 'nonce' );

	if ( ! ATK_VED_Demo_Importer::can_import() ) {
		wp_send_json_error( array( 'message' => __( 'Недостаточно прав', 'atk-ved' ) ) );
	}

	ATK_VED_Demo_Importer::reset_import();

	wp_send_json_success( array( 'message' => __( 'Демо контент сброшен', 'atk-ved' ) ) );
}
add_action( 'wp_ajax_atk_ved_reset_demo', 'atk_ved_ajax_reset_demo' );

/**
 * Страница импорта демо контента в админке
 */
function atk_ved_demo_import_page(): void {
	$is_imported = ATK_VED_Demo_Importer::is_imported();
	?>
	<div class="wrap atk-demo-import">
		<h1><?php _e( 'Импорт демо контента', 'atk-ved' ); ?></h1>
		
		<div class="atk-demo-card">
			<h2><?php _e( 'Быстрая настройка сайта', 'atk-ved' ); ?></h2>
			<p><?php _e( 'Импортируйте демонстрационный контент для быстрой настройки сайта:', 'atk-ved' ); ?></p>
			
			<ul class="demo-features">
				<li>✅ <?php _e( 'Страницы (Главная, О компании, Услуги, Контакты)', 'atk-ved' ); ?></li>
				<li>✅ <?php _e( 'Меню навигации', 'atk-ved' ); ?></li>
				<li>✅ <?php _e( 'Настройки темы', 'atk-ved' ); ?></li>
				<li>✅ <?php _e( 'Виджеты', 'atk-ved' ); ?></li>
				<li>✅ <?php _e( 'Политика конфиденциальности', 'atk-ved' ); ?></li>
			</ul>
			
			<?php if ( ! $is_imported ) : ?>
				<button type="button" class="button button-primary button-hero" id="importDemo">
					<?php _e( 'Импортировать демо контент', 'atk-ved' ); ?>
				</button>
			<?php else : ?>
				<div class="notice notice-success inline">
					<p><?php _e( 'Демо контент уже импортирован', 'atk-ved' ); ?></p>
				</div>
				<button type="button" class="button button-secondary" id="resetDemo">
					<?php _e( 'Сбросить и импортировать заново', 'atk-ved' ); ?>
				</button>
			<?php endif; ?>
			
			<div id="importProgress" style="display: none; margin-top: 20px;">
				<div class="spinner is-active"></div>
				<p><?php _e( 'Идёт импорт...', 'atk-ved' ); ?></p>
			</div>
			
			<div id="importResult" style="margin-top: 20px;"></div>
		</div>
		
		<div class="atk-demo-warning">
			<h3>⚠️ <?php _e( 'Внимание!', 'atk-ved' ); ?></h3>
			<p><?php _e( 'Импорт перезапишет некоторые настройки сайта. Рекомендуется использовать на новом сайте.', 'atk-ved' ); ?></p>
		</div>
		
		<style>
			.atk-demo-import { max-width: 800px; }
			.atk-demo-card {
				background: #fff;
				padding: 30px;
				border-radius: 8px;
				box-shadow: 0 2px 8px rgba(0,0,0,0.1);
				margin-bottom: 20px;
			}
			.demo-features {
				list-style: none;
				padding: 0;
				margin: 20px 0;
			}
			.demo-features li {
				padding: 10px 0;
				border-bottom: 1px solid #f0f0f0;
			}
			.atk-demo-warning {
				background: #fff3cd;
				border-left: 4px solid #ffc107;
				padding: 20px;
				border-radius: 4px;
			}
			.atk-demo-warning h3 { margin: 0 0 10px; }
			.atk-demo-warning p { margin: 0; }
			#importResult .success { color: #28a745; }
			#importResult .error { color: #dc3545; }
		</style>
		
		<script>
		jQuery(document).ready(function($) {
			$('#importDemo').on('click', function() {
				if (!confirm('<?php _e( 'Вы уверены? Это перезапишет некоторые настройки.', 'atk-ved' ); ?>')) {
					return;
				}
				
				$('#importProgress').show();
				$('#importResult').hide();
				
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'atk_ved_import_demo',
						nonce: '<?php echo wp_create_nonce( 'atk_ved_nonce' ); ?>'
					},
					success: function(response) {
						$('#importProgress').hide();
						$('#importResult').show();
						
						if (response.success) {
							$('#importResult').html(
								'<div class="success"><strong>✓ ' + response.data.message + '</strong></div>' +
								'<pre>' + JSON.stringify(response.data.results, null, 2) + '</pre>'
							);
							setTimeout(function() { location.reload(); }, 2000);
						} else {
							$('#importResult').html('<div class="error">✗ ' + response.data.message + '</div>');
						}
					},
					error: function() {
						$('#importProgress').hide();
						$('#importResult').show();
						$('#importResult').html('<div class="error">✗ <?php _e( 'Ошибка импорта', 'atk-ved' ); ?></div>');
					}
				});
			});
			
			$('#resetDemo').on('click', function() {
				if (!confirm('<?php _e( 'Вы уверены? Весь демо контент будет удалён.', 'atk-ved' ); ?>')) {
					return;
				}
				
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'atk_ved_reset_demo',
						nonce: '<?php echo wp_create_nonce( 'atk_ved_nonce' ); ?>'
					},
					success: function(response) {
						if (response.success) {
							location.reload();
						}
					}
				});
			});
		});
		</script>
	</div>
	<?php
}

/**
 * Добавление страницы импорта в меню
 */
function atk_ved_add_demo_import_menu(): void {
	add_theme_page(
		__( 'Импорт демо', 'atk-ved' ),
		__( 'Импорт демо', 'atk-ved' ),
		'manage_options',
		'atk-ved-demo-import',
		'atk_ved_demo_import_page'
	);
}
add_action( 'admin_menu', 'atk_ved_add_demo_import_menu' );

/**
 * Уведомление о возможности импорта
 */
function atk_ved_demo_import_notice(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! ATK_VED_Demo_Importer::is_imported() ) {
		?>
		<div class="notice notice-info is-dismissible">
			<p>
				<strong><?php _e( 'АТК ВЭД: Быстрая настройка', 'atk-ved' ); ?></strong><br>
				<?php _e( 'Импортируйте демо контент для быстрой настройки сайта.', 'atk-ved' ); ?>
				<a href="<?php echo admin_url( 'themes.php?page=atk-ved-demo-import' ); ?>" class="button">
					<?php _e( 'Импортировать', 'atk-ved' ); ?>
				</a>
			</p>
		</div>
		<?php
	}
}
add_action( 'admin_notices', 'atk_ved_demo_import_notice' );
