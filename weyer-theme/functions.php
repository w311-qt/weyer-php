<?php
/**
 * Weyer Theme Functions - Полная версия
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
            'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
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
    $theme_version = wp_get_theme()->get('Version') ?: '1.0.0';

    // Основные стили
    wp_enqueue_style('weyer-style', get_stylesheet_uri(), array(), $theme_version);

    // Google Fonts
    wp_enqueue_style('weyer-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap', array(), null);

    // Font Awesome
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css', array(), '6.5.0');

    // Основной JavaScript
    wp_enqueue_script('weyer-main', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), $theme_version, true);

    // Каталог скрипты
    if (is_post_type_archive('product') || is_tax('product_category') || is_singular('product')) {
        wp_enqueue_script('weyer-catalog', get_template_directory_uri() . '/assets/js/catalog.js', array('jquery'), $theme_version, true);

        // AJAX для каталога
        wp_localize_script('weyer-catalog', 'weyer_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('weyer_catalog_nonce')
        ));
    }

    // Слайдер на главной
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
            'add_new_item' => 'Добавить новый товар',
            'add_new' => 'Добавить новый',
            'new_item' => 'Новый товар',
            'edit_item' => 'Редактировать товар',
            'view_item' => 'Просмотреть товар',
            'all_items' => 'Все товары',
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
 * Мета-боксы для товаров
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

    add_meta_box(
            'product-import',
            'Импорт товаров',
            'weyer_product_import_callback',
            'product',
            'side',
            'low'
    );
}
add_action('add_meta_boxes', 'weyer_add_product_meta_boxes');

/**
 * Callback для мета-бокса характеристик товара
 */
function weyer_product_details_callback($post) {
    wp_nonce_field('weyer_product_meta_nonce', 'product_meta_nonce');

    $fields = array(
            'sku' => get_post_meta($post->ID, '_product_sku', true),
            'price' => get_post_meta($post->ID, '_product_price', true),
            'old_price' => get_post_meta($post->ID, '_product_old_price', true),
            'material' => get_post_meta($post->ID, '_product_material', true),
            'diameter' => get_post_meta($post->ID, '_product_diameter', true),
            'temperature' => get_post_meta($post->ID, '_product_temperature', true),
            'protection' => get_post_meta($post->ID, '_product_protection', true),
            'in_stock' => get_post_meta($post->ID, '_product_in_stock', true),
            'is_hit' => get_post_meta($post->ID, '_product_is_hit', true),
    );
    ?>

    <table class="form-table">
        <tr>
            <th><label for="product_sku">Артикул (SKU)</label></th>
            <td><input type="text" id="product_sku" name="product_sku" value="<?php echo esc_attr($fields['sku']); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="product_price">Цена (₽)</label></th>
            <td><input type="number" id="product_price" name="product_price" value="<?php echo esc_attr($fields['price']); ?>" step="0.01" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="product_old_price">Старая цена (₽)</label></th>
            <td><input type="number" id="product_old_price" name="product_old_price" value="<?php echo esc_attr($fields['old_price']); ?>" step="0.01" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="product_material">Материал</label></th>
            <td><input type="text" id="product_material" name="product_material" value="<?php echo esc_attr($fields['material']); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="product_diameter">Диаметр</label></th>
            <td><input type="text" id="product_diameter" name="product_diameter" value="<?php echo esc_attr($fields['diameter']); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="product_temperature">Температурный диапазон</label></th>
            <td><input type="text" id="product_temperature" name="product_temperature" value="<?php echo esc_attr($fields['temperature']); ?>" placeholder="-25°C +80°C" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="product_protection">Степень защиты</label></th>
            <td><input type="text" id="product_protection" name="product_protection" value="<?php echo esc_attr($fields['protection']); ?>" placeholder="IP68" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="product_in_stock">Наличие</label></th>
            <td>
                <label>
                    <input type="checkbox" id="product_in_stock" name="product_in_stock" value="1" <?php checked($fields['in_stock'], 1); ?> />
                    Товар в наличии
                </label>
            </td>
        </tr>
        <tr>
            <th><label for="product_is_hit">Хит продаж</label></th>
            <td>
                <label>
                    <input type="checkbox" id="product_is_hit" name="product_is_hit" value="1" <?php checked($fields['is_hit'], 1); ?> />
                    Отметить как хит продаж
                </label>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Callback для импорта товаров
 */
function weyer_product_import_callback($post) {
    ?>
    <div class="weyer-import-section">
        <p><strong>Массовый импорт товаров</strong></p>
        <p>Загрузите CSV файл со следующими колонками:</p>
        <ul style="font-size: 12px; margin: 10px 0;">
            <li>title - Название товара</li>
            <li>sku - Артикул</li>
            <li>price - Цена</li>
            <li>old_price - Старая цена (опционально)</li>
            <li>material - Материал</li>
            <li>diameter - Диаметр</li>
            <li>temperature - Температура</li>
            <li>protection - Защита</li>
            <li>category - Категория</li>
            <li>in_stock - Наличие (1/0)</li>
            <li>is_hit - Хит продаж (1/0)</li>
        </ul>

        <form method="post" enctype="multipart/form-data" id="weyer-import-form">
            <?php wp_nonce_field('weyer_import_products', 'import_nonce'); ?>
            <input type="file" name="products_csv" accept=".csv" required>
            <br><br>
            <button type="submit" name="import_products" class="button button-primary">
                Импортировать товары
            </button>
        </form>

        <p><small>
                <a href="<?php echo get_template_directory_uri(); ?>/sample-products.csv" download>
                    Скачать пример CSV файла
                </a>
            </small></p>
    </div>
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

    // Чекбоксы
    $in_stock = isset($_POST['product_in_stock']) ? 1 : 0;
    update_post_meta($post_id, '_product_in_stock', $in_stock);

    $is_hit = isset($_POST['product_is_hit']) ? 1 : 0;
    update_post_meta($post_id, '_product_is_hit', $is_hit);
}
add_action('save_post', 'weyer_save_product_meta');

/**
 * Обработка импорта товаров из CSV
 */
function weyer_handle_products_import() {
    if (!isset($_POST['import_products']) || !wp_verify_nonce($_POST['import_nonce'], 'weyer_import_products')) {
        return;
    }

    if (!current_user_can('manage_options')) {
        wp_die('У вас нет прав для выполнения этого действия.');
    }

    if (empty($_FILES['products_csv']['tmp_name'])) {
        add_action('admin_notices', function() {
            echo '<div class="notice notice-error"><p>Пожалуйста, выберите CSV файл для загрузки.</p></div>';
        });
        return;
    }

    $file = $_FILES['products_csv']['tmp_name'];
    $handle = fopen($file, 'r');

    if ($handle === false) {
        add_action('admin_notices', function() {
            echo '<div class="notice notice-error"><p>Ошибка при чтении файла.</p></div>';
        });
        return;
    }

    $headers = fgetcsv($handle);
    $imported = 0;
    $errors = array();

    while (($row = fgetcsv($handle)) !== false) {
        $data = array_combine($headers, $row);

        if (empty($data['title']) || empty($data['sku'])) {
            $errors[] = "Пропущена строка: отсутствует название или артикул";
            continue;
        }

        // Создаем товар
        $post_data = array(
                'post_title' => sanitize_text_field($data['title']),
                'post_type' => 'product',
                'post_status' => 'publish',
                'post_content' => isset($data['description']) ? sanitize_textarea_field($data['description']) : '',
        );

        $post_id = wp_insert_post($post_data);

        if (is_wp_error($post_id)) {
            $errors[] = "Ошибка создания товара: " . $data['title'];
            continue;
        }

        // Добавляем мета-данные
        $meta_fields = array(
                '_product_sku' => 'sku',
                '_product_price' => 'price',
                '_product_old_price' => 'old_price',
                '_product_material' => 'material',
                '_product_diameter' => 'diameter',
                '_product_temperature' => 'temperature',
                '_product_protection' => 'protection',
                '_product_in_stock' => 'in_stock',
                '_product_is_hit' => 'is_hit',
        );

        foreach ($meta_fields as $meta_key => $data_key) {
            if (isset($data[$data_key]) && !empty($data[$data_key])) {
                update_post_meta($post_id, $meta_key, sanitize_text_field($data[$data_key]));
            }
        }

        // Добавляем категорию
        if (!empty($data['category'])) {
            $category = get_term_by('name', $data['category'], 'product_category');
            if (!$category) {
                $category = wp_insert_term($data['category'], 'product_category');
                if (!is_wp_error($category)) {
                    $category_id = $category['term_id'];
                } else {
                    $category_id = null;
                }
            } else {
                $category_id = $category->term_id;
            }

            if ($category_id) {
                wp_set_post_terms($post_id, array($category_id), 'product_category');
            }
        }

        $imported++;
    }

    fclose($handle);

    add_action('admin_notices', function() use ($imported, $errors) {
        if ($imported > 0) {
            echo '<div class="notice notice-success"><p>Успешно импортировано товаров: ' . $imported . '</p></div>';
        }

        if (!empty($errors)) {
            echo '<div class="notice notice-warning"><p>Ошибки при импорте:</p><ul>';
            foreach ($errors as $error) {
                echo '<li>' . esc_html($error) . '</li>';
            }
            echo '</ul></div>';
        }
    });

    wp_redirect(admin_url('edit.php?post_type=product'));
    exit;
}
add_action('admin_init', 'weyer_handle_products_import');

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
 * Кастомизация колонок в админке товаров
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
    if (!$post_id) $post_id = get_the_ID();
    return get_post_meta($post_id, '_product_price', true);
}

function get_product_old_price($post_id = null) {
    if (!$post_id) $post_id = get_the_ID();
    return get_post_meta($post_id, '_product_old_price', true);
}

function get_product_sku($post_id = null) {
    if (!$post_id) $post_id = get_the_ID();
    return get_post_meta($post_id, '_product_sku', true);
}

function is_product_in_stock($post_id = null) {
    if (!$post_id) $post_id = get_the_ID();
    return (bool) get_post_meta($post_id, '_product_in_stock', true);
}

function is_product_hit($post_id = null) {
    if (!$post_id) $post_id = get_the_ID();
    return (bool) get_post_meta($post_id, '_product_is_hit', true);
}

/**
 * AJAX обработчики
 */
add_action('wp_ajax_search_suggestions', 'weyer_search_suggestions');
add_action('wp_ajax_nopriv_search_suggestions', 'weyer_search_suggestions');

function weyer_search_suggestions() {
    $query = sanitize_text_field($_GET['query']);

    if (strlen($query) < 2) {
        wp_send_json_error('Query too short');
    }

    $suggestions = array();

    // Поиск по товарам
    $products = get_posts(array(
            'post_type' => 'product',
            'posts_per_page' => 5,
            's' => $query,
            'meta_query' => array(
                    array(
                            'key' => '_product_in_stock',
                            'value' => 1,
                            'compare' => '='
                    )
            )
    ));

    foreach ($products as $product) {
        $suggestions[] = array(
                'title' => $product->post_title,
                'url' => get_permalink($product),
                'type' => 'product'
        );
    }

    // Поиск по категориям
    $categories = get_terms(array(
            'taxonomy' => 'product_category',
            'name__like' => $query,
            'number' => 3,
    ));

    if ($categories && !is_wp_error($categories)) {
        foreach ($categories as $category) {
            $suggestions[] = array(
                    'title' => $category->name,
                    'url' => get_term_link($category),
                    'type' => 'category'
            );
        }
    }

    wp_send_json_success($suggestions);
}

/**
 * Настройка переписывания URL
 */
function weyer_rewrite_rules() {
    add_rewrite_rule('^catalog/?$', 'index.php?post_type=product', 'top');
    add_rewrite_rule('^catalog/page/([0-9]+)/?$', 'index.php?post_type=product&paged=$matches[1]', 'top');
}
add_action('init', 'weyer_rewrite_rules');

/**
 * Активация темы - создание страниц и настроек
 */
function weyer_theme_activation() {
    // Создаем базовые страницы
    $pages = array(
            'О компании' => array('slug' => 'about', 'content' => 'Информация о компании Weyer'),
            'Контакты' => array('slug' => 'contacts', 'content' => 'Контактная информация'),
            'Услуги' => array('slug' => 'services', 'content' => 'Наши услуги'),
            'Сравнение товаров' => array('slug' => 'compare', 'content' => '[weyer_compare_page]'),
            'Избранное' => array('slug' => 'favorites', 'content' => '[weyer_favorites_page]'),
    );

    foreach ($pages as $title => $page_data) {
        if (!get_page_by_path($page_data['slug'])) {
            wp_insert_post(array(
                    'post_title' => $title,
                    'post_name' => $page_data['slug'],
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_content' => $page_data['content']
            ));
        }
    }

    // Создание демо-категорий
    $demo_categories = array(
            'Металлорукава' => 'Гибкие металлические рукава для защиты кабелей',
            'Кабельные вводы' => 'Герметичные вводы для прокладки кабелей',
            'Соединители' => 'Промышленные соединители и разъемы',
            'Защитные системы' => 'Системы защиты электрооборудования'
    );

    foreach ($demo_categories as $name => $description) {
        if (!get_term_by('name', $name, 'product_category')) {
            wp_insert_term($name, 'product_category', array(
                    'description' => $description
            ));
        }
    }

    // Создание демо-товаров
    weyer_create_demo_products();

    // Настройки сайта
    update_option('blogdescription', '15 000+ позиций промышленного оборудования от ведущих производителей');

    // Настройки темы
    set_theme_mod('company_name', 'WEYER');
    set_theme_mod('company_tagline', 'Industrial Systems');
    set_theme_mod('company_phone', '+7 (812) 123-45-67');
    set_theme_mod('company_email', 'info@weyer.ru');
    set_theme_mod('company_address', '190000, Санкт-Петербург, Невский пр., 25');

    // Обновляем правила перезаписи
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'weyer_theme_activation');

/**
 * Создание демо-товаров
 */
function weyer_create_demo_products() {
    $demo_products = array(
            array(
                    'title' => 'Металлорукав нержавеющий 12мм',
                    'sku' => 'MR-12-SS',
                    'price' => 850,
                    'old_price' => 980,
                    'material' => 'AISI 304',
                    'diameter' => '12 мм',
                    'temperature' => '-25°C +150°C',
                    'protection' => 'IP68',
                    'category' => 'Металлорукава',
                    'in_stock' => 1,
                    'is_hit' => 1,
                    'content' => 'Высококачественный металлорукав из нержавеющей стали для защиты кабелей в агрессивных средах.'
            ),
            array(
                    'title' => 'Кабельный ввод Ex M20',
                    'sku' => 'CG-M20-EX',
                    'price' => 1790,
                    'old_price' => 1990,
                    'material' => 'Никелированная латунь',
                    'diameter' => 'M20',
                    'temperature' => '-40°C +100°C',
                    'protection' => 'Ex d IIC',
                    'category' => 'Кабельные вводы',
                    'in_stock' => 1,
                    'is_hit' => 0,
                    'content' => 'Взрывозащищенный кабельный ввод для использования во взрывоопасных зонах.'
            ),
            array(
                    'title' => 'Соединитель быстросъёмный 3P',
                    'sku' => 'CON-QC-3P',
                    'price' => 1290,
                    'old_price' => 1490,
                    'material' => 'Полиамид PA66',
                    'diameter' => '—',
                    'temperature' => '-40°C +120°C',
                    'protection' => 'IP67',
                    'category' => 'Соединители',
                    'in_stock' => 1,
                    'is_hit' => 1,
                    'content' => 'Быстросъёмный соединитель для промышленного оборудования.'
            ),
            array(
                    'title' => 'Короб защитный IP54',
                    'sku' => 'PRO-BOX-54',
                    'price' => 2450,
                    'material' => 'ABS пластик',
                    'diameter' => '200x150x100',
                    'temperature' => '-25°C +80°C',
                    'protection' => 'IP54',
                    'category' => 'Защитные системы',
                    'in_stock' => 1,
                    'is_hit' => 0,
                    'content' => 'Защитный короб для размещения электронного оборудования.'
            )
    );

    foreach ($demo_products as $product_data) {
        // Проверяем, не существует ли уже товар с таким SKU
        $existing = get_posts(array(
                'post_type' => 'product',
                'meta_key' => '_product_sku',
                'meta_value' => $product_data['sku'],
                'posts_per_page' => 1
        ));

        if (!empty($existing)) {
            continue; // Товар уже существует
        }

        $post_id = wp_insert_post(array(
                'post_title' => $product_data['title'],
                'post_type' => 'product',
                'post_status' => 'publish',
                'post_content' => $product_data['content']
        ));

        if (!is_wp_error($post_id)) {
            // Добавляем мета-данные
            update_post_meta($post_id, '_product_sku', $product_data['sku']);
            update_post_meta($post_id, '_product_price', $product_data['price']);
            if (isset($product_data['old_price'])) {
                update_post_meta($post_id, '_product_old_price', $product_data['old_price']);
            }
            update_post_meta($post_id, '_product_material', $product_data['material']);
            update_post_meta($post_id, '_product_diameter', $product_data['diameter']);
            update_post_meta($post_id, '_product_temperature', $product_data['temperature']);
            update_post_meta($post_id, '_product_protection', $product_data['protection']);
            update_post_meta($post_id, '_product_in_stock', $product_data['in_stock']);
            update_post_meta($post_id, '_product_is_hit', $product_data['is_hit']);

            // Добавляем категорию
            $category = get_term_by('name', $product_data['category'], 'product_category');
            if ($category) {
                wp_set_post_terms($post_id, array($category->term_id), 'product_category');
            }
        }
    }
}

/**
 * Шорткоды для страниц
 */
function weyer_compare_page_shortcode() {
    ob_start();
    ?>
    <div id="compare-page">
        <div class="compare-container">
            <h2>Сравнение товаров</h2>
            <div id="compare-products">
                <!-- Товары для сравнения загружаются через JavaScript -->
            </div>
            <div id="compare-empty" style="text-align: center; padding: 40px; display: none;">
                <p>Вы еще не добавили товары для сравнения.</p>
                <a href="<?php echo get_post_type_archive_link('product'); ?>" class="btn btn-primary">
                    Перейти к каталогу
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadCompareProducts();
        });

        function loadCompareProducts() {
            const compareList = JSON.parse(localStorage.getItem('weyer_compare') || '[]');
            const container = document.getElementById('compare-products');
            const emptyMessage = document.getElementById('compare-empty');

            if (compareList.length === 0) {
                container.style.display = 'none';
                emptyMessage.style.display = 'block';
                return;
            }

            // Загружаем данные о товарах через AJAX
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_compare_products&products=' + compareList.join(',') + '&nonce=<?php echo wp_create_nonce('weyer_compare_nonce'); ?>'
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        container.innerHTML = data.data;
                        container.style.display = 'block';
                        emptyMessage.style.display = 'none';
                    }
                });
        }
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('weyer_compare_page', 'weyer_compare_page_shortcode');

function weyer_favorites_page_shortcode() {
    ob_start();
    ?>
    <div id="favorites-page">
        <div class="favorites-container">
            <h2>Избранные товары</h2>
            <div id="favorites-products">
                <!-- Избранные товары загружаются через JavaScript -->
            </div>
            <div id="favorites-empty" style="text-align: center; padding: 40px; display: none;">
                <p>У вас пока нет избранных товаров.</p>
                <a href="<?php echo get_post_type_archive_link('product'); ?>" class="btn btn-primary">
                    Перейти к каталогу
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadFavoriteProducts();
        });

        function loadFavoriteProducts() {
            const favoritesList = JSON.parse(localStorage.getItem('weyer_favorites') || '[]');
            const container = document.getElementById('favorites-products');
            const emptyMessage = document.getElementById('favorites-empty');

            if (favoritesList.length === 0) {
                container.style.display = 'none';
                emptyMessage.style.display = 'block';
                return;
            }

            // Загружаем данные о товарах через AJAX
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_favorite_products&products=' + favoritesList.join(',') + '&nonce=<?php echo wp_create_nonce('weyer_favorites_nonce'); ?>'
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        container.innerHTML = data.data;
                        container.style.display = 'block';
                        emptyMessage.style.display = 'none';
                    }
                });
        }
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('weyer_favorites_page', 'weyer_favorites_page_shortcode');

/**
 * AJAX обработчики для сравнения и избранного
 */
add_action('wp_ajax_get_compare_products', 'weyer_get_compare_products');
add_action('wp_ajax_nopriv_get_compare_products', 'weyer_get_compare_products');

function weyer_get_compare_products() {
    if (!wp_verify_nonce($_POST['nonce'], 'weyer_compare_nonce')) {
        wp_send_json_error('Invalid nonce');
    }

    $product_ids = explode(',', sanitize_text_field($_POST['products']));
    $product_ids = array_map('intval', $product_ids);

    if (empty($product_ids)) {
        wp_send_json_success('');
    }

    $products = get_posts(array(
            'post_type' => 'product',
            'post__in' => $product_ids,
            'posts_per_page' => -1
    ));

    ob_start();
    ?>
    <div class="compare-table">
        <table>
            <thead>
            <tr>
                <th>Характеристика</th>
                <?php foreach ($products as $product): ?>
                    <th>
                        <div class="product-info">
                            <?php if (has_post_thumbnail($product->ID)): ?>
                                <?php echo get_the_post_thumbnail($product->ID, 'thumbnail'); ?>
                            <?php endif; ?>
                            <h4><?php echo esc_html($product->post_title); ?></h4>
                            <button class="btn btn-sm" onclick="removeFromCompare(<?php echo $product->ID; ?>)">
                                Убрать из сравнения
                            </button>
                        </div>
                    </th>
                <?php endforeach; ?>
            </tr>
            </thead>
            <tbody>
            <?php
            $characteristics = array(
                    'Артикул' => '_product_sku',
                    'Цена' => '_product_price',
                    'Материал' => '_product_material',
                    'Диаметр' => '_product_diameter',
                    'Температура' => '_product_temperature',
                    'Защита' => '_product_protection',
                    'Наличие' => '_product_in_stock'
            );

            foreach ($characteristics as $label => $meta_key): ?>
                <tr>
                    <td><strong><?php echo $label; ?></strong></td>
                    <?php foreach ($products as $product):
                        $value = get_post_meta($product->ID, $meta_key, true);
                        if ($meta_key === '_product_price' && $value) {
                            $value = number_format($value, 0, '.', ' ') . ' ₽';
                        } elseif ($meta_key === '_product_in_stock') {
                            $value = $value ? 'В наличии' : 'Нет в наличии';
                        }
                        ?>
                        <td><?php echo $value ? esc_html($value) : '—'; ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function removeFromCompare(productId) {
            let compareList = JSON.parse(localStorage.getItem('weyer_compare') || '[]');
            compareList = compareList.filter(id => id != productId);
            localStorage.setItem('weyer_compare', JSON.stringify(compareList));
            loadCompareProducts();
        }
    </script>
    <?php
    wp_send_json_success(ob_get_clean());
}

add_action('wp_ajax_get_favorite_products', 'weyer_get_favorite_products');
add_action('wp_ajax_nopriv_get_favorite_products', 'weyer_get_favorite_products');

function weyer_get_favorite_products() {
    if (!wp_verify_nonce($_POST['nonce'], 'weyer_favorites_nonce')) {
        wp_send_json_error('Invalid nonce');
    }

    $product_ids = explode(',', sanitize_text_field($_POST['products']));
    $product_ids = array_map('intval', $product_ids);

    if (empty($product_ids)) {
        wp_send_json_success('');
    }

    $products = get_posts(array(
            'post_type' => 'product',
            'post__in' => $product_ids,
            'posts_per_page' => -1
    ));

    ob_start();
    ?>
    <div class="favorites-grid">
        <?php foreach ($products as $product):
            setup_postdata($product);
            get_template_part('template-parts/product-card');
        endforeach;
        wp_reset_postdata(); ?>
    </div>
    <?php
    wp_send_json_success(ob_get_clean());
}

/**
 * Деактивация темы
 */
function weyer_theme_deactivation() {
    flush_rewrite_rules();
}
add_action('switch_theme', 'weyer_theme_deactivation');

/**
 * AJAX обработчик для фильтрации товаров
 */
add_action('wp_ajax_filter_products', 'weyer_filter_products');
add_action('wp_ajax_nopriv_filter_products', 'weyer_filter_products');

function weyer_filter_products() {
    if (!wp_verify_nonce($_POST['nonce'], 'weyer_catalog_nonce')) {
        wp_send_json_error('Invalid nonce');
    }

    $search = sanitize_text_field($_POST['search'] ?? '');
    $category = intval($_POST['category'] ?? 0);
    $price_min = floatval($_POST['price_min'] ?? 0);
    $price_max = floatval($_POST['price_max'] ?? 0);
    $material = sanitize_text_field($_POST['material'] ?? '');
    $in_stock = (bool)($_POST['in_stock'] ?? false);
    $hits_only = (bool)($_POST['hits_only'] ?? false);
    $sort = sanitize_text_field($_POST['sort'] ?? 'date');

    $args = array(
            'post_type' => 'product',
            'posts_per_page' => 12,
            'post_status' => 'publish',
            'meta_query' => array('relation' => 'AND'),
            'tax_query' => array('relation' => 'AND')
    );

    // Поиск по названию
    if (!empty($search)) {
        $args['s'] = $search;
    }

    // Фильтр по категории
    if ($category > 0) {
        $args['tax_query'][] = array(
                'taxonomy' => 'product_category',
                'field' => 'term_id',
                'terms' => $category
        );
    }

    // Фильтр по цене
    if ($price_min > 0) {
        $args['meta_query'][] = array(
                'key' => '_product_price',
                'value' => $price_min,
                'compare' => '>=',
                'type' => 'DECIMAL'
        );
    }

    if ($price_max > 0) {
        $args['meta_query'][] = array(
                'key' => '_product_price',
                'value' => $price_max,
                'compare' => '<=',
                'type' => 'DECIMAL'
        );
    }

    // Фильтр по материалу
    if (!empty($material)) {
        $args['meta_query'][] = array(
                'key' => '_product_material',
                'value' => $material,
                'compare' => '='
        );
    }

    // Фильтр по наличию
    if ($in_stock) {
        $args['meta_query'][] = array(
                'key' => '_product_in_stock',
                'value' => 1,
                'compare' => '='
        );
    }

    // Фильтр по хитам
    if ($hits_only) {
        $args['meta_query'][] = array(
                'key' => '_product_is_hit',
                'value' => 1,
                'compare' => '='
        );
    }

    // Сортировка
    switch ($sort) {
        case 'title':
            $args['orderby'] = 'title';
            $args['order'] = 'ASC';
            break;
        case 'price_asc':
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = '_product_price';
            $args['order'] = 'ASC';
            break;
        case 'price_desc':
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = '_product_price';
            $args['order'] = 'DESC';
            break;
        case 'popularity':
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = '_product_views';
            $args['order'] = 'DESC';
            break;
        default:
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
    }

    $query = new WP_Query($args);
    $html = '';

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            ob_start();
            get_template_part('template-parts/product-card');
            $html .= ob_get_clean();
        }
        wp_reset_postdata();
    } else {
        $html = '<div class="no-products">
            <i class="fas fa-search" style="font-size: 48px; opacity: 0.3; margin-bottom: 16px;"></i>
            <h3>Товары не найдены</h3>
            <p>Попробуйте изменить параметры поиска или очистить фильтры</p>
        </div>';
    }

    wp_send_json_success(array(
            'html' => $html,
            'count' => $query->found_posts
    ));
}

/**
 * AJAX обработчик для быстрого просмотра товара
 */
add_action('wp_ajax_get_product_quick_view', 'weyer_get_product_quick_view');
add_action('wp_ajax_nopriv_get_product_quick_view', 'weyer_get_product_quick_view');

function weyer_get_product_quick_view() {
    if (!wp_verify_nonce($_GET['nonce'], 'weyer_catalog_nonce')) {
        wp_send_json_error('Invalid nonce');
    }

    $product_id = intval($_GET['product_id']);
    $product = get_post($product_id);

    if (!$product || $product->post_type !== 'product') {
        wp_send_json_error('Product not found');
    }

    $sku = get_product_sku($product_id);
    $price = get_product_price($product_id);
    $old_price = get_product_old_price($product_id);
    $material = get_post_meta($product_id, '_product_material', true);
    $diameter = get_post_meta($product_id, '_product_diameter', true);
    $temperature = get_post_meta($product_id, '_product_temperature', true);
    $protection = get_post_meta($product_id, '_product_protection', true);
    $in_stock = is_product_in_stock($product_id);

    ob_start();
    ?>
    <div class="quick-view-content">
        <div class="quick-view-image">
            <?php if (has_post_thumbnail($product_id)): ?>
                <img src="<?php echo get_the_post_thumbnail_url($product_id, 'product-large'); ?>"
                     alt="<?php echo esc_attr($product->post_title); ?>">
            <?php endif; ?>
        </div>

        <div class="quick-view-info">
            <?php if ($sku): ?>
                <div class="product-sku">Артикул: <?php echo esc_html($sku); ?></div>
            <?php endif; ?>

            <h3><?php echo esc_html($product->post_title); ?></h3>

            <?php if ($product->post_excerpt): ?>
                <p><?php echo esc_html($product->post_excerpt); ?></p>
            <?php endif; ?>

            <div class="quick-view-specs">
                <?php if ($material): ?>
                    <div><strong>Материал:</strong> <?php echo esc_html($material); ?></div>
                <?php endif; ?>
                <?php if ($diameter): ?>
                    <div><strong>Диаметр:</strong> <?php echo esc_html($diameter); ?></div>
                <?php endif; ?>
                <?php if ($temperature): ?>
                    <div><strong>Температура:</strong> <?php echo esc_html($temperature); ?></div>
                <?php endif; ?>
                <?php if ($protection): ?>
                    <div><strong>Защита:</strong> <?php echo esc_html($protection); ?></div>
                <?php endif; ?>
            </div>

            <div class="quick-view-price">
                <?php if ($old_price && $old_price > $price): ?>
                    <span class="price-old"><?php echo number_format($old_price, 0, '.', ' '); ?> ₽</span>
                <?php endif; ?>
                <span class="price-current"><?php echo number_format($price, 0, '.', ' '); ?> ₽</span>
            </div>

            <div class="quick-view-actions">
                <a href="<?php echo get_permalink($product_id); ?>" class="btn btn-primary">
                    Подробнее
                </a>
                <?php if ($in_stock): ?>
                    <button class="btn btn-secondary" onclick="requestQuote(<?php echo $product_id; ?>)">
                        Запросить КП
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php

    wp_send_json_success(ob_get_clean());
}

/**
 * Обработчик формы запроса КП
 */
add_action('wp_ajax_request_quote', 'weyer_request_quote');
add_action('wp_ajax_nopriv_request_quote', 'weyer_request_quote');

function weyer_request_quote() {
    if (!wp_verify_nonce($_POST['nonce'], 'weyer_quote_nonce')) {
        wp_send_json_error('Invalid nonce');
    }

    $product_id = intval($_POST['product_id']);
    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $phone = sanitize_text_field($_POST['phone']);
    $company = sanitize_text_field($_POST['company']);
    $message = sanitize_textarea_field($_POST['message']);
    $quantity = intval($_POST['quantity']) ?: 1;

    if (empty($name) || empty($email) || empty($phone)) {
        wp_send_json_error('Заполните обязательные поля');
    }

    $product = get_post($product_id);
    if (!$product) {
        wp_send_json_error('Товар не найден');
    }

    // Отправляем email администратору
    $to = get_option('admin_email');
    $subject = 'Запрос коммерческого предложения - ' . get_bloginfo('name');

    $email_message = "<h3>Новый запрос коммерческого предложения</h3>";
    $email_message .= "<p><strong>Товар:</strong> {$product->post_title}</p>";
    $email_message .= "<p><strong>Артикул:</strong> " . get_product_sku($product_id) . "</p>";
    $email_message .= "<p><strong>Количество:</strong> {$quantity} шт.</p>";
    $email_message .= "<hr>";
    $email_message .= "<p><strong>Имя:</strong> {$name}</p>";
    $email_message .= "<p><strong>Email:</strong> {$email}</p>";
    $email_message .= "<p><strong>Телефон:</strong> {$phone}</p>";
    if ($company) {
        $email_message .= "<p><strong>Компания:</strong> {$company}</p>";
    }
    if ($message) {
        $email_message .= "<p><strong>Комментарий:</strong> {$message}</p>";
    }
    $email_message .= "<p><strong>Дата:</strong> " . current_time('d.m.Y H:i') . "</p>";

    $headers = array('Content-Type: text/html; charset=UTF-8');

    if (wp_mail($to, $subject, $email_message, $headers)) {
        wp_send_json_success('Запрос успешно отправлен! Мы свяжемся с вами в ближайшее время.');
    } else {
        wp_send_json_error('Ошибка отправки запроса. Попробуйте еще раз.');
    }
}

/**
 * Счетчик просмотров товара
 */
function weyer_track_product_views() {
    if (is_singular('product')) {
        $product_id = get_the_ID();
        $views = get_post_meta($product_id, '_product_views', true) ?: 0;
        $views++;
        update_post_meta($product_id, '_product_views', $views);
    }
}
add_action('wp_head', 'weyer_track_product_views');

/**
 * Хлебные крошки
 */
function weyer_breadcrumbs() {
    echo '<a href="' . home_url() . '"><i class="fas fa-home"></i> Главная</a>';

    if (is_post_type_archive('product')) {
        echo ' > <span>Каталог</span>';
    } elseif (is_tax()) {
        echo ' > <a href="' . get_post_type_archive_link('product') . '">Каталог</a>';
        echo ' > <span>' . single_term_title('', false) . '</span>';
    } elseif (is_singular('product')) {
        echo ' > <a href="' . get_post_type_archive_link('product') . '">Каталог</a>';

        $terms = get_the_terms(get_the_ID(), 'product_category');
        if ($terms && !is_wp_error($terms)) {
            $term = array_shift($terms);
            echo ' > <a href="' . get_term_link($term) . '">' . $term->name . '</a>';
        }

        echo ' > <span>' . get_the_title() . '</span>';
    } elseif (is_page()) {
        echo ' > <span>' . get_the_title() . '</span>';
    } elseif (is_search()) {
        echo ' > <span>Поиск: ' . get_search_query() . '</span>';
    }
}

/**
 * Добавление поля галереи в мета-бокс товара
 */
function weyer_add_gallery_field() {
    add_meta_box(
            'product-gallery',
            'Галерея изображений',
            'weyer_product_gallery_callback',
            'product',
            'side',
            'low'
    );
}
add_action('add_meta_boxes', 'weyer_add_gallery_field');

function weyer_product_gallery_callback($post) {
    wp_nonce_field('weyer_gallery_meta_nonce', 'gallery_meta_nonce');

    $gallery_images = get_post_meta($post->ID, '_product_gallery', true);
    ?>
    <div id="product-gallery-container">
        <input type="hidden" name="product_gallery" id="product_gallery" value="<?php echo esc_attr(implode(',', (array)$gallery_images)); ?>">
        <div id="gallery-images">
            <?php if ($gallery_images && is_array($gallery_images)): ?>
                <?php foreach ($gallery_images as $image_id): ?>
                    <div class="gallery-image" data-id="<?php echo $image_id; ?>">
                        <img src="<?php echo wp_get_attachment_image_url($image_id, 'thumbnail'); ?>" alt="">
                        <button type="button" class="remove-image">&times;</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <button type="button" id="add-gallery-images" class="button">Добавить изображения</button>
    </div>

    <script>
        jQuery(document).ready(function($) {
            let mediaUploader;

            $('#add-gallery-images').click(function(e) {
                e.preventDefault();

                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }

                mediaUploader = wp.media.frames.file_frame = wp.media({
                    title: 'Выберите изображения',
                    button: {
                        text: 'Добавить'
                    },
                    multiple: true
                });

                mediaUploader.on('select', function() {
                    const attachments = mediaUploader.state().get('selection').toJSON();
                    let galleryIds = $('#product_gallery').val().split(',').filter(id => id);

                    attachments.forEach(function(attachment) {
                        if (!galleryIds.includes(attachment.id.toString())) {
                            galleryIds.push(attachment.id);
                            $('#gallery-images').append(`
                            <div class="gallery-image" data-id="${attachment.id}">
                                <img src="${attachment.sizes.thumbnail.url}" alt="">
                                <button type="button" class="remove-image">&times;</button>
                            </div>
                        `);
                        }
                    });

                    $('#product_gallery').val(galleryIds.join(','));
                });

                mediaUploader.open();
            });

            $(document).on('click', '.remove-image', function() {
                const imageId = $(this).parent().data('id');
                let galleryIds = $('#product_gallery').val().split(',').filter(id => id && id != imageId);
                $('#product_gallery').val(galleryIds.join(','));
                $(this).parent().remove();
            });
        });
    </script>

    <style>
        #gallery-images {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 10px;
        }

        .gallery-image {
            position: relative;
            width: 80px;
            height: 80px;
        }

        .gallery-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 4px;
        }

        .remove-image {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #dc3545;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 12px;
            line-height: 1;
        }
    </style>
    <?php
}

/**
 * Сохранение галереи товара
 */
function weyer_save_product_gallery($post_id) {
    if (!isset($_POST['gallery_meta_nonce']) || !wp_verify_nonce($_POST['gallery_meta_nonce'], 'weyer_gallery_meta_nonce')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['product_gallery'])) {
        $gallery_ids = array_filter(explode(',', $_POST['product_gallery']));
        $gallery_ids = array_map('intval', $gallery_ids);
        update_post_meta($post_id, '_product_gallery', $gallery_ids);
    }
}
add_action('save_post', 'weyer_save_product_gallery');

/**
 * Создание примера CSV файла
 */
function weyer_create_sample_csv() {
    add_action('init', function() {
        if (isset($_GET['download_sample_csv']) && current_user_can('manage_options')) {
            $filename = 'sample-products.csv';
            $sample_data = array(
                    array('title', 'sku', 'price', 'old_price', 'material', 'diameter', 'temperature', 'protection', 'category', 'in_stock', 'is_hit', 'description'),
                    array('Металлорукав нержавеющий 12мм', 'MR-12-SS', '850', '980', 'AISI 304', '12 мм', '-25°C +150°C', 'IP68', 'Металлорукава', '1', '1', 'Высококачественный металлорукав'),
                    array('Кабельный ввод Ex M20', 'CG-M20-EX', '1790', '1990', 'Никелированная латунь', 'M20', '-40°C +100°C', 'Ex d IIC', 'Кабельные вводы', '1', '0', 'Взрывозащищенный кабельный ввод'),
            );

            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');

            $output = fopen('php://output', 'w');
            foreach ($sample_data as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
            exit;
        }
    });
}
weyer_create_sample_csv();

/**
 * БЫСТРЫЕ ФИКСЫ - добавить в конец functions.php
 */

// Исправление подключения скриптов
function weyer_enqueue_scripts_fix() {
    wp_enqueue_script('weyer-main', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '1.0', true);

    if (is_post_type_archive('product') || is_tax('product_category')) {
        wp_enqueue_script('weyer-catalog', get_template_directory_uri() . '/assets/js/catalog.js', array('jquery'), '1.0', true);
        wp_localize_script('weyer-catalog', 'weyer_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('weyer_catalog_nonce')
        ));
    }

    if (is_front_page()) {
        wp_enqueue_script('weyer-hero', get_template_directory_uri() . '/assets/js/hero.js', array(), '1.0', true);
    }
}
add_action('wp_enqueue_scripts', 'weyer_enqueue_scripts_fix', 20);

// Создать директории для JS
function weyer_create_js_directory() {
    $js_dir = get_template_directory() . '/assets/js';
    if (!file_exists($js_dir)) {
        wp_mkdir_p($js_dir);
    }
}
add_action('after_setup_theme', 'weyer_create_js_directory');

// Быстрый обработчик запроса КП
add_action('wp_ajax_request_quote', 'weyer_quick_quote');
add_action('wp_ajax_nopriv_request_quote', 'weyer_quick_quote');

function weyer_quick_quote() {
    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $phone = sanitize_text_field($_POST['phone']);
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']) ?: 1;

    if (empty($name) || empty($email) || empty($phone)) {
        wp_send_json_error('Заполните все поля');
    }

    $product = get_post($product_id);
    $subject = 'Запрос КП - ' . get_bloginfo('name');
    $message = "Новый запрос КП:\n\n";
    $message .= "Товар: {$product->post_title}\n";
    $message .= "Количество: {$quantity}\n\n";
    $message .= "Клиент:\n";
    $message .= "Имя: {$name}\n";
    $message .= "Email: {$email}\n";
    $message .= "Телефон: {$phone}\n";
    $message .= "Дата: " . current_time('d.m.Y H:i');

    if (wp_mail(get_option('admin_email'), $subject, $message)) {
        wp_send_json_success('Запрос отправлен!');
    } else {
        wp_send_json_error('Ошибка отправки');
    }
}

// Исправление breadcrumbs если функция не существует
if (!function_exists('weyer_breadcrumbs')) {
    function weyer_breadcrumbs() {
        echo '<a href="' . home_url() . '">Главная</a>';

        if (is_singular('product')) {
            echo ' > <a href="' . get_post_type_archive_link('product') . '">Каталог</a>';
            echo ' > ' . get_the_title();
        } elseif (is_post_type_archive('product')) {
            echo ' > Каталог';
        } elseif (is_page()) {
            echo ' > ' . get_the_title();
        }
    }
}

// Создать папки и placeholder изображения
function weyer_setup_assets() {
    $dirs = array(
            get_template_directory() . '/assets',
            get_template_directory() . '/assets/js',
            get_template_directory() . '/assets/css',
            get_template_directory() . '/assets/images',
    );

    foreach ($dirs as $dir) {
        if (!file_exists($dir)) {
            wp_mkdir_p($dir);
        }
    }
}
add_action('after_setup_theme', 'weyer_setup_assets');

// Сброс настроек при активации темы
function weyer_theme_activation_quick() {
    flush_rewrite_rules();

    // Создать базовые страницы если их нет
    $pages = array(
            'compare' => 'Сравнение товаров',
            'favorites' => 'Избранное',
    );

    foreach ($pages as $slug => $title) {
        if (!get_page_by_path($slug)) {
            wp_insert_post(array(
                    'post_title' => $title,
                    'post_name' => $slug,
                    'post_status' => 'publish',
                    'post_type' => 'page',
            ));
        }
    }
}
register_activation_hook(__FILE__, 'weyer_theme_activation_quick');