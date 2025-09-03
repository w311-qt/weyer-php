<?php
/**
 * Демонстрационный сайт Weyer - полная версия
 * Запуск: в Docker контейнере на порту 8090
 */

// Демо данные товаров
$products = [
    [
        'id' => 1,
        'title' => 'Металлорукав нержавеющий 12мм',
        'sku' => 'MR-12-SS',
        'price' => 850,
        'old_price' => 980,
        'material' => 'AISI 304',
        'diameter' => '12 мм',
        'image' => 'https://images.unsplash.com/photo-1563865436916-7f27eca7e2b8?q=80&w=400',
        'badges' => ['hit', 'sale']
    ],
    [
        'id' => 2,
        'title' => 'Кабельный ввод Ex M20',
        'sku' => 'CG-M20-EX',
        'price' => 1790,
        'old_price' => 1990,
        'material' => 'Никелированная латунь',
        'diameter' => 'M20',
        'image' => 'https://images.unsplash.com/photo-1614064641938-3bbee52942c7?q=80&w=400',
        'badges' => ['sale']
    ],
    [
        'id' => 3,
        'title' => 'Соединитель быстросъёмный 3P',
        'sku' => 'CON-QC-3P',
        'price' => 1290,
        'old_price' => 1490,
        'material' => 'Полиамид PA66',
        'diameter' => '—',
        'image' => 'https://images.unsplash.com/photo-1557804506-669a67965ba0?q=80&w=400',
        'badges' => ['hit', 'sale']
    ],
    [
        'id' => 4,
        'title' => 'Короб защитный IP54',
        'sku' => 'PRO-BOX-54',
        'price' => 2450,
        'material' => 'ABS пластик',
        'diameter' => '200x150x100',
        'image' => 'https://images.unsplash.com/photo-1591799264318-7e6ef8ddb7ea?q=80&w=400',
        'badges' => ['new']
    ]
];

// API endpoints
if (isset($_GET['api'])) {
    header('Content-Type: application/json');

    switch ($_GET['api']) {
        case 'products':
            echo json_encode($products);
            break;
        case 'search':
            $query = $_GET['q'] ?? '';
            $filtered = array_filter($products, function($p) use ($query) {
                return stripos($p['title'], $query) !== false;
            });
            echo json_encode(array_values($filtered));
            break;
        default:
            echo json_encode(['error' => 'Unknown API endpoint']);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weyer Industrial Systems - Демо</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        <?php include 'style.css'; ?>
    </style>
</head>
<body>
<!-- Header -->
<div class="topbar">
    <div class="container">
        <div class="wrap">
            <div class="info">
                <span><i class="fas fa-truck"></i> Доставка по РФ и СНГ</span>
                <span><i class="fas fa-shield"></i> Гарантия качества</span>
            </div>
            <div class="info">
                <span><i class="fas fa-phone"></i> +7 (812) 123-45-67</span>
            </div>
        </div>
    </div>
</div>

<header>
    <div class="head">
        <div class="container">
            <div class="head-grid">
                <a class="logo" href="#" onclick="return false">
                    <div class="logo-mark">W</div>
                    <div class="logo-text">
                        <span class="logo-title">WEYER</span>
                        <span class="logo-sub">Industrial Systems</span>
                    </div>
                </a>

                <div class="search">
                    <i class="fas fa-search icon"></i>
                    <input type="text" id="searchInput" placeholder="Поиск товаров...">
                    <button class="go"><i class="fas fa-arrow-right"></i></button>
                </div>

                <div class="h-actions">
                    <a class="ha" href="#" onclick="return false">
                        <i class="fas fa-balance-scale"></i>
                        <span>Сравнить</span>
                        <span class="badge" id="compareCount">0</span>
                    </a>
                    <a class="ha" href="#" onclick="return false">
                        <i class="fas fa-heart"></i>
                        <span>Избранное</span>
                        <span class="badge" id="favCount">0</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="nav">
        <div class="container">
            <div class="row">
                <a href="#catalog" class="catalog-btn">
                    <i class="fas fa-bars"></i> Каталог товаров
                </a>
                <nav class="nav-links">
                    <a href="#about">О компании</a>
                    <a href="#production">Производство</a>
                    <a href="#certificates">Сертификаты</a>
                    <a href="#contacts">Контакты</a>
                </nav>
            </div>
        </div>
    </div>
</header>

<!-- Hero -->
<section class="hero">
    <div class="container">
        <div class="wrap">
            <div class="hero-badge">
                <i class="fas fa-star"></i> Работаем с 2003 года
            </div>

            <h1>WEYER - Промышленные решения нового поколения</h1>
            <p>15 000+ позиций: кабельные системы, металлорукава и защитное оборудование от ведущих мировых производителей.</p>

            <div class="hero-actions">
                <a class="btn btn-accent" href="#catalog">
                    <i class="fas fa-rocket"></i> Открыть каталог
                </a>
                <a class="btn btn-ghost" href="#" onclick="openModal('callback-modal')">
                    <i class="fas fa-calculator"></i> Рассчитать проект
                </a>
            </div>

            <div class="stats">
                <div class="stat">
                    <div class="stat-number">15K+</div>
                    <div class="stat-label">товаров в наличии</div>
                </div>
                <div class="stat">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">крупных клиентов</div>
                </div>
                <div class="stat">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">техподдержка</div>
                </div>
            </div>
        </div>
    </div>

    <div class="floating">
        <div class="float-card float-1">
            <i class="fas fa-certificate"></i> ATEX сертификаты
        </div>
        <div class="float-card float-2">
            <i class="fas fa-shipping-fast"></i> Быстрая доставка
        </div>
    </div>
</section>

<!-- Products -->
<section class="section products-section" id="catalog">
    <div class="container">
        <div class="section-bar">
            <div>
                <div class="section-badge">
                    <i class="fas fa-fire"></i> Популярные товары
                </div>
                <h2>Каталог <span class="gradient-text">продукции</span></h2>
            </div>
            <div class="tabs">
                <button class="tab active" onclick="filterProducts('all')">Все</button>
                <button class="tab" onclick="filterProducts('hit')">Хиты</button>
                <button class="tab" onclick="filterProducts('sale')">Скидки</button>
                <button class="tab" onclick="filterProducts('new')">Новинки</button>
            </div>
        </div>

        <div class="products-grid" id="productsGrid">
            <?php foreach ($products as $product): ?>
                <div class="product-card <?php echo implode(' ', $product['badges']); ?>" data-id="<?php echo $product['id']; ?>">
                    <?php if (!empty($product['badges'])): ?>
                        <div class="badges">
                            <?php foreach ($product['badges'] as $badge): ?>
                                <span class="badge badge-<?php echo $badge; ?>">
                                        <?php
                                        switch($badge) {
                                            case 'hit': echo 'ХИТ'; break;
                                            case 'new': echo 'НОВИНКА'; break;
                                            case 'sale':
                                                $discount = round((($product['old_price'] - $product['price']) / $product['old_price']) * 100);
                                                echo "-{$discount}%";
                                                break;
                                        }
                                        ?>
                                    </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="product-fav" onclick="toggleFavorite(<?php echo $product['id']; ?>)">
                        <i class="far fa-heart"></i>
                    </div>

                    <div class="product-image">
                        <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['title']; ?>">
                    </div>

                    <div class="product-body">
                        <div class="product-sku"><?php echo $product['sku']; ?></div>
                        <h3 class="product-title"><?php echo $product['title']; ?></h3>

                        <div class="product-specs">
                            <div class="spec">
                                <span>Материал</span>
                                <strong><?php echo $product['material']; ?></strong>
                            </div>
                            <div class="spec">
                                <span>Диаметр</span>
                                <strong><?php echo $product['diameter']; ?></strong>
                            </div>
                        </div>

                        <div class="product-footer">
                            <div class="product-price">
                                <?php if (isset($product['old_price'])): ?>
                                    <span class="price-old"><?php echo number_format($product['old_price'], 0, '.', ' '); ?> ₽</span>
                                <?php endif; ?>
                                <span class="price-current"><?php echo number_format($product['price'], 0, '.', ' '); ?> ₽</span>
                            </div>
                            <div class="product-actions">
                                <button class="action-btn" onclick="toggleCompare(<?php echo $product['id']; ?>)" title="Сравнить">
                                    <i class="fas fa-balance-scale"></i>
                                </button>
                                <button class="action-btn btn-primary" onclick="requestQuote(<?php echo $product['id']; ?>)" title="Запрос КП">
                                    <i class="fas fa-file-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <h3>Нужна консультация специалиста?</h3>
        <p>Подберем оптимальное решение для вашего проекта</p>
        <button class="btn btn-white" onclick="openModal('callback-modal')">
            <i class="fas fa-phone"></i> Обратный звонок
        </button>
    </div>
</section>

<!-- Footer -->
<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h4>WEYER Industrial</h4>
                <p>Промышленные решения нового поколения</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-telegram"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h4>Контакты</h4>
                <p><i class="fas fa-phone"></i> +7 (812) 123-45-67</p>
                <p><i class="fas fa-envelope"></i> info@weyer.ru</p>
                <p><i class="fas fa-map-marker-alt"></i> Санкт-Петербург</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 WEYER. Все права защищены.</p>
        </div>
    </div>
</footer>

<!-- Modal -->
<div id="modal-overlay" class="modal-overlay">
    <div id="callback-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Обратный звонок</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form onsubmit="submitCallback(event)">
                    <div class="form-group">
                        <label>Имя *</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Телефон *</label>
                        <input type="tel" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label>Комментарий</label>
                        <textarea name="message" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Заказать звонок</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Все JavaScript функции из наших предыдущих файлов
    let favorites = JSON.parse(localStorage.getItem('weyer_favorites') || '[]');
    let compare = JSON.parse(localStorage.getItem('weyer_compare') || '[]');

    function updateCounters() {
        document.getElementById('favCount').textContent = favorites.length;
        document.getElementById('compareCount').textContent = compare.length;
    }

    function toggleFavorite(id) {
        const idx = favorites.indexOf(id);
        if (idx > -1) {
            favorites.splice(idx, 1);
            alert('Удалено из избранного');
        } else {
            favorites.push(id);
            alert('Добавлено в избранное');
        }
        localStorage.setItem('weyer_favorites', JSON.stringify(favorites));
        updateCounters();
    }

    function toggleCompare(id) {
        const idx = compare.indexOf(id);
        if (idx > -1) {
            compare.splice(idx, 1);
            alert('Убрано из сравнения');
        } else if (compare.length < 4) {
            compare.push(id);
            alert('Добавлено к сравнению');
        } else {
            alert('Можно сравнивать не более 4 товаров');
            return;
        }
        localStorage.setItem('weyer_compare', JSON.stringify(compare));
        updateCounters();
    }

    function requestQuote(id) {
        openModal('callback-modal');
    }

    function filterProducts(filter) {
        const cards = document.querySelectorAll('.product-card');
        const tabs = document.querySelectorAll('.tab');

        tabs.forEach(t => t.classList.remove('active'));
        event.target.classList.add('active');

        cards.forEach(card => {
            if (filter === 'all' || card.classList.contains(filter)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function openModal(modalId) {
        document.getElementById('modal-overlay').style.display = 'flex';
        document.getElementById(modalId).style.display = 'block';
    }

    function closeModal() {
        document.getElementById('modal-overlay').style.display = 'none';
        document.querySelectorAll('.modal').forEach(m => m.style.display = 'none');
    }

    function submitCallback(e) {
        e.preventDefault();
        alert('Заявка отправлена! Мы свяжемся с вами в ближайшее время.');
        closeModal();
        e.target.reset();
    }

    // Поиск
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        const cards = document.querySelectorAll('.product-card');

        cards.forEach(card => {
            const title = card.querySelector('.product-title').textContent.toLowerCase();
            if (title.includes(query)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });

    // Закрытие модалки по клику вне
    document.getElementById('modal-overlay').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    // Инициализация
    updateCounters();

    // Демо уведомление
    setTimeout(() => {
        alert('🎉 Добро пожаловать в демо Weyer Industrial!\n\n✅ Все функции работают\n✅ Данные сохраняются в localStorage\n✅ Формы отправляются через JavaScript');
    }, 2000);
</script>
</body>
</html>