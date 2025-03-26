/**
 * Основные скрипты для работы таблицы температур
 * 
 * @file scripts.js
 * @requires jQuery
 */

(function($) {
    /**
     * Обработчик поиска городов через AJAX
     * @listens input
     * @selector #city-search
     */
    $('#city-search').on('input', function() {
        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'city_search',
                search: $(this).val()
            },
            success: function(response) {
                // Обработка результатов
            }
        });
    });

    /**
     * Прокрутка к найденному городу
     * @listens click
     * @selector .search-result-item
     */
    $(document).on('click', '.search-result-item', function() {
        var cityId = $(this).data('city-id');
        $('html, body').animate({
            scrollTop: $('#city-'+cityId).offset().top - 100
        }, 500);
    });
})(jQuery);