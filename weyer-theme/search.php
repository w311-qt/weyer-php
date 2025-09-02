<?php get_header(); ?>

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

                        <!-- Кнопки фильтров -->
                        <div class="filter-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="in-stock-only">
                                <span>Только товары в наличии</span>
                            </label>
                        </div>

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
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Пагинация -->
                    <?php if ($wp_query->max_num_pages > 1): ?>
                        <div class="catalog-pagination">
                            <?php
                            echo paginate_links(array(
                                'total' => $wp_query->max_num_pages,
                                'current' => max(1, get_query_var('paged')),
                                'format' => '?paged=%#%',
                                'prev_text' => '← Назад',
                                'next_text' => 'Далее →',
                            ));
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <style>
        /* ВСТРОЕННЫЕ СТИЛИ ДЛЯ КАТАЛОГА */
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

        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-header h1 {
            font-size: clamp(2rem, 4vw, 3rem);
            margin-bottom: 16px;
            color: var(--secondary);
        }

        .catalog-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 32px;
            align-items: start;
        }

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
        .filter-group select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--g300);
            border-radius: var(--r-md);
            font-size: 14px;
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

        .catalog-grid.list-view .p {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .catalog-grid.list-view .p .img {
            width: 200px;
            height: 150px;
            flex-shrink: 0;
        }

        .catalog-grid.list-view .p .body {
            flex: 1;
        }

        .no-products {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 20px;
            color: var(--g600);
        }

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

<?php get_footer(); ?>