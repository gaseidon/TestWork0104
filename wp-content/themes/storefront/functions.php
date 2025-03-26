<?php
/**
 * Storefront engine room
 *
 * @package storefront
 */

/**
 * Assign the Storefront version to a var
 */
$theme              = wp_get_theme( 'storefront' );
$storefront_version = $theme['Version'];

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 980; /* pixels */
}

$storefront = (object) array(
	'version'    => $storefront_version,

	/**
	 * Initialize all the things.
	 */
	'main'       => require 'inc/class-storefront.php',
	'customizer' => require 'inc/customizer/class-storefront-customizer.php',
);

require 'inc/storefront-functions.php';
require 'inc/storefront-template-hooks.php';
require 'inc/storefront-template-functions.php';
require 'inc/wordpress-shims.php';

if ( class_exists( 'Jetpack' ) ) {
	$storefront->jetpack = require 'inc/jetpack/class-storefront-jetpack.php';
}

if ( storefront_is_woocommerce_activated() ) {
	$storefront->woocommerce            = require 'inc/woocommerce/class-storefront-woocommerce.php';
	$storefront->woocommerce_customizer = require 'inc/woocommerce/class-storefront-woocommerce-customizer.php';

	require 'inc/woocommerce/class-storefront-woocommerce-adjacent-products.php';

	require 'inc/woocommerce/storefront-woocommerce-template-hooks.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions.php';
	require 'inc/woocommerce/storefront-woocommerce-functions.php';
}

if ( is_admin() ) {
	$storefront->admin = require 'inc/admin/class-storefront-admin.php';

	require 'inc/admin/class-storefront-plugin-install.php';
}

/**
 * NUX
 * Only load if wp version is 4.7.3 or above because of this issue;
 * https://core.trac.wordpress.org/ticket/39610?cversion=1&cnum_hist=2
 */
if ( version_compare( get_bloginfo( 'version' ), '4.7.3', '>=' ) && ( is_admin() || is_customize_preview() ) ) {
	require 'inc/nux/class-storefront-nux-admin.php';
	require 'inc/nux/class-storefront-nux-guided-tour.php';
	require 'inc/nux/class-storefront-nux-starter-content.php';
}

/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woocommerce/theme-customisations
 */
/**
 * Подключает стили родительской темы
 * 
 * @hook wp_enqueue_scripts
 * @since 1.0
 */
add_action('wp_enqueue_scripts', 'enqueue_parent_theme_style');
function enqueue_parent_theme_style() {
    wp_enqueue_style('parent-style', get_template_directory_uri().'/style.css');
}
/**
 * Регистрирует Custom Post Type 'Cities' для хранения информации о городах
 * 
 * @hook init
 * @since 1.0
 */
// 1. Создание CPT "Cities"
function create_cities_cpt() {
    $args = array(
        'labels' => array(
            'name' => __('Cities'),
            'singular_name' => __('City')
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail'),
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-location'
    );
    register_post_type('city', $args);
}
add_action('init', 'create_cities_cpt');
/**
 * Добавляет метабокс для координат города
 * 
 * @hook add_meta_boxes
 * @since 1.0
 */
// 2. Метабокс с координатами
function add_city_meta_boxes() {
    add_meta_box(
        'city_coordinates',
        'Coordinates',
        'render_city_coordinates_meta_box',
        'city',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'add_city_meta_boxes');
/**
 * Отображает содержимое метабокса с координатами
 * 
 * @param WP_Post $post Объект текущего поста
 * @since 1.0
 */
function render_city_coordinates_meta_box($post) {
    wp_nonce_field('city_coordinates_nonce', 'city_coordinates_nonce');
    
    $latitude = get_post_meta($post->ID, '_latitude', true);
    $longitude = get_post_meta($post->ID, '_longitude', true);
    ?>
    <p>
        <label>Latitude:</label>
        <input type="text" name="latitude" value="<?php echo esc_attr($latitude); ?>">
    </p>
    <p>
        <label>Longitude:</label>
        <input type="text" name="longitude" value="<?php echo esc_attr($longitude); ?>">
    </p>
    <?php
}
/**
 * Сохраняет данные координат города
 * 
 * @param int $post_id ID сохраняемого поста
 * @hook save_post
 * @since 1.0
 */
function save_city_coordinates($post_id) {
    if (!isset($_POST['city_coordinates_nonce']) || 
        !wp_verify_nonce($_POST['city_coordinates_nonce'], 'city_coordinates_nonce')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    
    update_post_meta($post_id, '_latitude', sanitize_text_field($_POST['latitude']));
    update_post_meta($post_id, '_longitude', sanitize_text_field($_POST['longitude']));
}
add_action('save_post', 'save_city_coordinates');
/**
 * Регистрирует таксономию 'Countries' для классификации городов
 * 
 * @hook init
 * @since 1.0
 */
// 3. Таксономия "Countries"
function create_countries_taxonomy() {
    $args = array(
        'labels' => array(
            'name' => __('Countries'),
            'singular_name' => __('Country')
        ),
        'hierarchical' => true,
        'show_admin_column' => true
    );
    register_taxonomy('country', 'city', $args);
}
add_action('init', 'create_countries_taxonomy');
/**
 * Подключает файл с виджетом погоды
 * 
 * @since 1.0
 */
// 4. Виджет погоды
require_once('widgets/weather-widget.php');
/**
 * Регистрирует кастомный шаблон страницы 'Temperature Table'
 * 
 * @param array $templates Массив доступных шаблонов
 * @return array Модифицированный массив шаблонов
 * @hook theme_page_templates
 * @since 1.0
 */
// 5. Кастомный шаблон страницы
function register_custom_templates($templates) {
    $templates['page-temperature-table.php'] = 'Temperature Table';
    return $templates;
}
add_filter('theme_page_templates', 'register_custom_templates');
/**
 * Обрабатывает AJAX-запросы для поиска городов
 * 
 * @hook wp_ajax_city_search
 * @hook wp_ajax_nopriv_city_search
 * @since 1.0
 */
// 6. AJAX обработчики
add_action('wp_ajax_city_search', 'handle_city_search');
add_action('wp_ajax_nopriv_city_search', 'handle_city_search');
/**
 * Колбэк-функция для обработки поиска городов
 * 
 * @global wpdb $wpdb Объект базы данных WordPress
 * @since 1.0
 */
function handle_city_search() {
    global $wpdb;
    
    $search_term = sanitize_text_field($_POST['search']);
    
    $results = $wpdb->get_results($wpdb->prepare("
        SELECT * FROM {$wpdb->posts} 
        WHERE post_type = 'city' 
        AND post_status = 'publish' 
        AND post_title LIKE %s
    ", '%'.$wpdb->esc_like($search_term).'%'));
    
    wp_send_json($results);
}
/**
 * Подключает кастомные скрипты и локализует AJAX-параметры
 * 
 * @hook wp_enqueue_scripts
 * @since 1.0
 */
// 7. Подключение скриптов
function enqueue_custom_scripts() {
    wp_enqueue_script(
        'custom-scripts',
        get_stylesheet_directory_uri().'/js/scripts.js',
        array('jquery'),
        '1.0',
        true
    );
    
    wp_localize_script('custom-scripts', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');
/**
 * Принудительно отображает метабокс таксономии Countries
 * 
 * @hook admin_menu
 * @since 1.0
 */
add_action('admin_menu', 'force_show_country_metabox');
function force_show_country_metabox() {
    add_meta_box(
        'countrydiv', // ID
        'Countries', // Заголовок
        'post_categories_meta_box', // Колбэк
        'city', // CPT
        'side', // Расположение
        'default', // Приоритет
        array('taxonomy' => 'country') // Аргументы
    );
}