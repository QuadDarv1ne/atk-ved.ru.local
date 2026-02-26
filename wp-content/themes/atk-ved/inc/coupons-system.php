<?php
/**
 * Advanced Coupons & Discounts System
 * Продвинутая система промокодов и скидок
 *
 * @package ATK_VED
 * @since 3.4.0
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Создание таблицы промокодов
 */
function atk_ved_create_coupons_table(): void {
	global $wpdb;

	$table_name      = $wpdb->prefix . 'atk_ved_coupons';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        code varchar(50) NOT NULL UNIQUE,
        description text,
        discount_type varchar(20) NOT NULL DEFAULT 'percent',
        discount_value decimal(10,2) NOT NULL,
        min_order_amount decimal(10,2) DEFAULT 0,
        max_discount_amount decimal(10,2) DEFAULT 0,
        usage_limit int(11) DEFAULT 0,
        usage_count int(11) DEFAULT 0,
        user_usage_limit int(11) DEFAULT 1,
        valid_from datetime DEFAULT NULL,
        valid_until datetime DEFAULT NULL,
        applicable_products text,
        applicable_categories text,
        excluded_products text,
        excluded_categories text,
        status varchar(20) DEFAULT 'active',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY code (code),
        KEY status (status),
        KEY valid_from (valid_from),
        KEY valid_until (valid_until)
    ) {$charset_collate};";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );

	// Таблица использования промокодов
	$usage_table = $wpdb->prefix . 'atk_ved_coupon_usage';

	$sql_usage = "CREATE TABLE IF NOT EXISTS {$usage_table} (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        coupon_id bigint(20) NOT NULL,
        user_id bigint(20) DEFAULT 0,
        user_email varchar(100) DEFAULT '',
        order_id bigint(20) DEFAULT 0,
        discount_amount decimal(10,2) NOT NULL,
        used_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY coupon_id (coupon_id),
        KEY user_id (user_id),
        KEY order_id (order_id),
        KEY used_at (used_at)
    ) {$charset_collate};";

	dbDelta( $sql_usage );
}
register_activation_hook( __FILE__, 'atk_ved_create_coupons_table' );
add_action( 'plugins_loaded', 'atk_ved_create_coupons_table' );

/**
 * AJAX: Проверка и применение промокода
 */
function atk_ved_ajax_apply_coupon(): void {
	check_ajax_referer( 'atk_ved_nonce', 'nonce' );

	$code       = sanitize_text_field( $_POST['code'] ?? '' );
	$cart_total = floatval( $_POST['cart_total'] ?? 0 );
	$user_id    = get_current_user_id();
	$user_email = wp_get_current_user()->user_email;

	if ( empty( $code ) ) {
		wp_send_json_error( array( 'message' => __( 'Введите промокод', 'atk-ved' ) ) );
	}

	$result = atk_ved_validate_coupon( $code, $cart_total, $user_id, $user_email );

	if ( $result['valid'] ) {
		wp_send_json_success(
			array(
				'message'             => __( 'Промокод применён', 'atk-ved' ),
				'discount'            => $result['discount'],
				'discount_formatted'  => wc_price( $result['discount'] ),
				'new_total'           => $cart_total - $result['discount'],
				'new_total_formatted' => wc_price( $cart_total - $result['discount'] ),
			)
		);
	} else {
		wp_send_json_error( array( 'message' => $result['message'] ) );
	}
}
add_action( 'wp_ajax_atk_ved_apply_coupon', 'atk_ved_ajax_apply_coupon' );
add_action( 'wp_ajax_nopriv_atk_ved_apply_coupon', 'atk_ved_ajax_apply_coupon' );

/**
 * Валидация промокода
 */
function atk_ved_validate_coupon( string $code, float $cart_total = 0, int $user_id = 0, string $user_email = '' ): array {
	global $wpdb;

	$table_name = $wpdb->prefix . 'atk_ved_coupons';

	$coupon = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM {$table_name} WHERE code = %s AND status = 'active'",
			strtoupper( $code )
		)
	);

	if ( ! $coupon ) {
		return array(
			'valid'   => false,
			'message' => __( 'Промокод не найден', 'atk-ved' ),
		);
	}

	// Проверка даты действия
	$now = current_time( 'mysql' );

	if ( $coupon->valid_from && $now < $coupon->valid_from ) {
		return array(
			'valid'   => false,
			'message' => __( 'Промокод ещё не активен', 'atk-ved' ),
		);
	}

	if ( $coupon->valid_until && $now > $coupon->valid_until ) {
		return array(
			'valid'   => false,
			'message' => __( 'Срок действия промокода истёк', 'atk-ved' ),
		);
	}

	// Проверка лимита использований
	if ( $coupon->usage_limit > 0 && $coupon->usage_count >= $coupon->usage_limit ) {
		return array(
			'valid'   => false,
			'message' => __( 'Лимит использований промокода исчерпан', 'atk-ved' ),
		);
	}

	// Проверка минимальной суммы заказа
	if ( $coupon->min_order_amount > 0 && $cart_total < $coupon->min_order_amount ) {
		return array(
			'valid'   => false,
			'message' => sprintf(
				__( 'Минимальная сумма заказа для этого промокода: %s', 'atk-ved' ),
				wc_price( $coupon->min_order_amount )
			),
		);
	}

	// Проверка лимита на пользователя
	if ( $user_id > 0 || ! empty( $user_email ) ) {
		$usage_table = $wpdb->prefix . 'atk_ved_coupon_usage';

		$user_usage = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$usage_table} WHERE coupon_id = %d AND (user_id = %d OR user_email = %s)",
				$coupon->id,
				$user_id,
				$user_email
			)
		);

		if ( $coupon->user_usage_limit > 0 && $user_usage >= $coupon->user_usage_limit ) {
			return array(
				'valid'   => false,
				'message' => __( 'Вы уже использовали этот промокод', 'atk-ved' ),
			);
		}
	}

	// Расчёт скидки
	$discount = atk_ved_calculate_coupon_discount( $coupon, $cart_total );

	// Проверка максимального размера скидки
	if ( $coupon->max_discount_amount > 0 && $discount > $coupon->max_discount_amount ) {
		$discount = $coupon->max_discount_amount;
	}

	return array(
		'valid'    => true,
		'message'  => __( 'Промокод применён', 'atk-ved' ),
		'discount' => $discount,
		'coupon'   => $coupon,
	);
}

/**
 * Расчёт размера скидки
 */
function atk_ved_calculate_coupon_discount( object $coupon, float $cart_total ): float {
	switch ( $coupon->discount_type ) {
		case 'percent':
			$discount = ( $cart_total * $coupon->discount_value ) / 100;
			break;

		case 'fixed':
			$discount = $coupon->discount_value;
			break;

		case 'shipping':
			// Скидка на доставку (реализуется через WooCommerce)
			$discount = 0;
			break;

		default:
			$discount = 0;
	}

	// Скидка не может быть больше суммы заказа
	return min( $discount, $cart_total );
}

/**
 * Применение промокода к заказу
 */
function atk_ved_apply_coupon_to_order( int $coupon_id, int $order_id, float $discount, int $user_id = 0, string $user_email = '' ): void {
	global $wpdb;

	$table_name  = $wpdb->prefix . 'atk_ved_coupons';
	$usage_table = $wpdb->prefix . 'atk_ved_coupon_usage';

	// Увеличиваем счётчик использований
	$wpdb->query(
		$wpdb->prepare(
			"UPDATE {$table_name} SET usage_count = usage_count + 1 WHERE id = %d",
			$coupon_id
		)
	);

	// Записываем использование
	$wpdb->insert(
		$usage_table,
		array(
			'coupon_id'       => $coupon_id,
			'user_id'         => $user_id,
			'user_email'      => $user_email,
			'order_id'        => $order_id,
			'discount_amount' => $discount,
		)
	);
}

/**
 * Шорткод: Форма ввода промокода
 */
function atk_ved_coupon_form_shortcode(): string {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return '';
	}

	ob_start();
	?>
	<div class="atk-coupon-form">
		<h4><?php _e( 'Есть промокод?', 'atk-ved' ); ?></h4>
		<div class="coupon-input-group">
			<input type="text" id="couponCode" placeholder="<?php esc_attr_e( 'Введите код', 'atk-ved' ); ?>" autocomplete="off">
			<button type="button" id="applyCouponBtn"><?php _e( 'Применить', 'atk-ved' ); ?></button>
		</div>
		<div class="coupon-message"></div>
		<div class="coupon-applied" style="display: none;">
			<span class="coupon-code"></span>
			<span class="coupon-discount"></span>
			<button type="button" class="remove-coupon">×</button>
		</div>
	</div>
	
	<style>
	.atk-coupon-form {
		margin: 30px 0;
		padding: 25px;
		background: #f8f9fa;
		border-radius: 12px;
	}
	
	.atk-coupon-form h4 {
		margin: 0 0 15px;
		font-size: 18px;
	}
	
	.coupon-input-group {
		display: flex;
		gap: 10px;
	}
	
	.coupon-input-group input {
		flex: 1;
		padding: 12px 15px;
		border: 2px solid #e0e0e0;
		border-radius: 8px;
		font-size: 15px;
	}
	
	.coupon-input-group input:focus {
		outline: none;
		border-color: #e31e24;
	}
	
	.coupon-input-group button {
		padding: 12px 25px;
		background: #e31e24;
		color: #fff;
		border: none;
		border-radius: 8px;
		font-weight: 600;
		cursor: pointer;
		transition: all 0.3s;
	}
	
	.coupon-input-group button:hover {
		background: #c01a1f;
		transform: translateY(-2px);
	}
	
	.coupon-message {
		margin-top: 15px;
		font-size: 14px;
	}
	
	.coupon-message.success { color: #4CAF50; }
	.coupon-message.error { color: #f44336; }
	
	.coupon-applied {
		display: flex;
		align-items: center;
		gap: 15px;
		margin-top: 15px;
		padding: 15px;
		background: #e8f5e9;
		border-radius: 8px;
		border-left: 4px solid #4CAF50;
	}
	
	.coupon-code {
		font-weight: 700;
		color: #2E7D32;
	}
	
	.coupon-discount {
		color: #666;
	}
	
	.remove-coupon {
		margin-left: auto;
		background: transparent;
		border: none;
		font-size: 20px;
		cursor: pointer;
		color: #999;
	}
	
	.remove-coupon:hover {
		color: #f44336;
	}
	</style>
	
	<script>
	(function($) {
		'use strict';
		
		let appliedCoupon = null;
		
		$('#applyCouponBtn').on('click', function() {
			const code = $('#couponCode').val().trim();
			
			if (!code) {
				$('.coupon-message').removeClass('success').addClass('error').text('<?php _e( 'Введите код промокода', 'atk-ved' ); ?>');
				return;
			}
			
			$.ajax({
				url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
				type: 'POST',
				data: {
					action: 'atk_ved_apply_coupon',
					nonce: '<?php echo wp_create_nonce( 'atk_ved_nonce' ); ?>',
					code: code,
					cart_total: '<?php echo WC()->cart ? WC()->cart->get_total( 'edit' ) : 0; ?>'
				},
				success: function(response) {
					if (response.success) {
						appliedCoupon = { code: code, discount: response.data.discount };
						
						$('.coupon-message').removeClass('error').addClass('success').text(response.data.message);
						$('.coupon-applied').show();
						$('.coupon-code').text(code);
						$('.coupon-discount').text('-' + response.data.discount_formatted);
						
						// Обновление корзины (WooCommerce)
						$(document.body).trigger('update_checkout');
					} else {
						$('.coupon-message').removeClass('success').addClass('error').text(response.data.message);
					}
				},
				error: function() {
					$('.coupon-message').removeClass('success').addClass('error').text('<?php _e( 'Ошибка применения промокода', 'atk-ved' ); ?>');
				}
			});
		});
		
		$(document).on('click', '.remove-coupon', function() {
			appliedCoupon = null;
			$('#couponCode').val('');
			$('.coupon-applied').hide();
			$('.coupon-message').text('');
			
			$(document.body).trigger('update_checkout');
		});
		
	})(jQuery);
	</script>
	<?php
	return ob_get_clean();
}
add_shortcode( 'atk_coupon_form', 'atk_ved_coupon_form_shortcode' );

/**
 * Интеграция с WooCommerce
 */
function atk_ved_woocommerce_coupon_integration( $cart_discount, $coupon_code ) {
	// Проверяем наш промокод
	$result = atk_ved_validate_coupon( $coupon_code, WC()->cart->get_total( 'edit' ), get_current_user_id(), wp_get_current_user()->user_email );

	if ( $result['valid'] ) {
		return $result['discount'];
	}

	return $cart_discount;
}
// add_filter('woocommerce_cart_discount', 'atk_ved_woocommerce_coupon_integration', 10, 2);

/**
 * Админка: Управление промокодами
 */
function atk_ved_coupons_admin_menu(): void {
	add_submenu_page(
		'woocommerce',
		__( 'Промокоды', 'atk-ved' ),
		__( 'Промокоды', 'atk-ved' ),
		'manage_woocommerce',
		'atk-ved-coupons',
		'atk_ved_coupons_admin_page'
	);
}
add_action( 'admin_menu', 'atk_ved_coupons_admin_menu' );

/**
 * Админка: Страница промокодов
 */
function atk_ved_coupons_admin_page(): void {
	global $wpdb;

	$table_name = $wpdb->prefix . 'atk_ved_coupons';

	// Обработка создания/редактирования
	if ( isset( $_POST['atk_create_coupon'] ) && wp_verify_nonce( $_POST['atk_coupon_nonce'], 'atk_create_coupon' ) ) {
		$coupon_data = array(
			'code'                => strtoupper( sanitize_text_field( $_POST['code'] ) ),
			'description'         => sanitize_textarea_field( $_POST['description'] ),
			'discount_type'       => sanitize_text_field( $_POST['discount_type'] ),
			'discount_value'      => floatval( $_POST['discount_value'] ),
			'min_order_amount'    => floatval( $_POST['min_order_amount'] ),
			'max_discount_amount' => floatval( $_POST['max_discount_amount'] ),
			'usage_limit'         => intval( $_POST['usage_limit'] ),
			'user_usage_limit'    => intval( $_POST['user_usage_limit'] ),
			'valid_from'          => empty( $_POST['valid_from'] ) ? null : sanitize_text_field( $_POST['valid_from'] ),
			'valid_until'         => empty( $_POST['valid_until'] ) ? null : sanitize_text_field( $_POST['valid_until'] ),
			'status'              => sanitize_text_field( $_POST['status'] ),
		);

		$wpdb->insert( $table_name, $coupon_data );

		echo '<div class="notice notice-success"><p>' . __( 'Промокод создан', 'atk-ved' ) . '</p></div>';
	}

	// Получение всех промокодов
	$coupons = $wpdb->get_results( "SELECT * FROM {$table_name} ORDER BY created_at DESC" );
	?>
	<div class="wrap">
		<h1><?php _e( 'Промокоды', 'atk-ved' ); ?></h1>
		
		<button type="button" class="page-title-action" onclick="jQuery('#newCouponForm').toggle();">
			<?php _e( 'Добавить промокод', 'atk-ved' ); ?>
		</button>
		
		<!-- Форма создания -->
		<div id="newCouponForm" style="display: none; margin-top: 20px;">
			<form method="post" style="background: #fff; padding: 20px; border-radius: 8px; max-width: 600px;">
				<?php wp_nonce_field( 'atk_create_coupon', 'atk_coupon_nonce' ); ?>
				
				<table class="form-table">
					<tr>
						<th><label><?php _e( 'Код', 'atk-ved' ); ?></label></th>
						<td><input type="text" name="code" required style="width: 100%;"></td>
					</tr>
					<tr>
						<th><label><?php _e( 'Описание', 'atk-ved' ); ?></label></th>
						<td><textarea name="description" rows="3" style="width: 100%;"></textarea></td>
					</tr>
					<tr>
						<th><label><?php _e( 'Тип скидки', 'atk-ved' ); ?></label></th>
						<td>
							<select name="discount_type">
								<option value="percent"><?php _e( 'Процент', 'atk-ved' ); ?></option>
								<option value="fixed"><?php _e( 'Фиксированная', 'atk-ved' ); ?></option>
								<option value="shipping"><?php _e( 'Бесплатная доставка', 'atk-ved' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th><label><?php _e( 'Размер скидки', 'atk-ved' ); ?></label></th>
						<td><input type="number" name="discount_value" step="0.01" required style="width: 100%;"></td>
					</tr>
					<tr>
						<th><label><?php _e( 'Мин. сумма заказа', 'atk-ved' ); ?></label></th>
						<td><input type="number" name="min_order_amount" step="0.01" value="0" style="width: 100%;"></td>
					</tr>
					<tr>
						<th><label><?php _e( 'Макс. скидка', 'atk-ved' ); ?></label></th>
						<td><input type="number" name="max_discount_amount" step="0.01" value="0" style="width: 100%;"></td>
					</tr>
					<tr>
						<th><label><?php _e( 'Лимит использований', 'atk-ved' ); ?></label></th>
						<td><input type="number" name="usage_limit" value="0" style="width: 100%;"><br>
						<small><?php _e( '0 = без лимита', 'atk-ved' ); ?></small></td>
					</tr>
					<tr>
						<th><label><?php _e( 'Лимит на пользователя', 'atk-ved' ); ?></label></th>
						<td><input type="number" name="user_usage_limit" value="1" style="width: 100%;"></td>
					</tr>
					<tr>
						<th><label><?php _e( 'Действует с', 'atk-ved' ); ?></label></th>
						<td><input type="datetime-local" name="valid_from" style="width: 100%;"></td>
					</tr>
					<tr>
						<th><label><?php _e( 'Действует до', 'atk-ved' ); ?></label></th>
						<td><input type="datetime-local" name="valid_until" style="width: 100%;"></td>
					</tr>
					<tr>
						<th><label><?php _e( 'Статус', 'atk-ved' ); ?></label></th>
						<td>
							<select name="status">
								<option value="active"><?php _e( 'Активен', 'atk-ved' ); ?></option>
								<option value="inactive"><?php _e( 'Неактивен', 'atk-ved' ); ?></option>
							</select>
						</td>
					</tr>
				</table>
				
				<p class="submit">
					<input type="submit" name="atk_create_coupon" class="button button-primary" value="<?php _e( 'Создать промокод', 'atk-ved' ); ?>">
				</p>
			</form>
		</div>
		
		<!-- Таблица промокодов -->
		<table class="wp-list-table widefat fixed striped" style="margin-top: 20px;">
			<thead>
				<tr>
					<th><?php _e( 'Код', 'atk-ved' ); ?></th>
					<th><?php _e( 'Скидка', 'atk-ved' ); ?></th>
					<th><?php _e( 'Использовано', 'atk-ved' ); ?></th>
					<th><?php _e( 'Действует', 'atk-ved' ); ?></th>
					<th><?php _e( 'Статус', 'atk-ved' ); ?></th>
					<th><?php _e( 'Создан', 'atk-ved' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $coupons ) ) : ?>
				<tr>
					<td colspan="6"><?php _e( 'Промокоды не созданы', 'atk-ved' ); ?></td>
				</tr>
				<?php else : ?>
					<?php foreach ( $coupons as $coupon ) : ?>
					<tr>
						<td>
							<strong><?php echo esc_html( $coupon->code ); ?></strong><br>
							<small><?php echo esc_html( $coupon->description ); ?></small>
						</td>
						<td>
							<?php
							if ( $coupon->discount_type === 'percent' ) {
								echo $coupon->discount_value . '%';
							} else {
								echo wc_price( $coupon->discount_value );
							}
							?>
						</td>
						<td>
							<?php
							echo $coupon->usage_count;
							if ( $coupon->usage_limit > 0 ) {
								echo ' / ' . $coupon->usage_limit;
							}
							?>
						</td>
						<td>
							<?php
							if ( $coupon->valid_from ) {
								echo date_i18n( get_option( 'date_format' ), strtotime( $coupon->valid_from ) );
							} else {
								_e( 'Бессрочно', 'atk-ved' );
							}

							if ( $coupon->valid_until ) {
								echo ' - ' . date_i18n( get_option( 'date_format' ), strtotime( $coupon->valid_until ) );
							}
							?>
						</td>
						<td>
							<span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: bold; 
								<?php echo $coupon->status === 'active' ? 'background: #4CAF50; color: #fff;' : 'background: #999; color: #fff;'; ?>">
								<?php echo $coupon->status === 'active' ? __( 'Активен', 'atk-ved' ) : __( 'Неактивен', 'atk-ved' ); ?>
							</span>
						</td>
						<td><?php echo date_i18n( get_option( 'date_format' ), strtotime( $coupon->created_at ) ); ?></td>
					</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	<?php
}
