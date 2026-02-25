<?php
/**
 * Файлы отзывов - кастомный тип записи
 */

declare(strict_types=1);

// Регистрация кастомного типа
function atk_ved_register_testimonial_files(): void {
    register_post_type('testimonial_file', array(
        'labels' => array(
            'name' => 'Файлы отзывов',
            'singular_name' => 'Файл отзыва',
            'add_new' => 'Добавить файл',
            'add_new_item' => 'Добавить новый файл',
            'edit_item' => 'Редактировать файл',
            'all_items' => 'Все файлы',
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-media-document',
        'supports' => array('title', 'thumbnail'),
        'has_archive' => false,
    ));
}
add_action('init', 'atk_ved_register_testimonial_files');

// Мета-боксы
function atk_ved_testimonial_file_meta_boxes(): void {
    add_meta_box(
        'testimonial_file_details',
        'Детали файла',
        'atk_ved_testimonial_file_meta_box_callback',
        'testimonial_file',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'atk_ved_testimonial_file_meta_boxes');

function atk_ved_testimonial_file_meta_box_callback($post): void {
    wp_nonce_field('atk_ved_testimonial_file_nonce', 'atk_ved_testimonial_file_nonce');
    
    $file_url = get_post_meta($post->ID, '_file_url', true);
    $company = get_post_meta($post->ID, '_company_name', true);
    $file_type = get_post_meta($post->ID, '_file_type', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="file_url">Файл</label></th>
            <td>
                <input type="text" id="file_url" name="file_url" value="<?php echo esc_attr($file_url); ?>" class="regular-text" readonly>
                <button type="button" class="button upload-file-button">Загрузить файл</button>
                <p class="description">PDF, DOC, DOCX, JPG, PNG (макс. 10MB)</p>
            </td>
        </tr>
        <tr>
            <th><label for="company_name">Название компании</label></th>
            <td>
                <input type="text" id="company_name" name="company_name" value="<?php echo esc_attr($company); ?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th><label for="file_type">Тип файла</label></th>
            <td>
                <select id="file_type" name="file_type">
                    <option value="pdf" <?php selected($file_type, 'pdf'); ?>>PDF</option>
                    <option value="image" <?php selected($file_type, 'image'); ?>>Изображение</option>
                    <option value="doc" <?php selected($file_type, 'doc'); ?>>Документ</option>
                </select>
            </td>
        </tr>
    </table>
    
    <script>
    jQuery(document).ready(function($) {
        $('.upload-file-button').click(function(e) {
            e.preventDefault();
            var button = $(this);
            var custom_uploader = wp.media({
                title: 'Выберите файл',
                button: { text: 'Использовать этот файл' },
                multiple: false
            }).on('select', function() {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                $('#file_url').val(attachment.url);
            }).open();
        });
    });
    </script>
    <?php
}

// Сохранение мета-данных
function atk_ved_save_testimonial_file_meta($post_id): void {
    if (!isset($_POST['atk_ved_testimonial_file_nonce']) || 
        !wp_verify_nonce($_POST['atk_ved_testimonial_file_nonce'], 'atk_ved_testimonial_file_nonce')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    
    if (isset($_POST['file_url'])) {
        update_post_meta($post_id, '_file_url', sanitize_text_field($_POST['file_url']));
    }
    if (isset($_POST['company_name'])) {
        update_post_meta($post_id, '_company_name', sanitize_text_field($_POST['company_name']));
    }
    if (isset($_POST['file_type'])) {
        update_post_meta($post_id, '_file_type', sanitize_text_field($_POST['file_type']));
    }
}
add_action('save_post_testimonial_file', 'atk_ved_save_testimonial_file_meta');

// Вывод файлов на фронтенде
function atk_ved_get_testimonial_files(): array {
    $args = array(
        'post_type' => 'testimonial_file',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC'
    );
    
    $query = new WP_Query($args);
    $files = array();
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $files[] = array(
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'file_url' => get_post_meta(get_the_ID(), '_file_url', true),
                'company' => get_post_meta(get_the_ID(), '_company_name', true),
                'file_type' => get_post_meta(get_the_ID(), '_file_type', true),
                'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'medium'),
                'date' => get_the_date('d.m.Y')
            );
        }
    }
    
    wp_reset_postdata();
    return $files;
}
