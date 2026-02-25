/**
 * Улучшения админки WordPress
 */

jQuery(document).ready(function($) {
    
    // Медиа загрузчик для кастомных полей
    if (typeof wp !== 'undefined' && wp.media) {
        
        // Загрузка файлов для отзывов
        $(document).on('click', '.upload-file-button', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var inputField = button.prev('input');
            
            var mediaUploader = wp.media({
                title: 'Выберите файл',
                button: {
                    text: 'Использовать этот файл'
                },
                multiple: false,
                library: {
                    type: ['application/pdf', 'image', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
                }
            });
            
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                inputField.val(attachment.url);
                
                // Показываем превью для изображений
                if (attachment.type === 'image') {
                    var preview = '<div class="file-preview-admin"><img src="' + attachment.url + '" style="max-width: 200px; margin-top: 10px; border-radius: 8px;"></div>';
                    button.parent().find('.file-preview-admin').remove();
                    button.parent().append(preview);
                }
            });
            
            mediaUploader.open();
        });
        
        // Drag & Drop для загрузки
        var $dropZone = $('.testimonial-file-upload-zone');
        
        if ($dropZone.length) {
            $dropZone.on('dragover', function(e) {
                e.preventDefault();
                $(this).addClass('drag-over');
            });
            
            $dropZone.on('dragleave', function(e) {
                e.preventDefault();
                $(this).removeClass('drag-over');
            });
            
            $dropZone.on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('drag-over');
                
                var files = e.originalEvent.dataTransfer.files;
                if (files.length > 0) {
                    // Обработка загрузки файлов
                    handleFileUpload(files[0]);
                }
            });
        }
    }
    
    // Функция загрузки файла
    function handleFileUpload(file) {
        var formData = new FormData();
        formData.append('file', file);
        formData.append('action', 'upload_testimonial_file');
        formData.append('nonce', atkAdminData.nonce);
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#file_url').val(response.data.url);
                    if (typeof atkShowToast === 'function') {
                        atkShowToast('Файл успешно загружен', 'success');
                    }
                }
            }
        });
    }
    
    // Улучшенный интерфейс списка файлов
    if ($('.post-type-testimonial_file').length) {
        // Добавляем превью в список
        $('.column-thumbnail').each(function() {
            var $img = $(this).find('img');
            if ($img.length) {
                $img.css({
                    'border-radius': '8px',
                    'box-shadow': '0 2px 8px rgba(0,0,0,0.1)'
                });
            }
        });
    }
    
    // Быстрое редактирование
    $('.quick-edit-row').on('click', '.save', function() {
        var $row = $(this).closest('.quick-edit-row');
        var postId = $row.find('input[name="post_ID"]').val();
        
        // Сохранение дополнительных полей
        var companyName = $row.find('input[name="company_name"]').val();
        
        if (companyName) {
            $.post(ajaxurl, {
                action: 'save_quick_edit_testimonial',
                post_id: postId,
                company_name: companyName,
                nonce: atkAdminData.nonce
            });
        }
    });
    
});
