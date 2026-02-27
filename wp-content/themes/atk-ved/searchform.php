<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <label class="search-label">
        <span class="screen-reader-text"><?php _e('Поиск:', 'atk-ved'); ?></span>
        <input type="search" 
               class="search-field" 
               placeholder="<?php esc_attr_e('Введите запрос...', 'atk-ved'); ?>" 
               value="<?php echo esc_attr(get_search_query()); ?>" 
               name="s" 
               required>
    </label>
    <button type="submit" class="search-submit" aria-label="<?php esc_attr_e('Найти', 'atk-ved'); ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/>
            <path d="m21 21-4.35-4.35"/>
        </svg>
        <span class="search-text"><?php _e('Найти', 'atk-ved'); ?></span>
    </button>
</form>
