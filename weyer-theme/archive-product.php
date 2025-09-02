<?php
/**
 * Архив товаров (каталог)
 * Template Name: Каталог товаров
 */

get_header(); ?>

    <main class="catalog-page">
        <div class="container">
            <!-- Хлебные крошки -->
            <nav class="breadcrumbs" aria-label="Навигация">
                <?php weyer_breadcrumbs(); ?>
            </nav>

            <!-- Заголовок страницы -->
            <div class="page-header">
                <h1><?php echo is_tax() ? single_term_title('', false) : 'Каталог товаров'; ?></h1>
                <?php if (is_tax() && term_description()): ?>
                    <p class="category-description"><?php echo term_description(); ?></p>
                <?php endif; ?>
            </div>

            <div class="catalog-layout">
                <!-- Боковая панель с фильтрами -->
                <aside class="catalog-sidebar">
                    <div class="filters-wrapper">
                        <h3>Фильтры</h3>

                        <!-- Поиск -->
                        <div class="filter-group">
                            <label>Поиск по товарам</label>
                            <input type="text" id="product-search" placeholder="Введите название..."
                                   value="<?php echo get_search_query(); ?>">
                        </div>

                        <!-- Категории -->
                        <div class="filter-group">
                            <h4>Категории</h4>
                            <?php
                            $categories = get_terms(array(
                                'taxonomy' => 'product_category',
                                'hide_empty' => true,
                            ));

                            if ($categories && !is_wp_error($categories)):
                                $current_term = get_queried_object();
                                $current_cat_id = is_tax('product_category') ? $current_term->term_id : 0;
                                ?>
                                <ul class="category-filter">
                                    <li>
                                        <label>
                                            <input type="radio" name="category" value="" <?php echo !$current_cat_id ? 'checked' : ''; ?>>
                                            <span>Все категории</span>
                                        </label>
                                    </li>
                                    <?php foreach ($categories as $category): ?>
                                        <li>
                                            <label>
                                                <input type="radio" name="category" value="<?php echo $category->term_id; ?>"
                                                    <?php echo $current_cat_id == $category->term_id ? 'checked' : ''; ?>>
                                                <span><?php echo $category->name; ?> (<?php echo $category->count; ?>)</span>
                                            </label>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>

                        <!-- Цена -->
                        <?php
                        $price_range = weyer_get_products_price_range();
                        ?>
                        <div class="filter-group">
                            <h4>Цена (₽)</h4>
                            <div class="price-range">
                                <input type="number" id="price-min" placeholder="От" min="0" value="" data-min="<?php echo $price_range['min']; ?>">
                                <input type="number" id="price-max" placeholder="До" min="0" value="" data-max="<?php echo $price_range['max']; ?>">
                            </div>
                        </div>

                        <!-- Материал -->
                        <?php
                        $materials = weyer_get_unique_product_meta('_product_material');
                        if (!empty($materials)):
                            ?>
                            <div class="filter-group">
                                <h4>Материал</h4>
                                <select id="material-filter">
                                    <option value="">Любой материал</option>
                                    <?php foreach ($materials as $material): ?>
                                        <option value="<?php echo esc_attr($material); ?>"><?php echo esc_html($material); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <!-- Наличие -->
                        <div class="filter-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="in-stock-only">
                                <span>Только товары в наличии</span>
                            </label>
                        </div>

                        <!-- Хиты продаж -->
                        <div class="filter-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="hits-only">
                                <span>Только хиты продаж</span>
                            </label>
                        </div>

                        <button type="button" id="apply-filters" class="btn btn-primary">
                            Применить фильтры
                        </button>

                        <button type="button" id="reset-filters" class="btn btn-ghost">
                            Сбросить
                        </button>
                    </div>
                </aside>

                <!-- Основной контент каталога -->
                <div class="catalog-content">
                    <!-- Панель управления -->
                    <div class="catalog-controls">
                        <div class="catalog-info">
                        <span id="products-count">
                            <?php
                            global $wp_query;
                            echo sprintf('Найдено товаров: %d', $wp_query->found_posts);
                            ?>
                        </span>
                        </div>

                        <div class="catalog-actions">
                            <!-- Вид отображения -->
                            <div class="view-toggle">
                                <button class="view-btn active" data-view="grid" title="Сетка">
                                    <i class="fas fa-th-large"></i>
                                </button>
                                <button class="view-btn" data-view="list" title="Список">
                                    <i class="fas fa-list"></i>
                                </button>
                            </div>

                            <!-- Сортировка -->
                            <select id="sort-products">
                                <option value="date">По новизне</option>
                                <option value="title">По названию</option>
                                <option value="price_asc">По цене (возрастание)</option>
                                <option value="price_desc">По цене (убывание)</option>
                                <option value="popularity">По популярности</option>
                            </select>
                        </div>
                    </div>

                    <!-- Сетка товаров -->
                    <div class="catalog-grid grid-view" id="products-grid">
                        <?php
                        if (have_posts()) :
                            while (have_posts()) : the_post();
                                get_template_part('template-parts/product-card');
                            endwhile;
                        else :
                            ?>
                            <div class="no-products">
                                <i class="fas fa-search" style="font-size: 48px; opacity: 0.3; margin-bottom: 16px;"></i>
                                <h3>Товары не найдены</h3>
                                <p>Попробуйте изменить параметры поиска или очистить фильтры</p>
                                <button class="btn btn-primary" onclick="resetFilters()">Сбросить фильтры</button>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Пагинация -->
                    <div class="catalog-pagination">
                        <?php
                        echo paginate_links(array(
                            'total' => $wp_query->max_num_pages,
                            'current' => max(1, get_query_var('paged')),
                            'format' => '?paged=%#%',
                            'show_all' => false,
                            'end_size' => 1,
                            'mid_size' => 2,
                            'prev_next' => true,
                            'prev_text' => '<i class="fas fa-chevron-left"></i> Назад',
                            'next_text' => 'Далее <i class="fas fa-chevron-right"></i>',
                            'add_args' => false,
                            'add_fragment' => '',
                        ));
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- CSS для каталога -->
    <style>
        .catalog-page {
            padding: 40px 0 80px;
            background: var(--g50);
            min-height: 70vh;
        }

        .breadcrumbs {
            margin-bottom: 24px;
            font-size: 14px;
            color: var(--g600);
        }

        .breadcrumbs a {
            color: var(--primary);
            text-decoration: none;
        }

        .breadcrumbs a:hover {
            text-decoration: underline;
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-header h1 {
            font-size: clamp(2rem, 4vw, 3rem);
            margin-bottom: 16px;
            color: var(--secondary);
        }

        .category-description {
            color: var(--g600);
            max-width: 600px;
            margin: 0 auto;
        }

        .catalog-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 32px;
            align-items: start;
        }

        /* Боковая панель фильтров */
        .catalog-sidebar {
            background: white;
            border-radius: var(--r-xl);
            padding: 24px;
            box-shadow: var(--shadow-md);
            position: sticky;
            top: 100px;
        }

        .filters-wrapper h3 {
            margin: 0 0 20px;
            font-size: 18px;
            color: var(--secondary);
        }

        .filter-group {
            margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--g200);
        }

        .filter-group:last-of-type {
            border-bottom: none;
        }

        .filter-group h4 {
            margin: 0 0 12px;
            font-size: 14px;
            font-weight: 600;
            color: var(--g700);
        }

        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: var(--g700);
        }

        .filter-group input[type="text"],
        .filter-group input[type="number"],
        .filter-group select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--g300);
            border-radius: var(--r-md);
            font-size: 14px;
        }

        .price-range {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .category-filter {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .category-filter li {
            margin-bottom: 8px;
        }

        .category-filter label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            padding: 4px 0;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        /* Контент каталога */
        .catalog-content {
            min-height: 500px;
        }

        .catalog-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding: 16px 20px;
            background: white;
            border-radius: var(--r-lg);
            box-shadow: var(--shadow-md);
        }

        .catalog-info {
            color: var(--g600);
            font-size: 14px;
        }

        .catalog-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .view-toggle {
            display: flex;
            border: 1px solid var(--g300);
            border-radius: var(--r-md);
            overflow: hidden;
        }

        .view-btn {
            padding: 8px 12px;
            border: none;
            background: white;
            cursor: pointer;
            color: var(--g600);
            transition: all var(--t-base);
        }

        .view-btn.active,
        .view-btn:hover {
            background: var(--primary);
            color: white;
        }

        #sort-products {
            padding: 8px 12px;
            border: 1px solid var(--g300);
            border-radius: var(--r-md);
            font-size: 14px;
        }

        /* Сетка товаров */
        .catalog-grid {
            display: grid;
            gap: 24px;
            margin-bottom: 40px;
        }

        .catalog-grid.grid-view {
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        }

        .catalog-grid.list-view {
            grid-template-columns: 1fr;
        }

        .no-products {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 20px;
            color: var(--g600);
        }

        /* Пагинация */
        .catalog-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 40px;
        }

        .catalog-pagination a,
        .catalog-pagination span {
            padding: 10px 16px;
            border: 1px solid var(--g300);
            border-radius: var(--r-md);
            text-decoration: none;
            color: var(--g700);
            transition: all var(--t-base);
        }

        .catalog-pagination a:hover,
        .catalog-pagination .current {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        /* Адаптивность */
        @media (max-width: 1024px) {
            .catalog-layout {
                grid-template-columns: 1fr;
                gap: 24px;
            }

            .catalog-sidebar {
                position: static;
                order: 2;
            }

            .catalog-content {
                order: 1;
            }
        }

        @media (max-width: 768px) {
            .catalog-controls {
                flex-direction: column;
                gap: 16px;
                align-items: stretch;
            }

            .catalog-actions {
                justify-content: space-between;
            }

            .catalog-grid.grid-view {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 16px;
            }
        }
    </style>

    <!-- JavaScript для каталога -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Переключение вида
            const viewButtons = document.querySelectorAll('.view-btn');
            const catalogGrid = document.getElementById('products-grid');

            viewButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    viewButtons.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    const view = this.dataset.view;
                    catalogGrid.className = `catalog-grid ${view}-view`;
                });
            });

            // Фильтры
            let filterTimeout;

            function applyFilters() {
                clearTimeout(filterTimeout);
                filterTimeout = setTimeout(() => {
                    const filters = {
                        search: document.getElementById('product-search')?.value || '',
                        category: document.querySelector('input[name="category"]:checked')?.value || '',
                        price_min: document.getElementById('price-min')?.value || '',
                        price_max: document.getElementById('price-max')?.value || '',
                        material: document.getElementById('material-filter')?.value || '',
                        in_stock: document.getElementById('in-stock-only')?.checked || false,
                        hits_only: document.getElementById('hits-only')?.checked || false,
                        sort: document.getElementById('sort-products')?.value || 'date'
                    };

                    loadProducts(filters);
                }, 500);
            }

            // Обработчики событий для фильтров
            document.getElementById('product-search')?.addEventListener('input', applyFilters);
            document.getElementById('price-min')?.addEventListener('change', applyFilters);
            document.getElementById('price-max')?.addEventListener('change', applyFilters);
            document.getElementById('material-filter')?.addEventListener('change', applyFilters);
            document.getElementById('in-stock-only')?.addEventListener('change', applyFilters);
            document.getElementById('hits-only')?.addEventListener('change', applyFilters);
            document.getElementById('sort-products')?.addEventListener('change', applyFilters);

            // Радио-кнопки категорий
            document.querySelectorAll('input[name="category"]').forEach(radio => {
                radio.addEventListener('change', applyFilters);
            });

            // Кнопки применения фильтров
            document.getElementById('apply-filters')?.addEventListener('click', applyFilters);

            document.getElementById('reset-filters')?.addEventListener('click', function() {
                // Сброс всех фильтров
                document.getElementById('product-search').value = '';
                document.querySelector('input[name="category"][value=""]').checked = true;
                document.getElementById('price-min').value = '';
                document.getElementById('price-max').value = '';
                document.getElementById('material-filter').value = '';
                document.getElementById('in-stock-only').checked = false;
                document.getElementById('hits-only').checked = false;
                document.getElementById('sort-products').value = 'date';

                applyFilters();
            });

            function loadProducts(filters) {
                catalogGrid.classList.add('loading');

                const formData = new FormData();
                formData.append('action', 'filter_products');
                formData.append('nonce', '<?php echo wp_create_nonce('weyer_catalog_nonce'); ?>');

                Object.keys(filters).forEach(key => {
                    formData.append(key, filters[key]);
                });

                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        catalogGrid.classList.remove('loading');

                        if (data.success) {
                            catalogGrid.innerHTML = data.data.html;
                            document.getElementById('products-count').textContent = `Найдено товаров: ${data.data.count}`;
                        } else {
                            catalogGrid.innerHTML = '<div class="no-products"><p>Ошибка загрузки товаров</p></div>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        catalogGrid.classList.remove('loading');
                        catalogGrid.innerHTML = '<div class="no-products"><p>Ошибка загрузки товаров</p></div>';
                    });
            }
        });
    </script>

<?php get_footer(); ?>

<?php
/**
 * Функция для получения диапазона цен товаров
 */
function weyer_get_products_price_range() {
    global $wpdb;

    $results = $wpdb->get_row("
        SELECT 
            MIN(CAST(meta_value AS DECIMAL(10,2))) as min_price,
            MAX(CAST(meta_value AS DECIMAL(10,2))) as max_price
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
        WHERE pm.meta_key = '_product_price' 
        AND pm.meta_value > 0
        AND p.post_status = 'publish'
        AND p.post_type = 'product'
    ");

    return array(
        'min' => $results->min_price ?? 0,
        'max' => $results->max_price ?? 10000
    );
}

/**
 * Функция для получения уникальных значений мета-поля
 */
function weyer_get_unique_product_meta($meta_key) {
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
?>