<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <label>
        <span class="screen-reader-text">Поиск:</span>
        <input type="search" 
               class="search-field" 
               placeholder="Введите запрос..." 
               value="<?php echo get_search_query(); ?>" 
               name="s" 
               required>
    </label>
    <button type="submit" class="search-submit">
        <span class="search-icon">🔍</span>
        <span class="search-text">Найти</span>
    </button>
</form>
