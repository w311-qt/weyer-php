<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0052FF">

    <?php wp_head(); ?>

    <!-- Дополнительные стили из оригинального шаблона -->
    <style>
        /* ---------- MODERN VARIABLES & RESET ---------- */
        :root {
            /* Brand */
            --primary: #0052FF;
            --primary-dark: #0041CC;
            --primary-25: rgba(0, 82, 255, .25);
            --secondary: #1A1D29;
            --accent: #FF4B00;
            --success: #00D084;
            --warning: #FFB800;
            --error: #FF3B30;

            /* Neutrals */
            --white: #fff;
            --black: #0F172A;
            --g50: #F8FAFC; --g100:#F1F5F9; --g200:#E2E8F0; --g300:#CBD5E1; --g400:#94A3B8; --g500:#64748B; --g600:#475569; --g700:#334155; --g800:#1E293B; --g900:#0F172A;

            /* Gradients */
            --gradient-primary: linear-gradient(135deg, #0052FF 0%, #3B82F6 100%);
            --gradient-accent: linear-gradient(135deg, #FF4B00 0%, #FF6B35 100%);
            --gradient-dark: linear-gradient(135deg, #0F172A 0%, #1F2937 100%);
            --gradient-glass: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.06) 100%);

            /* Shadows */
            --shadow-xs: 0 1px 2px rgba(0,0,0,.05);
            --shadow-md: 0 4px 20px rgba(16,24,40,.08);
            --shadow-xl: 0 20px 50px rgba(2, 6, 23, .25);
            --shadow-colored: 0 14px 40px rgba(0, 82, 255, .25);

            /* Radius */
            --r-md: 12px; --r-lg: 16px; --r-xl: 24px; --r-2xl: 28px; --r-full: 999px;

            /* Spacing */
            --s-2: 8px; --s-3: 12px; --s-4: 16px; --s-5: 20px; --s-6: 24px; --s-8: 32px; --s-10: 40px; --s-12: 48px; --s-16: 64px; --s-20: 80px; --s-24: 96px;

            /* Transitions */
            --t-fast: .15s ease-out; --t-base:.25s ease-out; --t-slow:.45s ease;

            /* Blur */
            --blur: blur(10px);
        }

        *, *::before, *::after { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, sans-serif; margin: 0; color: var(--g800); background: var(--g50); }

        .container { width: 100%; max-width: 1280px; margin: 0 auto; padding: 0 var(--s-6); }

        .btn { display: inline-flex; align-items: center; gap: var(--s-3); padding: 12px 18px; border-radius: var(--r-lg); border: 0; cursor: pointer; font-weight: 700; transition: transform var(--t-base), box-shadow var(--t-base), background var(--t-base); }
        .btn i { font-size: 14px }
        .btn-primary { background: var(--gradient-primary); color: #fff; box-shadow: var(--shadow-colored); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: var(--shadow-xl), var(--shadow-colored); }
        .btn-ghost { background: #fff; color: var(--g700); border: 1px solid var(--g200); }
        .btn-ghost:hover { background: var(--g100); }
        .btn-accent { background: var(--gradient-accent); color:#fff }

        /* Header */
        .topbar { background: var(--gradient-dark); color:#fff; font-size: 12px; }
        .topbar .wrap { display:flex; justify-content: space-between; align-items: center; gap: var(--s-6); padding: 8px 0; }
        .topbar .info { display:flex; gap: var(--s-6); opacity:.95 }

        header { position: sticky; top: 0; z-index: 40; backdrop-filter: saturate(140%) blur(8px); background: rgba(255,255,255,.85); border-bottom: 1px solid var(--g200); }
        .head { padding: 14px 0; }
        .head-grid { display: grid; grid-template-columns: auto 1fr auto; align-items: center; gap: var(--s-6); }

        .logo { display:flex; align-items: center; gap: 10px; text-decoration: none; }
        .logo-mark { width: 44px; height: 44px; border-radius: 12px; display:grid; place-items:center; font-weight: 800; color:#fff; background: var(--gradient-primary); box-shadow: var(--shadow-colored); }
        .logo-text { display:flex; flex-direction: column; line-height: 1; }
        .logo-title { font-weight: 900; letter-spacing: .2px; color: var(--secondary) }
        .logo-sub { font-size: 11px; color: var(--g500); text-transform: uppercase; letter-spacing:.4px }

        .search { position: relative; max-width: 640px; }
        .search input { width: 100%; border: 2px solid var(--g200); border-radius: var(--r-full); padding: 14px 48px 14px 44px; background:#fff; transition: border var(--t-base), box-shadow var(--t-base); font-size: 15px; }
        .search input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 6px var(--primary-25); }
        .search .icon { position:absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--g400) }
        .search .go { position:absolute; right: 6px; top: 50%; transform: translateY(-50%); width: 44px; height:44px; border-radius: 50%; border: 0; display:grid; place-items:center; color:#fff; background: var(--gradient-primary) }
        .suggest { position:absolute; left: 0; right: 0; top: calc(100% + 8px); background:#fff; border: 1px solid var(--g200); border-radius: 14px; box-shadow: var(--shadow-md); display:none; overflow: hidden; }
        .suggest-item { padding: 10px 12px; display:flex; align-items: center; gap: 10px; cursor: pointer; }
        .suggest-item:hover { background: var(--g100) }

        .h-actions { display:flex; align-items:center; gap: 12px; }
        .ha { position: relative; display:flex; align-items: center; gap: 8px; text-decoration: none; color: var(--g700); padding: 8px 10px; border-radius: 12px; }
        .ha:hover { background: var(--g100) }
        .ha .badge { position:absolute; top: -4px; right: -4px; background: var(--accent); color:#fff; font-size: 10px; font-weight: 800; border-radius: 999px; min-width: 18px; padding: 2px 6px; text-align:center; }

        /* Nav */
        .nav { background:#fff; border-top: 1px solid var(--g200); border-bottom: 1px solid var(--g200); }
        .nav .row { display:flex; align-items:center; gap: 16px; padding: 10px 0; }
        .catalog-btn { display:flex; align-items:center; gap: 10px; font-weight: 800; color:#fff; text-decoration:none; background: var(--gradient-primary); padding: 10px 16px; border-radius: 14px; box-shadow: var(--shadow-colored); }
        .nav-links { display:flex; gap: 22px; flex-wrap: wrap; }
        .nav-links a { color: var(--g700); text-decoration:none; font-weight: 600; position:relative }
        .nav-links a::after { content:''; position:absolute; left: 0; bottom: -8px; width: 0; height: 2px; background: var(--primary); transition: width var(--t-base); }
        .nav-links a:hover::after { width: 100% }

        /* Mobile */
        .mobilebar { display:none }
        @media (max-width: 860px) {
            .head-grid { grid-template-columns: auto 1fr auto }
            .mobilebar { position: fixed; left: 0; right: 0; bottom: 0; background:#fff; border-top: 1px solid var(--g200); display:flex; justify-content: space-around; padding: 8px; z-index: 55 }
            .h-actions { display: none; }
            .nav-links { display: none; }
        }
    </style>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Topbar -->
<div class="topbar">
    <div class="container">
        <div class="wrap">
            <div class="info">
                <span><i class="fa-solid fa-truck"></i> <?php echo get_theme_mod('topbar_delivery', 'Доставка по РФ и СНГ'); ?></span>
                <span><i class="fa-solid fa-shield"></i> <?php echo get_theme_mod('topbar_guarantee', 'Гарантия качества'); ?></span>
                <span><i class="fa-solid fa-headset"></i> <?php echo get_theme_mod('topbar_support', 'Техподдержка 24/7'); ?></span>
            </div>
            <div class="info">
                <span><i class="fa-solid fa-location-dot"></i> <?php echo get_theme_mod('company_city', 'Санкт-Петербург'); ?></span>
                <span><i class="fa-solid fa-phone"></i>
                    <a href="tel:<?php echo str_replace(array(' ', '(', ')', '-'), '', get_theme_mod('company_phone', '+7 (495) 123-45-67')); ?>" style="color: inherit;">
                        <?php echo get_theme_mod('company_phone', '+7 (495) 123-45-67'); ?>
                    </a>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Header -->
<header>
    <div class="head">
        <div class="container">
            <div class="head-grid">
                <a class="logo" href="<?php echo home_url(); ?>">
                    <div class="logo-mark">W</div>
                    <div class="logo-text">
                        <span class="logo-title"><?php echo get_theme_mod('company_name', 'WEYER'); ?></span>
                        <span class="logo-sub"><?php echo get_theme_mod('company_tagline', 'Industrial Systems'); ?></span>
                    </div>
                </a>

                <div class="search" id="searchBox">
                    <i class="fa-solid fa-magnifying-glass icon"></i>
                    <form role="search" method="get" action="<?php echo home_url('/'); ?>">
                        <input id="q" type="text" name="s" value="<?php echo get_search_query(); ?>" placeholder="<?php echo get_theme_mod('search_placeholder', 'Поиск по 15 000+ товарам…'); ?>" autocomplete="off" />
                        <input type="hidden" name="post_type" value="product" />
                    </form>
                    <button class="go" type="submit" aria-label="Найти" onclick="document.querySelector('.search form').submit();">
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                    <div class="suggest" id="suggest"></div>
                </div>

                <div class="h-actions">
                    <a class="ha" href="<?php echo home_url('/compare/'); ?>" id="btnCompare">
                        <i class="fa-solid fa-balance-scale"></i>
                        <span>Сравнить</span>
                        <span class="badge" id="badgeCompare" style="display: none;">0</span>
                    </a>
                    <a class="ha" href="<?php echo home_url('/favorites/'); ?>" id="btnFav">
                        <i class="fa-solid fa-heart"></i>
                        <span>Избранное</span>
                        <span class="badge" id="badgeFav" style="display: none;">0</span>
                    </a>
                    <?php if (is_user_logged_in()): ?>
                        <a class="ha" href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>">
                            <i class="fa-solid fa-user"></i>
                            <span>Профиль</span>
                        </a>
                    <?php else: ?>
                        <a class="ha" href="<?php echo wp_login_url(); ?>">
                            <i class="fa-solid fa-user"></i>
                            <span>Войти</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Nav -->
    <div class="nav">
        <div class="container">
            <div class="row">
                <a href="<?php echo get_post_type_archive_link('product'); ?>" class="catalog-btn" id="openCatalog">
                    <i class="fa-solid fa-bars"></i> Каталог товаров
                </a>

                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container' => 'nav',
                    'container_class' => 'nav-links',
                    'menu_class' => '',
                    'depth' => 1,
                    'fallback_cb' => 'weyer_fallback_menu',
                ));
                ?>
            </div>
        </div>
    </div>
</header>

<!-- Mobile bottom bar -->
<nav class="mobilebar">
    <a class="ha" href="<?php echo get_post_type_archive_link('product'); ?>" id="mbCatalog">
        <i class="fa-solid fa-bars"></i>Каталог
    </a>
    <a class="ha" href="<?php echo home_url('/?s=&post_type=product'); ?>">
        <i class="fa-solid fa-magnifying-glass"></i>Поиск
    </a>
    <a class="ha" href="<?php echo home_url('/compare/'); ?>">
        <i class="fa-solid fa-balance-scale"></i>Сравнение
    </a>
    <a class="ha" href="<?php echo is_user_logged_in() ? get_permalink(get_option('woocommerce_myaccount_page_id')) : wp_login_url(); ?>">
        <i class="fa-solid fa-user"></i>Профиль
    </a>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Поиск с подсказками
        const searchInput = document.getElementById('q');
        const suggest = document.getElementById('suggest');

        if (searchInput) {
            let searchTimeout;

            searchInput.addEventListener('input', function(e) {
                const query = e.target.value.trim();

                clearTimeout(searchTimeout);

                if (query.length < 2) {
                    suggest.style.display = 'none';
                    return;
                }

                searchTimeout = setTimeout(() => {
                    fetchSuggestions(query);
                }, 300);
            });

            // Закрытие подсказок при клике вне
            document.addEventListener('click', function(e) {
                if (!document.getElementById('searchBox').contains(e.target)) {
                    suggest.style.display = 'none';
                }
            });
        }

        function fetchSuggestions(query) {
            fetch(`<?php echo admin_url('admin-ajax.php'); ?>?action=search_suggestions&query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.length > 0) {
                        showSuggestions(data.data);
                    } else {
                        suggest.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Search suggestions error:', error);
                });
        }

        function showSuggestions(suggestions) {
            suggest.innerHTML = suggestions.map(item =>
                `<div class="suggest-item" onclick="selectSuggestion('${item.title}')">
                <i class="fa-solid fa-magnifying-glass"></i>
                ${item.title}
            </div>`
            ).join('');
            suggest.style.display = 'block';
        }

        window.selectSuggestion = function(title) {
            searchInput.value = title;
            suggest.style.display = 'none';
            document.querySelector('.search form').submit();
        }

        // Обновление счетчиков сравнения и избранного
        function updateCounters() {
            const compare = JSON.parse(localStorage.getItem('weyer_compare') || '[]');
            const favorites = JSON.parse(localStorage.getItem('weyer_favorites') || '[]');

            const compareCounter = document.getElementById('badgeCompare');
            const favCounter = document.getElementById('badgeFav');

            if (compareCounter) {
                compareCounter.textContent = compare.length;
                compareCounter.style.display = compare.length > 0 ? 'block' : 'none';
            }

            if (favCounter) {
                favCounter.textContent = favorites.length;
                favCounter.style.display = favorites.length > 0 ? 'block' : 'none';
            }
        }

        // Обновляем счетчики при загрузке и изменениях localStorage
        updateCounters();

        window.addEventListener('storage', updateCounters);

        // Периодическое обновление счетчиков (на случай изменений в том же окне)
        setInterval(updateCounters, 1000);
    });
</script>

<?php
/**
 * Fallback меню если основное меню не установлено
 */
function weyer_fallback_menu() {
    echo '<nav class="nav-links">';

    // Получаем категории товаров для меню
    $categories = get_terms(array(
        'taxonomy' => 'product_category',
        'hide_empty' => false,
        'number' => 5,
    ));

    if ($categories && !is_wp_error($categories)) {
        foreach ($categories as $category) {
            echo '<a href="' . get_term_link($category) . '">' . esc_html($category->name) . '</a>';
        }
    }

    // Добавляем стандартные страницы
    $pages = array(
        'О компании' => 'about',
        'Контакты' => 'contacts',
        'Услуги' => 'services'
    );

    foreach ($pages as $title => $slug) {
        $page = get_page_by_path($slug);
        if ($page) {
            echo '<a href="' . get_permalink($page) . '">' . $title . '</a>';
        }
    }

    echo '</nav>';
}

/**
 * AJAX обработчик для поисковых подсказок
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
?>