<?php
/**
 * Template Name: Temperature Table
 * 
 * Шаблон для отображения таблицы с температурой в городах
 * 
 * @package Storefront Child
 * @uses $wpdb для прямых SQL-запросов
 */

get_header(); ?>

<div class="temperature-table-container">
    <?php 
    /**
     * Хук для вывода контента перед таблицей
     * @hook before_weather_table
     */
    do_action('before_weather_table'); 
    ?>
    
    <!-- Основная таблица -->
    <table id="weather-table">
        <?php
        global $wpdb;
        $results = $wpdb->get_results("
            SELECT p.ID, p.post_title, t.name as country 
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
            LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            LEFT JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
            WHERE p.post_type = 'city' 
            AND p.post_status = 'publish'
            AND tt.taxonomy = 'country'
        ");
        
        foreach ($results as $row):
            $weather = get_transient('weather_'.$row->ID);
        ?>
        <tr>
            <td><?php echo esc_html($row->country); ?></td>
            <td><?php echo esc_html($row->post_title); ?></td>
            <td><?php echo $weather['main']['temp'] ?? 'N/A'; ?>°C</td>
        </tr>
        <?php endforeach; ?>
    </table>

    <?php 
    /**
     * Хук для вывода контента после таблицы
     * @hook after_weather_table
     */
    do_action('after_weather_table'); 
    ?>
</div>

<?php get_footer(); ?>