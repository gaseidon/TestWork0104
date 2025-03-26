<?php
/**
 * Виджет для отображения текущей погоды в выбранном городе
 * 
 * Использует OpenWeatherMap API для получения данных о погоде
 * 
 * @package Storefront_Child
 * @class Weather_Widget
 * @extends WP_Widget
 * @since 1.0
 */
class Weather_Widget extends WP_Widget {
    
    /**
     * Конструктор класса - регистрирует виджет
     * 
     * @since 1.0
     */
    public function __construct() {
        parent::__construct(
            'weather_widget', // Базовый ID виджета
            __('Weather Widget', 'storefront-child'), // Название виджета
            array( // Опции виджета
                'description' => __('Displays weather for selected city', 'storefront-child')
            )
        );
    }

    /**
     * Форма настройки виджета в админке
     * 
     * @param array $instance Текущие настройки виджета
     * @since 1.0
     */
    public function form($instance) {
        $selected_city = $instance['city'] ?? '';
        $api_key = $instance['api_key'] ?? '';
        
        // Получаем список всех опубликованных городов
        $cities_query = new WP_Query(array(
            'post_type' => 'city',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('api_key'); ?>">
                <?php _e('OpenWeatherMap API Key:', 'storefront-child'); ?>
            </label>
            <input class="widefat" 
                   id="<?php echo $this->get_field_id('api_key'); ?>"
                   name="<?php echo $this->get_field_name('api_key'); ?>" 
                   type="text" 
                   value="<?php echo esc_attr($api_key); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('city'); ?>">
                <?php _e('Select City:', 'storefront-child'); ?>
            </label>
            <select class="widefat" 
                    id="<?php echo $this->get_field_id('city'); ?>"
                    name="<?php echo $this->get_field_name('city'); ?>">
                <option value="">— <?php _e('Select', 'storefront-child'); ?> —</option>
                <?php 
                if ($cities_query->have_posts()) :
                    while ($cities_query->have_posts()) : $cities_query->the_post();
                        $city_id = get_the_ID();
                        ?>
                        <option value="<?php echo $city_id; ?>" 
                            <?php selected($selected_city, $city_id); ?>>
                            <?php the_title(); ?>
                        </option>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    echo '<option value="">' . __('No cities found', 'storefront-child') . '</option>';
                endif;
                ?>
            </select>
        </p>
        <?php
    }

    /**
     * Сохранение настроек виджета
     * 
     * @param array $new_instance Новые настройки
     * @param array $old_instance Старые настройки
     * @return array Очищенные настройки для сохранения
     * @since 1.0
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        // Санитизация ввода
        $instance['city'] = sanitize_text_field($new_instance['city']);
        $instance['api_key'] = sanitize_text_field($new_instance['api_key']);
        return $instance;
    }

    /**
     * Отображение виджета на фронтенде
     * 
     * @param array $args Аргументы отображения виджета
     * @param array $instance Настройки виджета
     * @since 1.0
     */
    public function widget($args, $instance) {
        $city_id = $instance['city'];
        $api_key = $instance['api_key'];
        
        // Получаем координаты города
        $latitude = get_post_meta($city_id, '_latitude', true);
        $longitude = get_post_meta($city_id, '_longitude', true);
        
        // Проверяем кэш погодных данных (хранится 1 час)
        $transient_key = 'weather_'.$city_id;
        $weather_data = get_transient($transient_key);
        
        // Если данных нет в кэше, делаем API запрос
        if (!$weather_data && $latitude && $longitude && $api_key) {
            $response = wp_remote_get(
                "https://api.openweathermap.org/data/2.5/weather?" . http_build_query([
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'appid' => $api_key,
                    'units' => 'metric'
                ])
            );
            
            if (!is_wp_error($response)) {
                $weather_data = json_decode(wp_remote_retrieve_body($response), true);
                set_transient($transient_key, $weather_data, HOUR_IN_SECONDS);
            }
        }
        
        // Вывод виджета
        echo $args['before_widget'];
        echo '<div class="weather-widget">';
        echo '<h3>' . get_the_title($city_id) . '</h3>';
        
        if ($weather_data) {
            echo '<p>' . __('Temperature:', 'storefront-child') . ' ' 
                 . $weather_data['main']['temp'] . '°C</p>';
            echo '<p>' . __('Humidity:', 'storefront-child') . ' ' 
                 . $weather_data['main']['humidity'] . '%</p>';
        } else {
            echo '<p>' . __('Weather data unavailable', 'storefront-child') . '</p>';
        }
        
        echo '</div>';
        echo $args['after_widget'];
    }
}

/**
 * Регистрирует виджет погоды в системе
 * 
 * @hook widgets_init
 * @since 1.0
 */
function register_weather_widget() {
    register_widget('Weather_Widget');
}
add_action('widgets_init', 'register_weather_widget');