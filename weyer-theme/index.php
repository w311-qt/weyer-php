<?php
/**
 * Главная страница сайта Weyer
 * Адаптация HTML шаблона под WordPress
 */

get_header(); ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="wrap">
                <div>
                    <div class="btn-ghost" style="display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius:999px; border:1px solid rgba(255,255,255,0.3); color:#00D084; opacity:.9">
                        <i class="fa-solid fa-star"></i> <?php echo get_theme_mod('hero_badge_text', 'Работаем с 2003 года'); ?>
                    </div>

                    <h1><?php echo get_theme_mod('hero_title', get_bloginfo('name') . ' - Промышленные решения нового поколения'); ?></h1>

                    <p><?php echo get_theme_mod('hero_description', '15 000+ позиций: кабельные системы, металлорукава и защитное оборудование от ведущих мировых производителей.'); ?></p>

                    <div style="display:flex; gap:12px; margin-top: 16px">
                        <a class="btn btn-accent" href="<?php echo get_post_type_archive_link('product'); ?>">
                            <i class="fa-solid fa-rocket"></i> <?php echo get_theme_mod('hero_btn_catalog', 'Открыть каталог'); ?>
                        </a>
                        <a class="btn btn-ghost" href="#calc" onclick="openModal('callback-modal')">
                            <i class="fa-solid fa-calculator"></i> <?php echo get_theme_mod('hero_btn_calc', 'Рассчитать проект'); ?>
                        </a>
                    </div>

                    <div class="stats">
                        <?php
                        // Получаем статистику
                        $products_count = wp_count_posts('product');
                        $published_products = $products_count->publish ?? 0;

                        $stats = array(
                                array(
                                        'number' => $published_products > 0 ? number_format($published_products, 0, '.', ' ') . '+' : '15K+',
                                        'label' => get_theme_mod('stat_1_label', 'товаров в наличии')
                                ),
                                array(
                                        'number' => get_theme_mod('stat_2_number', '500+'),
                                        'label' => get_theme_mod('stat_2_label', 'крупных клиентов')
                                ),
                                array(
                                        'number' => get_theme_mod('stat_3_number', '24/7'),
                                        'label' => get_theme_mod('stat_3_label', 'техподдержка')
                                )
                        );

                        foreach ($stats as $stat): ?>
                            <div class="stat">
                                <div class="n"><?php echo $stat['number']; ?></div>
                                <div><?php echo $stat['label']; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="floating">
            <div class="float-card float-1">
                <i class="fa-solid fa-certificate"></i>
                <?php echo get_theme_mod('float_1_text', 'ATEX сертификаты'); ?>
            </div>
            <div class="float-card float-2">
                <i class="fa-solid fa-shipping-fast"></i>
                <?php echo get_theme_mod('float_2_text', 'Быстрая доставка'); ?>
            </div>
            <div class="float-card float-3">
                <i class="fa-solid fa-shield"></i>
                <?php echo get_theme_mod('float_3_text', 'Гарантия качества'); ?>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section class="section">
        <div class="container">
            <div class="title">
                <div class="btn-ghost" style="border-radius:999px; padding:8px 12px; border:1px solid var(--g200); display:inline-flex; align-items:center; gap:8px">
                    <i class="fa-solid fa-layer-group"></i> <?php echo get_theme_mod('categories_badge', 'Популярные категории'); ?>
                </div>
                <h2><?php echo get_theme_mod('categories_title', 'Выберите нужную <span style="background:var(--gradient-primary); -webkit-background-clip:text; -webkit-text-fill-color:transparent">категорию</span>'); ?></h2>
                <p><?php echo get_theme_mod('categories_description', 'Широкий ассортимент промышленного оборудования для любых задач.'); ?></p>
            </div>

            <div class="cat-grid-cards">
                <?php
                // Получаем категории товаров
                $categories = get_terms(array(
                        'taxonomy' => 'product_category',
                        'hide_empty' => false,
                        'number' => 4,
                        'orderby' => 'count',
                        'order' => 'DESC'
                ));

                if ($categories && !is_wp_error($categories)) {
                    // Изображения для категорий (можно сделать настраиваемыми через мета-поля)
                    $category_images = array(
                            'металлорукава' => 'https://images.unsplash.com/photo-1581092580497-e0d23cbdf1dc?q=80&w=800&auto=format&fit=crop',
                            'кабельные-вводы' => 'https://images.unsplash.com/photo-1614064641938-3bbee52942c7?q=80&w=800&auto=format&fit=crop',
                            'соединители' => 'https://images.unsplash.com/photo-1557804506-669a67965ba0?q=80&w=800&auto=format&fit=crop',
                            'защитные-системы' => 'https://images.unsplash.com/photo-1591799264318-7e6ef8ddb7ea?q=80&w=800&auto=format&fit=crop'
                    );

                    foreach ($categories as $index => $category) {
                        $category_count = $category->count;
                        $category_link = get_term_link($category);
                        $category_slug = $category->slug;

                        // Получаем изображение категории (из мета-поля или дефолтное)
                        $category_image = get_term_meta($category->term_id, 'category_image', true);
                        if (!$category_image) {
                            $category_image = $category_images[$category_slug] ?? 'https://images.unsplash.com/photo-1581092580497-e0d23cbdf1dc?q=80&w=800&auto=format&fit=crop';
                        }

                        // Генерируем рейтинг (можно сделать реальным через отзывы)
                        $rating = 4.5 + ($index * 0.1);
                        $rating = min($rating, 5.0);

                        // Получаем минимальную цену в категории
                        $min_price = weyer_get_category_min_price($category->term_id);
                        ?>

                        <article class="card">
                            <div class="img">
                                <img src="<?php echo esc_url($category_image); ?>" alt="<?php echo esc_attr($category->name); ?>" loading="lazy"/>
                            </div>
                            <div class="body">
                                <div class="meta">
                                    <span><?php echo $category_count; ?> товар<?php echo weyer_plural_form($category_count, 'ов', '', 'а'); ?></span>
                                    <span class="rating">
                                    <i class="fa-solid fa-star"></i> <?php echo number_format($rating, 1); ?>
                                </span>
                                </div>
                                <h3><?php echo esc_html($category->name); ?></h3>
                                <p style="color:var(--g600); margin: 6px 0 10px">
                                    <?php echo $category->description ? esc_html($category->description) : 'Качественное оборудование для промышленных нужд.'; ?>
                                </p>
                                <div class="foot">
                                    <div>
                                        <small style="color:var(--g500)">от</small>
                                        <strong><?php echo $min_price ? number_format($min_price, 0, '.', ' ') . ' ₽' : 'Уточнить'; ?></strong>
                                    </div>
                                    <a class="btn btn-primary" href="<?php echo esc_url($category_link); ?>">
                                        Перейти <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                        <?php
                    }
                } else {
                    // Fallback категории если их нет в базе
                    $fallback_categories = array(
                            array(
                                    'name' => 'Металлорукава',
                                    'count' => 1286,
                                    'rating' => 4.8,
                                    'price' => 850,
                                    'image' => 'https://images.unsplash.com/photo-1581092580497-e0d23cbdf1dc?q=80&w=800&auto=format&fit=crop',
                                    'description' => 'Гибкие рукава для защиты кабелей в тяжёлых условиях.',
                                    'link' => get_post_type_archive_link('product')
                            ),
                            array(
                                    'name' => 'Кабельные вводы',
                                    'count' => 2104,
                                    'rating' => 4.9,
                                    'price' => 1250,
                                    'image' => 'https://images.unsplash.com/photo-1614064641938-3bbee52942c7?q=80&w=800&auto=format&fit=crop',
                                    'description' => 'Ex/EMC, герметичные решения для экстремальных условий.',
                                    'link' => get_post_type_archive_link('product')
                            ),
                            array(
                                    'name' => 'Соединители',
                                    'count' => 856,
                                    'rating' => 4.7,
                                    'price' => 320,
                                    'image' => 'https://images.unsplash.com/photo-1557804506-669a67965ba0?q=80&w=800&auto=format&fit=crop',
                                    'description' => 'Высокопрочные соединители для надёжного монтажа.',
                                    'link' => get_post_type_archive_link('product')
                            ),
                            array(
                                    'name' => 'Защитные системы',
                                    'count' => 643,
                                    'rating' => 4.6,
                                    'price' => 2850,
                                    'image' => 'https://images.unsplash.com/photo-1591799264318-7e6ef8ddb7ea?q=80&w=800&auto=format&fit=crop',
                                    'description' => 'Комплексные решения защиты электрооборудования.',
                                    'link' => get_post_type_archive_link('product')
                            )
                    );

                    foreach ($fallback_categories as $category): ?>
                        <article class="card">
                            <div class="img">
                                <img src="<?php echo esc_url($category['image']); ?>" alt="<?php echo esc_attr($category['name']); ?>" loading="lazy"/>
                            </div>
                            <div class="body">
                                <div class="meta">
                                    <span><?php echo $category['count']; ?> товаров</span>
                                    <span class="rating">
                                    <i class="fa-solid fa-star"></i> <?php echo $category['rating']; ?>
                                </span>
                                </div>
                                <h3><?php echo esc_html($category['name']); ?></h3>
                                <p style="color:var(--g600); margin: 6px 0 10px"><?php echo esc_html($category['description']); ?></p>
                                <div class="foot">
                                    <div>
                                        <small style="color:var(--g500)">от</small>
                                        <strong><?php echo number_format($category['price'], 0, '.', ' '); ?> ₽</strong>
                                    </div>
                                    <a class="btn btn-primary" href="<?php echo esc_url($category['link']); ?>">
                                        Перейти <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach;
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Products -->
    <section class="section products" id="popular">
        <div class="container">
            <div class="bar">
                <div>
                    <div class="btn-ghost" style="border-radius:999px; padding:8px 12px; border:1px solid var(--g200); display:inline-flex; align-items:center; gap:8px">
                        <i class="fa-solid fa-fire"></i> <?php echo get_theme_mod('products_badge', 'Хиты продаж'); ?>
                    </div>
                    <h2 style="margin:8px 0 0"><?php echo get_theme_mod('products_title', 'Популярные <span style="background:var(--gradient-primary); -webkit-background-clip:text; -webkit-text-fill-color:transparent">товары</span>'); ?></h2>
                </div>
                <div class="tabs" role="tablist">
                    <button class="tab active" data-filter="all" role="tab">Все</button>
                    <button class="tab" data-filter="new" role="tab">Новинки</button>
                    <button class="tab" data-filter="sale" role="tab">Скидки</button>
                    <button class="tab" data-filter="hit" role="tab">Хиты</button>
                </div>
            </div>

            <div class="grid" id="gridProducts">
                <?php
                // Получаем популярные товары
                $products_query = new WP_Query(array(
                        'post_type' => 'product',
                        'posts_per_page' => 6,
                        'meta_query' => array(
                                array(
                                        'key' => '_product_in_stock',
                                        'value' => 1,
                                        'compare' => '='
                                )
                        ),
                        'orderby' => 'date',
                        'order' => 'DESC'
                ));

                if ($products_query->have_posts()) {
                    while ($products_query->have_posts()) {
                        $products_query->the_post();
                        weyer_render_product_card(get_the_ID());
                    }
                    wp_reset_postdata();
                } else {
                    // Fallback продукты если их нет в базе
                    weyer_render_fallback_products();
                }
                ?>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="section" id="calc">
        <div class="container">
            <div style="text-align:center; background: var(--gradient-primary); color:#fff; padding: 40px; border-radius: 28px; position:relative; overflow:hidden">
                <h3 style="font-size: clamp(22px, 3vw, 30px); margin: 0 0 6px"><?php echo get_theme_mod('cta_title', 'Нужна подборка под проект?'); ?></h3>
                <p style="opacity:.92; margin: 0 0 18px"><?php echo get_theme_mod('cta_subtitle', 'Получить консультацию специалиста'); ?></p>
                <div style="display:flex; gap:10px; justify-content:center; flex-wrap: wrap">
                    <button class="btn" style="background:#fff; color: var(--primary)" onclick="openModal('callback-modal')">
                        <i class="fa-solid fa-file-arrow-up"></i><?php echo get_theme_mod('cta_btn_1', 'Свяжитесь с нами'); ?>
                    </button>
                    <button class="btn btn-ghost" style="color:#fff; border-color: rgba(255,255,255,0.3)" onclick="openModal('callback-modal')">
                        <i class="fa-solid fa-message"></i> <?php echo get_theme_mod('cta_btn_2', 'Консультация инженера'); ?>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Дополнительные стили для главной страницы -->
    <style>
        /* Hero Section */
        .hero {
            position: relative;
            background: var(--gradient-dark);
            color: #fff;
            overflow: hidden;
            min-height: 70vh;
            display: flex;
            align-items: center;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image:
                    radial-gradient(circle at 20% 50%, rgba(0, 82, 255, 0.2) 0%, transparent 50%),
                    radial-gradient(circle at 80% 20%, rgba(255, 75, 0, 0.15) 0%, transparent 50%),
                    radial-gradient(circle at 40% 80%, rgba(0, 208, 132, 0.1) 0%, transparent 50%);
            z-index: 1;
        }

        .hero .wrap {
            padding: 80px 0;
            display: grid;
            grid-template-columns: 1fr;
            gap: 40px;
            align-items: center;
            position: relative;
            z-index: 2;
            max-width: 800px;
        }

        .hero h1 {
            font-size: clamp(2.5rem, 5vw, 4rem);
            line-height: 1.1;
            margin: 16px 0;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            font-size: 18px;
            opacity: 0.9;
            margin: 0 0 24px;
            line-height: 1.7;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-top: 40px;
            padding: 32px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: var(--r-xl);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .stat {
            text-align: center;
        }

        .stat .n {
            font-weight: 900;
            font-size: 2.5rem;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #ffffff 0%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .floating {
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: 1;
        }

        .float-card {
            position: absolute;
            padding: 12px 16px;
            border-radius: 14px;
            background: var(--gradient-glass);
            border: 1px solid rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            color: #fff;
            animation: float 6s ease-in-out infinite;
            font-size: 14px;
            font-weight: 600;
        }

        .float-card i {
            margin-right: 8px;
        }

        .float-1 {
            top: 22%;
            right: 10%;
        }

        .float-2 {
            bottom: 18%;
            right: 18%;
            animation-delay: 1.5s;
        }

        .float-3 {
            top: 38%;
            right: 3%;
            animation-delay: 3s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-14px); }
        }

        /* Section */
        .section {
            padding: 80px 0;
        }

        .section .title {
            text-align: center;
            margin-bottom: 48px;
        }

        .section .title h2 {
            font-size: clamp(2rem, 4vw, 3rem);
            margin: 16px 0;
            font-weight: 800;
            color: var(--secondary);
        }

        .section .title p {
            color: var(--g600);
            font-size: 18px;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Category Cards */
        .cat-grid-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }

        .card {
            background: #fff;
            border-radius: 20px;
            border: 2px solid var(--g100);
            overflow: hidden;
            transition: transform var(--t-base), box-shadow var(--t-base), border-color var(--t-base);
            position: relative;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
            border-color: var(--primary);
        }

        .card .img {
            height: 200px;
            background: var(--g100);
            display: grid;
            place-items: center;
            overflow: hidden;
        }

        .card .img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform var(--t-slow);
        }

        .card:hover .img img {
            transform: scale(1.1);
        }

        .card .body {
            padding: 20px;
        }

        .card .meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            color: var(--g500);
            margin-bottom: 12px;
        }

        .card .rating {
            color: var(--warning);
            font-weight: 700;
        }

        .card h3 {
            margin: 0 0 12px;
            font-size: 20px;
            font-weight: 700;
            color: var(--secondary);
        }

        .card .foot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 16px;
        }

        /* Products */
        .products {
            background: var(--g50);
        }

        .products .bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 24px;
            margin-bottom: 32px;
        }

        .tabs {
            display: flex;
            gap: 8px;
            background: #fff;
            padding: 6px;
            border-radius: 14px;
            border: 1px solid var(--g200);
        }

        .tab {
            border: 0;
            background: transparent;
            padding: 10px 16px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 700;
            color: var(--g600);
            transition: all var(--t-base);
        }

        .tab.active {
            background: var(--gradient-primary);
            color: #fff;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stats {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .products .bar {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .tabs {
                align-self: stretch;
            }

            .floating {
                display: none;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Табы для товаров
            const tabs = document.querySelectorAll('.tab');
            const grid = document.getElementById('gridProducts');

            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Убираем активный класс со всех табов
                    tabs.forEach(t => t.classList.remove('active'));

                    // Добавляем активный класс к нажатому табу
                    this.classList.add('active');

                    // Получаем фильтр
                    const filter = this.dataset.filter;

                    // Фильтруем товары
                    filterProducts(filter);
                });
            });

            function filterProducts(filter) {
                const products = grid.querySelectorAll('.p');

                products.forEach(product => {
                    const productClasses = product.className;

                    if (filter === 'all') {
                        product.style.display = 'block';
                    } else if (filter === 'new' && productClasses.includes('new')) {
                        product.style.display = 'block';
                    } else if (filter === 'sale' && productClasses.includes('sale')) {
                        product.style.display = 'block';
                    } else if (filter === 'hit' && productClasses.includes('hit')) {
                        product.style.display = 'block';
                    } else {
                        product.style.display = 'none';
                    }
                });
            }
        });
    </script>

<?php get_footer(); ?>

<?php
/**
 * Функция для получения минимальной цены в категории
 */
function weyer_get_category_min_price($category_id) {
    global $wpdb;

    $min_price = $wpdb->get_var($wpdb->prepare("
        SELECT MIN(CAST(pm.meta_value AS DECIMAL(10,2))) 
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
        INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
        INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        WHERE pm.meta_key = '_product_price' 
        AND p.post_status = 'publish' 
        AND p.post_type = 'product'
        AND tt.term_id = %d
        AND pm.meta_value > 0
    ", $category_id));

    return $min_price;
}

/**
 * Функция для правильного склонения слов
 */
function weyer_plural_form($number, $form1, $form2, $form5) {
    $number = abs($number) % 100;
    $lnum = $number % 10;

    if ($number >= 10 && $number <= 20) return $form5;
    if ($lnum > 1 && $lnum < 5) return $form2;
    if ($lnum == 1) return $form1;

    return $form5;
}

/**
 * Функция для рендеринга карточки товара
 */
function weyer_render_product_card($product_id) {
    $sku = get_product_sku($product_id);
    $price = get_product_price($product_id);
    $old_price = get_product_old_price($product_id);
    $material = get_post_meta($product_id, '_product_material', true);
    $diameter = get_post_meta($product_id, '_product_diameter', true);
    $is_hit = is_product_hit($product_id);
    $product_image = get_the_post_thumbnail_url($product_id, 'product-thumb');

    if (!$product_image) {
        $product_image = 'https://images.unsplash.com/photo-1563865436916-7f27eca7e2b8?q=80&w=900&auto=format&fit=crop';
    }

    // Определяем классы и бейджи товара
    $classes = array('p');
    $badges = array();

    if ($old_price && $old_price > $price) {
        $classes[] = 'sale';
        $discount = round((($old_price - $price) / $old_price) * 100);
        $badges[] = array('text' => "-{$discount}%", 'class' => 'sale');
    }

    if ($is_hit) {
        $classes[] = 'hit';
        $badges[] = array('text' => 'ХИТ', 'class' => 'hit');
    }

    // Проверяем новинку (товар добавлен за последние 30 дней)
    $post_date = get_the_date('U', $product_id);
    if ((time() - $post_date) < (30 * 24 * 60 * 60)) {
        $classes[] = 'new';
        $badges[] = array('text' => 'НОВИНКА', 'class' => 'new');
    }
    ?>

    <article class="<?php echo implode(' ', $classes); ?>" data-id="<?php echo $product_id; ?>" data-cat="products">
        <div class="badges">
            <?php foreach ($badges as $badge): ?>
                <span class="b <?php echo $badge['class']; ?>"><?php echo $badge['text']; ?></span>
            <?php endforeach; ?>
        </div>

        <div class="fav" data-act="fav" title="Добавить в избранное">
            <i class="fa-solid fa-heart"></i>
        </div>

        <div class="img">
            <img src="<?php echo esc_url($product_image); ?>" alt="<?php echo esc_attr(get_the_title($product_id)); ?>" loading="lazy"/>
        </div>

        <div class="body">
            <div class="sku"><?php echo esc_html($sku); ?></div>
            <h3 style="margin:6px 0">
                <a href="<?php echo get_permalink($product_id); ?>"><?php echo get_the_title($product_id); ?></a>
            </h3>

            <?php if ($material): ?>
                <div class="spec"><span>Материал</span><strong><?php echo esc_html($material); ?></strong></div>
            <?php endif; ?>

            <?php if ($diameter): ?>
                <div class="spec"><span>Диаметр</span><strong><?php echo esc_html($diameter); ?></strong></div>
            <?php endif; ?>

            <div class="foot">
                <div class="price">
                    <span class="now"><?php echo number_format($price, 0, '.', ' '); ?> ₽</span>
                    <?php if ($old_price && $old_price > $price): ?>
                        <span class="was"><?php echo number_format($old_price, 0, '.', ' '); ?> ₽</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="quick">
            <div class="a" data-act="quick">
                <i class="fa-solid fa-eye"></i>
            </div>
            <div class="a" data-act="compare">
                <i class="fa-solid fa-balance-scale"></i>
            </div>
        </div>
    </article>
    <?php
}

/**
 * Функция для рендеринга fallback товаров
 */
function weyer_render_fallback_products() {
    $fallback_products = array(
            array(
                    'id' => 1,
                    'sku' => 'MR-12-SS',
                    'title' => 'Металлорукав нерж. 12 мм',
                    'image' => 'https://images.unsplash.com/photo-1563865436916-7f27eca7e2b8?q=80&w=900&auto=format&fit=crop',
                    'price' => 850,
                    'old_price' => 980,
                    'material' => 'AISI 304',
                    'diameter' => '12 мм',
                    'tags' => array('hit', 'sale'),
                    'link' => '#'
            ),
            array(
                    'id' => 2,
                    'sku' => 'MR-20-SS',
                    'title' => 'Металлорукав нерж. 20 мм',
                    'image' => 'https://images.unsplash.com/photo-1554213455-7f5b38b8bdfc?q=80&w=900&auto=format&fit=crop',
                    'price' => 1190,
                    'old_price' => null,
                    'material' => 'AISI 304',
                    'diameter' => '20 мм',
                    'tags' => array('hit'),
                    'link' => '#'
            ),
            array(
                    'id' => 3,
                    'sku' => 'CG-M20-EX',
                    'title' => 'Кабельный ввод Ex M20',
                    'image' => 'https://images.unsplash.com/photo-1614064641938-3bbee52942c7?q=80&w=1000&auto=format&fit=crop',
                    'price' => 1790,
                    'old_price' => 1990,
                    'material' => 'Никел. латунь',
                    'diameter' => 'M20',
                    'tags' => array('sale'),
                    'link' => '#'
            ),
            array(
                    'id' => 4,
                    'sku' => 'CG-M32-EMC',
                    'title' => 'Кабельный ввод EMC M32',
                    'image' => 'https://images.unsplash.com/photo-1602526212034-7ab1b3f6b9a7?q=80&w=1000&auto=format&fit=crop',
                    'price' => 2890,
                    'old_price' => null,
                    'material' => 'Нерж. сталь',
                    'diameter' => 'M32',
                    'tags' => array('new'),
                    'link' => '#'
            ),
            array(
                    'id' => 5,
                    'sku' => 'CON-QC-3P',
                    'title' => 'Соединитель быстросъёмный 3P',
                    'image' => 'https://images.unsplash.com/photo-1557804506-669a67965ba0?q=80&w=1000&auto=format&fit=crop',
                    'price' => 1290,
                    'old_price' => 1490,
                    'material' => 'Полиамид',
                    'diameter' => '—',
                    'tags' => array('hit', 'sale'),
                    'link' => '#'
            ),
            array(
                    'id' => 6,
                    'sku' => 'PRO-BOX-54',
                    'title' => 'Короб защитный IP54',
                    'image' => 'https://images.unsplash.com/photo-1591799264318-7e6ef8ddb7ea?q=80&w=1000&auto=format&fit=crop',
                    'price' => 2450,
                    'old_price' => null,
                    'material' => 'ABS',
                    'diameter' => '—',
                    'tags' => array('new'),
                    'link' => '#'
            )
    );

    foreach ($fallback_products as $product) {
        $classes = array('p');
        $badges = array();

        // Обработка тегов
        foreach ($product['tags'] as $tag) {
            $classes[] = $tag;

            switch ($tag) {
                case 'hit':
                    $badges[] = array('text' => 'ХИТ', 'class' => 'hit');
                    break;
                case 'new':
                    $badges[] = array('text' => 'НОВИНКА', 'class' => 'new');
                    break;
                case 'sale':
                    if ($product['old_price']) {
                        $discount = round((($product['old_price'] - $product['price']) / $product['old_price']) * 100);
                        $badges[] = array('text' => "-{$discount}%", 'class' => 'sale');
                    }
                    break;
            }
        }
        ?>

        <article class="<?php echo implode(' ', $classes); ?>" data-id="<?php echo $product['id']; ?>" data-cat="products">
            <div class="badges">
                <?php foreach ($badges as $badge): ?>
                    <span class="b <?php echo $badge['class']; ?>"><?php echo $badge['text']; ?></span>
                <?php endforeach; ?>
            </div>

            <div class="fav" data-act="fav" title="Добавить в избранное">
                <i class="fa-solid fa-heart"></i>
            </div>

            <div class="img">
                <img src="<?php echo esc_url($product['image']); ?>" alt="<?php echo esc_attr($product['title']); ?>" loading="lazy"/>
            </div>

            <div class="body">
                <div class="sku"><?php echo esc_html($product['sku']); ?></div>
                <h3 style="margin:6px 0"><?php echo esc_html($product['title']); ?></h3>

                <div class="spec"><span>Материал</span><strong><?php echo esc_html($product['material']); ?></strong></div>
                <div class="spec"><span>Диаметр</span><strong><?php echo esc_html($product['diameter']); ?></strong></div>

                <div class="foot">
                    <div class="price">
                        <span class="now"><?php echo number_format($product['price'], 0, '.', ' '); ?> ₽</span>
                        <?php if ($product['old_price']): ?>
                            <span class="was"><?php echo number_format($product['old_price'], 0, '.', ' '); ?> ₽</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="quick">
                <div class="a" data-act="quick">
                    <i class="fa-solid fa-eye"></i>
                </div>
                <div class="a" data-act="compare">
                    <i class="fa-solid fa-balance-scale"></i>
                </div>
            </div>
        </article>
        <?php
    }
}