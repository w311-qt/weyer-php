<?php
/**
 * Weyer Theme Functions
 * Основные функции темы для сайта промышленного оборудования
 */

// Предотвращение прямого доступа
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Настройки темы
 */
function weyer_theme_setup() {
    // Поддержка заголовка сайта
    add_theme_support('title-tag');

    // Поддержка миниатюр записей
    add_theme_support('post-thumbnails');

    // Поддержка HTML5 разметки
    add_theme_support('html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
    ));

    // Поддержка настройщика WordPress
    add_theme_support('customize-selective-refresh-widgets');

    // Регистрация меню навигации
    register_nav_menus(array(
            'primary' => 'Основное меню',
            'footer' => 'Меню футера',
            'catalog' => 'Меню каталога',
    ));

    // Размеры изображений
    add_image_size('product-thumb', 300, 300, true);
    add_image_size('product-large', 800, 600, true);
    add_image_size('category-thumb', 400, 300, true);
    add_image_size('hero-bg', 1920, 1080, true);
}
add_action('after_setup_theme', 'weyer_theme_setup');

/**
 * Подключение стилей и скриптов
 */
function weyer_enqueue_scripts() {
    $theme_version = wp_get_theme()->get('Version');

    // Основные стили
    wp_enqueue_style('weyer-style', get_stylesheet_uri(), array(), $theme_version);

    // Google Fonts
    wp_enqueue_style('weyer-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap', array(), null);

    // Font Awesome
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css', array(), '6.5.0');

    // Основной JavaScript
    wp_enqueue_script('weyer-main', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), $theme_version, true);

    // Каталог скрипты (только на страницах каталога)
    if (is_post_type_archive('product') || is_tax('product_category') || is_singular('product')) {
        wp_enqueue_script('weyer-catalog', get_template_directory_uri() . '/assets/js/catalog.js', array('jquery'), $theme_version, true);

        // AJAX для каталога
        wp_localize_script('weyer-catalog', 'weyer_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('weyer_catalog_nonce')
        ));
    }

    // Слайдер на главной странице
    if (is_front_page()) {
        wp_enqueue_script('weyer-hero', get_template_directory_uri() . '/assets/js/hero.js', array('jquery'), $theme_version, true);
    }
}
add_action('wp_enqueue_scripts', 'weyer_enqueue_scripts');

/**
 * Регистрация Custom Post Type для продукции
 */
function weyer_register_product_post_type() {
    $labels = array(
            'name' => 'Продукция',
            'singular_name' => 'Товар',
            'menu_name' => 'Каталог',
            'name_admin_bar' => 'Товар',
            'archives' => 'Архив товаров',
            'attributes' => 'Характеристики товара',
            'parent_item_colon' => 'Родительский товар:',
            'all_items' => 'Все товары',
            'add_new_item' => 'Добавить новый товар',
            'add_new' => 'Добавить новый',
            'new_item' => 'Новый товар',
            'edit_item' => 'Редактировать товар',
            'update_item' => 'Обновить товар',
            'view_item' => 'Просмотреть товар',
            'view_items' => 'Просмотреть товары',
            'search_items' => 'Найти товары',
    );

    $args = array(
            'label' => 'Товар',
            'description' => 'Каталог промышленного оборудования',
            'labels' => $labels,
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'taxonomies' => array('product_category', 'product_tag'),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-products',
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => 'catalog',
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'post',
            'show_in_rest' => true,
            'rewrite' => array(
                    'slug' => 'product',
                    'with_front' => false
            ),
    );

    register_post_type('product', $args);
}
add_action('init', 'weyer_register_product_post_type', 0);

/**
 * Регистрация таксономий для продукции
 */
function weyer_register_product_taxonomies() {
    // Категории товаров
    register_taxonomy('product_category', 'product', array(
            'hierarchical' => true,
            'labels' => array(
                    'name' => 'Категории товаров',
                    'singular_name' => 'Категория товара',
                    'menu_name' => 'Категории',
            ),
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'category'),
    ));

    // Теги товаров
    register_taxonomy('product_tag', 'product', array(
            'hierarchical' => false,
            'labels' => array(
                    'name' => 'Теги товаров',
                    'singular_name' => 'Тег товара',
                    'menu_name' => 'Теги',
            ),
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'tag'),
    ));

    // Бренды
    register_taxonomy('product_brand', 'product', array(
            'hierarchical' => true,
            'labels' => array(
                    'name' => 'Бренды',
                    'singular_name' => 'Бренд',
                    'menu_name' => 'Бренды',
            ),
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'brand'),
    ));
}
add_action('init', 'weyer_register_product_taxonomies', 0);

/**
 * Добавление мета-полей для товаров
 */
function weyer_add_product_meta_boxes() {
    add_meta_box(
            'product-details',
            'Характеристики товара',
            'weyer_product_details_callback',
            'product',
            'normal',
            'high'
    );
}
add_action('add_meta_boxes', 'weyer_add_product_meta_boxes');

/**
 * Callback для мета-бокса характеристик товара
 */
function weyer_product_details_callback($post) {
    wp_nonce_field('weyer_product_meta_nonce', 'product_meta_nonce');

    $sku = get_post_meta($post->ID, '_product_sku', true);
    $price = get_post_meta($post->ID, '_product_price', true);
    $old_price = get_post_meta($post->ID, '_product_old_price', true);
    $material = get_post_meta($post->ID, '_product_material', true);
    $diameter = get_post_meta($post->ID, '_product_diameter', true);
    $temperature = get_post_meta($post->ID, '_product_temperature', true);
    $protection = get_post_meta($post->ID, '_product_protection', true);
    $in_stock = get_post_meta($post->ID, '_product_in_stock', true);
    $is_hit = get_post_meta($post->ID, '_product_is_hit', true);
    ?>

    <table class="form-table">
        <tr>
            <th><label for="product_sku">Артикул (SKU)</label></th>
            <td><input type="text" id="product_sku" name="product_sku" value="<?php echo esc_attr($sku); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="product_price">Цена (₽)</label></th>
            <td><input type="number" id="product_price" name="product_price" value="<?php echo esc_attr($price); ?>" step="0.01" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="product_old_price">Старая цена (₽)</label></th>
            <td><input type="number" id="product_old_price" name="product_old_price" value="<?php echo esc_attr($old_price); ?>" step="0.01" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="product_material">Материал</label></th>
            <td><input type="text" id="product_material" name="product_material" value="<?php echo esc_attr($material); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="product_diameter">Диаметр</label></th>
            <td><input type="text" id="product_diameter" name="product_diameter" value="<?php echo esc_attr($diameter); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="product_temperature">Температурный диапазон</label></th>
            <td><input type="text" id="product_temperature" name="product_temperature" value="<?php echo esc_attr($temperature); ?>" placeholder="-25°C +80°C" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="product_protection">Степень защиты</label></th>
            <td><input type="text" id="product_protection" name="product_protection" value="<?php echo esc_attr($protection); ?>" placeholder="IP68" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="product_in_stock">Наличие</label></th>
            <td>
                <label>
                    <input type="checkbox" id="product_in_stock" name="product_in_stock" value="1" <?php checked($in_stock, 1); ?> />
                    Товар в наличии
                </label>
            </td>
        </tr>
        <tr>
            <th><label for="product_is_hit">Хит продаж</label></th>
            <td>
                <label>
                    <input type="checkbox" id="product_is_hit" name="product_is_hit" value="1" <?php checked($is_hit, 1); ?> />
                    Отметить как хит продаж
                </label>
            </td>
        </tr>
    </table>

    <style>
        .form-table th { width: 200px; }
        .form-table input[type="text"],
        .form-table input[type="number"] { width: 100%; max-width: 300px; }
    </style>
    <?php
}

/**
 * Сохранение мета-данных товара
 */
function weyer_save_product_meta($post_id) {
    if (!isset($_POST['product_meta_nonce']) || !wp_verify_nonce($_POST['product_meta_nonce'], 'weyer_product_meta_nonce')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $fields = array(
            'product_sku' => '_product_sku',
            'product_price' => '_product_price',
            'product_old_price' => '_product_old_price',
            'product_material' => '_product_material',
            'product_diameter' => '_product_diameter',
            'product_temperature' => '_product_temperature',
            'product_protection' => '_product_protection',
    );

    foreach ($fields as $form_field => $meta_key) {
        if (isset($_POST[$form_field])) {
            update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$form_field]));
        }
    }

    // Чекбоксы для наличия и хита
    $in_stock = isset($_POST['product_in_stock']) ? 1 : 0;
    update_post_meta($post_id, '_product_in_stock', $in_stock);

    $is_hit = isset($_POST['product_is_hit']) ? 1 : 0;
    update_post_meta($post_id, '_product_is_hit', $is_hit);
}
add_action('save_post', 'weyer_save_product_meta');

/**
 * Настройка области виджетов
 */
function weyer_widgets_init() {
    register_sidebar(array(
            'name' => 'Боковая панель каталога',
            'id' => 'catalog-sidebar',
            'description' => 'Виджеты для страницы каталога',
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
    ));

    register_sidebar(array(
            'name' => 'Футер',
            'id' => 'footer-widgets',
            'description' => 'Виджеты в футере сайта',
            'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h4 class="footer-widget-title">',
            'after_title' => '</h4>',
    ));
}
add_action('widgets_init', 'weyer_widgets_init');

/**
 * Добавление колонок в админку товаров
 */
function weyer_product_columns($columns) {
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'title') {
            $new_columns['product_image'] = 'Изображение';
            $new_columns['product_sku'] = 'Артикул';
            $new_columns['product_price'] = 'Цена';
            $new_columns['product_stock'] = 'Наличие';
        }
    }
    return $new_columns;
}
add_filter('manage_product_posts_columns', 'weyer_product_columns');

/**
 * Заполнение кастомных колонок
 */
function weyer_product_column_content($column, $post_id) {
    switch ($column) {
        case 'product_image':
            if (has_post_thumbnail($post_id)) {
                echo get_the_post_thumbnail($post_id, array(50, 50));
            } else {
                echo '—';
            }
            break;

        case 'product_sku':
            $sku = get_post_meta($post_id, '_product_sku', true);
            echo $sku ? esc_html($sku) : '—';
            break;

        case 'product_price':
            $price = get_post_meta($post_id, '_product_price', true);
            echo $price ? number_format($price, 0, '.', ' ') . ' ₽' : '—';
            break;

        case 'product_stock':
            $in_stock = get_post_meta($post_id, '_product_in_stock', true);
            if ($in_stock) {
                echo '<span style="color: green;">✓ В наличии</span>';
            } else {
                echo '<span style="color: red;">✗ Нет в наличии</span>';
            }
            break;
    }
}
add_action('manage_product_posts_custom_column', 'weyer_product_column_content', 10, 2);

/**
 * Хелперы для получения данных товара
 */
function get_product_price($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, '_product_price', true);
}

function get_product_old_price($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, '_product_old_price', true);
}

function get_product_sku($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, '_product_sku', true);
}

function is_product_in_stock($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return (bool) get_post_meta($post_id, '_product_in_stock', true);
}

function is_product_hit($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return (bool) get_post_meta($post_id, '_product_is_hit', true);
}

/**
 * Настройка переписывания URL для каталога
 */
function weyer_rewrite_rules() {
    add_rewrite_rule('^catalog/?$', 'index.php?post_type=product', 'top');
    add_rewrite_rule('^catalog/page/([0-9]+)/?$', 'index.php?post_type=product&paged=$matches[1]', 'top');
}
add_action('init', 'weyer_rewrite_rules');

/**
 * Кастомизация админки
 */
function weyer_admin_styles() {
    echo '<style>
        .post-type-product .form-table th { width: 200px; }
        .post-type-product .form-table input[type="text"],
        .post-type-product .form-table input[type="number"] { width: 100%; max-width: 300px; }
        .weyer-import-section { 
            background: #f1f1f1; 
            padding: 20px; 
            margin: 20px 0; 
            border-radius: 5px; 
        }
    </style>';
}
add_action('admin_head', 'weyer_admin_styles');

/**
 * Функция для получения уникальных значений мета-поля
 */
function get_unique_product_meta($meta_key) {
    global $wpdb;

    $values = $wpdb->get_col($wpdb->prepare("
        SELECT DISTINCT meta_value 
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
        WHERE pm.meta_key = %s 
        AND pm.meta_value != ''
        AND p.post_status = 'publish'
        AND p.post_type = 'product'
        ORDER BY meta_value
    ", $meta_key));

    return array_filter($values);
}

/**
 * Функция для получения диапазона цен
 */
function get_products_price_range() {
    global $wpdb;

    $results = $wpdb->get_row("
        SELECT 
            MIN(CAST(meta_value AS DECIMAL(10,2))) as min_price,
            MAX(CAST(meta_value AS DECIMAL(10,2))) as max_price
        FROM {$wpdb->postmeta} 
        WHERE meta_key = '_product_price' 
        AND meta_value > 0
    ");

    return array(
            'min' => $results->min_price ?? 0,
            'max' => $results->max_price ?? 10000
    );
}

/**
 * Хлебные крошки
 */
function weyer_breadcrumbs() {
    echo '<a href="' . home_url() . '"><i class="fas fa-home"></i></a>';
    echo ' > ';

    if (is_post_type_archive('product')) {
        echo '<span>Каталог</span>';
    } elseif (is_tax()) {
        echo '<a href="' . get_post_type_archive_link('product') . '">Каталог</a>';
        echo ' > ';
        echo '<span>' . single_term_title('', false) . '</span>';
    } elseif (is_singular('product')) {
        echo '<a href="' . get_post_type_archive_link('product') . '">Каталог</a>';
        echo ' > ';

        $terms = get_the_terms(get_the_ID(), 'product_category');
        if ($terms && !is_wp_error($terms)) {
            $term = array_shift($terms);
            echo '<a href="' . get_term_link($term) . '">' . $term->name . '</a>';
            echo ' > ';
        }

        echo '<span>' . get_the_title() . '</span>';
    }
}

/**
 * Активация темы - создание страниц и настроек
 */
function weyer_theme_activation() {
    // Создаем базовые страницы если их нет
    $pages = array(
            'О компании' => 'about',
            'Контакты' => 'contacts',
            'Услуги' => 'services'
    );

    foreach ($pages as $title => $slug) {
        if (!get_page_by_path($slug)) {
            wp_insert_post(array(
                    'post_title' => $title,
                    'post_name' => $slug,
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_content' => 'Содержимое страницы ' . $title
            ));
        }
    }

    // Настройки сайта
    update_option('blogdescription', '15 000+ позиций промышленного оборудования от ведущих производителей');

    // Обновляем правила перезаписи
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'weyer_theme_activation');

/**
 * Деактивация темы
 */
function weyer_theme_deactivation() {
    flush_rewrite_rules();
}
add_action('switch_theme', 'weyer_theme_deactivation');
?>