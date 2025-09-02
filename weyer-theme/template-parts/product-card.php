<?php
/**
 * Шаблон карточки товара
 * template-parts/product-card.php
 */

$product_id = get_the_ID();
$sku = get_product_sku($product_id);
$price = get_product_price($product_id);
$old_price = get_product_old_price($product_id);
$material = get_post_meta($product_id, '_product_material', true);
$diameter = get_post_meta($product_id, '_product_diameter', true);
$temperature = get_post_meta($product_id, '_product_temperature', true);
$protection = get_post_meta($product_id, '_product_protection', true);
$in_stock = is_product_in_stock($product_id);

// Получаем изображение товара
$product_image = get_the_post_thumbnail_url($product_id, 'product-thumb');
if (!$product_image) {
    $product_image = get_template_directory_uri() . '/assets/images/product-placeholder.jpg';
}

// Определяем бейджи товара
$badges = array();

// Скидка
if ($old_price && $old_price > $price) {
    $discount = round((($old_price - $price) / $old_price) * 100);
    $badges[] = array(
        'text' => "Скидка {$discount}%",
        'class' => 'badge-discount'
    );
}

// Новинка (если товар добавлен за последние 30 дней)
$post_date = get_the_date('U');
if ((time() - $post_date) < (30 * 24 * 60 * 60)) {
    $badges[] = array(
        'text' => 'Новинка',
        'class' => 'badge-new'
    );
}

// Хит продаж (можно добавить кастомное поле или определять по количеству просмотров)
$is_hit = get_post_meta($product_id, '_product_is_hit', true);
if ($is_hit) {
    $badges[] = array(
        'text' => 'Хит продаж',
        'class' => 'badge-hit'
    );
}

// Получаем категории товара
$categories = get_the_terms($product_id, 'product_category');
$category_names = array();
if ($categories && !is_wp_error($categories)) {
    foreach ($categories as $category) {
        $category_names[] = $category->name;
    }
}
?>

<div class="product-card" data-product-id="<?php echo $product_id; ?>" data-price="<?php echo $price; ?>" data-categories="<?php echo implode(',', $category_names); ?>">

    <?php if (!empty($badges)): ?>
        <div class="product-badges">
            <?php foreach ($badges as $badge): ?>
                <span class="product-badge <?php echo $badge['class']; ?>"><?php echo $badge['text']; ?></span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Изображение товара -->
    <div class="product-image">
        <img src="<?php echo esc_url($product_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy">

        <!-- Кнопки быстрых действий -->
        <div class="product-compare">
            <input type="checkbox" id="compare_<?php echo $product_id; ?>" class="compare-check">
            <label for="compare_<?php echo $product_id; ?>" class="compare-label" data-action="compare" data-product-id="<?php echo $product_id; ?>" title="Добавить к сравнению">
                <i class="fas fa-check"></i>
            </label>
        </div>

        <!-- Индикатор наличия -->
        <?php if (!$in_stock): ?>
            <div class="out-of-stock-overlay">
                <span>Нет в наличии</span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Контент карточки -->
    <div class="product-content">

        <!-- Артикул -->
        <?php if ($sku): ?>
            <div class="product-sku">Арт: <?php echo esc_html($sku); ?></div>
        <?php endif; ?>

        <!-- Название товара -->
        <h3 class="product-title">
            <a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a>
        </h3>

        <!-- Характеристики -->
        <div class="product-specs">
            <?php if ($material): ?>
                <div class="spec-item">
                    <span class="spec-label">Материал:</span>
                    <span class="spec-value"><?php echo esc_html($material); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($diameter): ?>
                <div class="spec-item">
                    <span class="spec-label">Диаметр:</span>
                    <span class="spec-value"><?php echo esc_html($diameter); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($temperature): ?>
                <div class="spec-item">
                    <span class="spec-label">Температура:</span>
                    <span class="spec-value"><?php echo esc_html($temperature); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($protection): ?>
                <div class="spec-item">
                    <span class="spec-label">Защита:</span>
                    <span class="spec-value"><?php echo esc_html($protection); ?></span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Футер карточки -->
        <div class="product-footer">
            <!-- Цена -->
            <div class="product-price">
                <?php if ($old_price && $old_price > $price): ?>
                    <span class="price-old"><?php echo number_format($old_price, 0, '.', ' '); ?> ₽</span>
                <?php endif; ?>
                <span class="price-current"><?php echo number_format($price, 0, '.', ' '); ?> ₽</span>
            </div>

            <!-- Действия -->
            <div class="product-actions">
                <button class="action-btn" data-action="quick-view" data-product-id="<?php echo $product_id; ?>" title="Быстрый просмотр">
                    <i class="fas fa-eye"></i>
                </button>

                <button class="action-btn" data-action="add-to-favorites" data-product-id="<?php echo $product_id; ?>" title="Добавить в избранное">
                    <i class="fas fa-heart"></i>
                </button>

                <?php if ($in_stock): ?>
                    <button class="action-btn btn-primary" data-action="request-quote" data-product-id="<?php echo $product_id; ?>" title="Запросить КП">
                        <i class="fas fa-file-alt"></i>
                    </button>
                <?php else: ?>
                    <button class="action-btn" data-action="notify-availability" data-product-id="<?php echo $product_id; ?>" title="Уведомить о поступлении">
                        <i class="fas fa-bell"></i>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Быстрые действия (показываются при ховере) -->
    <div class="quick-actions">
        <div class="quick-action" data-action="quick-view" data-product-id="<?php echo $product_id; ?>" title="Быстрый просмотр">
            <i class="fas fa-eye"></i>
        </div>
        <div class="quick-action" data-action="compare" data-product-id="<?php echo $product_id; ?>" title="Сравнить">
            <i class="fas fa-balance-scale"></i>
        </div>
        <?php if ($in_stock): ?>
            <div class="quick-action primary" data-action="request-quote" data-product-id="<?php echo $product_id; ?>" title="Запрос КП">
                <i class="fas fa-file-alt"></i>
            </div>
        <?php endif; ?>
    </div>

    <!-- Статус наличия -->
    <div class="stock-status <?php echo $in_stock ? 'in-stock' : 'out-of-stock'; ?>">
        <?php if ($in_stock): ?>
            <i class="fas fa-check-circle"></i> В наличии
        <?php else: ?>
            <i class="fas fa-times-circle"></i> Нет в наличии
        <?php endif; ?>
    </div>
</div>

<style>
    /* Стили для карточки товара */
    .product-specs {
        margin-bottom: 16px;
    }

    .spec-item {
        display: flex;
        justify-content: space-between;
        padding: 6px 0;
        border-bottom: 1px dashed var(--gray-200);
        font-size: 13px;
    }

    .spec-item:last-child {
        border-bottom: none;
    }

    .spec-label {
        color: var(--gray-600);
    }

    .spec-value {
        font-weight: 600;
        color: var(--gray-800);
    }

    .product-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
        padding-top: 12px;
        border-top: 1px solid var(--gray-100);
    }

    .product-price {
        display: flex;
        flex-direction: column;
    }

    .price-current {
        font-family: 'Inter', sans-serif;
        font-weight: 700;
        font-size: 18px;
        color: var(--primary);
    }

    .price-old {
        font-size: 14px;
        color: var(--gray-400);
        text-decoration: line-through;
        margin-bottom: 2px;
    }

    .product-actions {
        display: flex;
        gap: 6px;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: var(--radius-sm);
        background: var(--gray-100);
        color: var(--gray-600);
        border: none;
        cursor: pointer;
        transition: all var(--t-base);
        font-size: 14px;
    }

    .action-btn:hover {
        background: var(--primary);
        color: var(--white);
        transform: translateY(-1px);
    }

    .action-btn.btn-primary {
        background: var(--gradient-primary);
        color: var(--white);
    }

    .quick-actions {
        position: absolute;
        left: 12px;
        right: 12px;
        bottom: 12px;
        display: flex;
        gap: 8px;
        opacity: 0;
        transform: translateY(10px);
        transition: all var(--t-base);
    }

    .product-card:hover .quick-actions {
        opacity: 1;
        transform: translateY(0);
    }

    .quick-action {
        flex: 1;
        padding: 10px;
        border-radius: var(--radius-lg);
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid var(--gray-200);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all var(--t-base);
        backdrop-filter: blur(10px);
    }

    .quick-action:hover {
        background: var(--primary);
        color: var(--white);
        border-color: var(--primary);
        transform: translateY(-2px);
    }

    .quick-action.primary {
        background: var(--gradient-primary);
        color: var(--white);
        border-color: var(--primary);
    }

    .quick-action.primary:hover {
        transform: translateY(-2px) scale(1.05);
    }

    .stock-status {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 6px 12px;
        font-size: 11px;
        font-weight: 600;
        text-align: center;
        transition: all var(--t-base);
    }

    .stock-status.in-stock {
        background: rgba(0, 208, 132, 0.1);
        color: var(--success);
    }

    .stock-status.out-of-stock {
        background: rgba(255, 59, 48, 0.1);
        color: var(--error);
    }

    /* Адаптивность карточки */
    @media (max-width: 768px) {
        .product-card {
            margin-bottom: 16px;
        }

        .product-actions {
            flex-direction: column;
            gap: 4px;
        }

        .action-btn {
            width: 28px;
            height: 28px;
            font-size: 12px;
        }
    }

    /* Список товаров */
    .catalog-grid.list-view .product-card {
        flex-direction: row;
        height: auto;
    }

    .catalog-grid.list-view .product-image {
        width: 200px;
        height: 150px;
        flex-shrink: 0;
    }

    .catalog-grid.list-view .product-content {
        flex: 1;
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 20px;
    }

    .catalog-grid.list-view .product-specs {
        display: flex;
        gap: 16px;
        margin: 0;
    }

    .catalog-grid.list-view .spec-item {
        border: none;
        padding: 0;
        flex-direction: column;
        align-items: center;
        text-align: center;
        min-width: 80px;
    }

    .catalog-grid.list-view .product-footer {
        border: none;
        padding: 0;
        margin: 0;
        flex-direction: column;
        align-items: flex-end;
        gap: 12px;
    }

    .catalog-grid.list-view .stock-status {
        position: static;
        padding: 4px 8px;
        border-radius: var(--radius-sm);
        width: fit-content;
    }

    /* Загрузка */
    .catalog-grid.loading {
        opacity: 0.6;
        pointer-events: none;
    }

    .catalog-grid.loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 40px;
        height: 40px;
        border: 3px solid var(--gray-200);
        border-top: 3px solid var(--primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: translate(-50%, -50%) rotate(0deg); }
        100% { transform: translate(-50%, -50%) rotate(360deg); }
    }

    /* Состояние "нет товаров" */
    .no-products {
        text-align: center;
        padding: 60px 20px;
        color: var(--gray-600);
        grid-column: 1 / -1;
    }

    .no-products h3 {
        margin: 16px 0 8px;
        color: var(--gray-800);
    }

    .no-products p {
        margin-bottom: 20px;
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Обработчик быстрых действий
        document.addEventListener('click', function(e) {
            const action = e.target.closest('[data-action]');
            if (!action) return;

            const actionType = action.dataset.action;
            const productId = action.dataset.productId;

            switch (actionType) {
                case 'quick-view':
                    handleQuickView(productId);
                    break;

                case 'add-to-favorites':
                    handleAddToFavorites(productId, action);
                    break;

                case 'request-quote':
                    handleRequestQuote(productId);
                    break;

                case 'notify-availability':
                    handleNotifyAvailability(productId);
                    break;

                case 'compare':
                    handleCompare(productId, action);
                    break;
            }
        });

        function handleQuickView(productId) {
            // Загружаем данные товара через AJAX
            fetch(`${weyer_ajax.ajax_url}?action=get_product_quick_view&product_id=${productId}&nonce=${weyer_ajax.nonce}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showQuickViewModal(data.data);
                    }
                })
                .catch(error => {
                    console.error('Error loading quick view:', error);
                });
        }

        function handleAddToFavorites(productId, button) {
            const favorites = JSON.parse(localStorage.getItem('weyer_favorites') || '[]');
            const icon = button.querySelector('i');

            if (favorites.includes(productId)) {
                // Убираем из избранного
                const index = favorites.indexOf(productId);
                favorites.splice(index, 1);
                icon.classList.remove('fas');
                icon.classList.add('far');
                button.classList.remove('active');
            } else {
                // Добавляем в избранное
                favorites.push(productId);
                icon.classList.remove('far');
                icon.classList.add('fas');
                button.classList.add('active');
            }

            localStorage.setItem('weyer_favorites', JSON.stringify(favorites));

            // Показываем уведомление
            showNotification(
                favorites.includes(productId) ? 'Товар добавлен в избранное' : 'Товар удален из избранного'
            );
        }

        function handleRequestQuote(productId) {
            // Открываем форму запроса КП
            window.location.href = `/request-quote/?product_id=${productId}`;
        }

        function handleNotifyAvailability(productId) {
            // Показываем форму для уведомления о поступлении
            showNotifyForm(productId);
        }

        function handleCompare(productId, button) {
            const compareList = JSON.parse(localStorage.getItem('weyer_compare') || '[]');

            if (compareList.includes(productId)) {
                // Убираем из сравнения
                const index = compareList.indexOf(productId);
                compareList.splice(index, 1);
                button.classList.remove('active');
            } else {
                // Добавляем в сравнение
                if (compareList.length >= 4) {
                    showNotification('Можно сравнивать не более 4 товаров', 'warning');
                    return;
                }
                compareList.push(productId);
                button.classList.add('active');
            }

            localStorage.setItem('weyer_compare', JSON.stringify(compareList));

            // Обновляем счетчик сравнения
            updateCompareCounter(compareList.length);

            showNotification(
                compareList.includes(productId) ? 'Товар добавлен к сравнению' : 'Товар убран из сравнения'
            );
        }

        function showNotification(message, type = 'success') {
            // Создаем уведомление
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check' : 'exclamation-triangle'}"></i>
            <span>${message}</span>
        `;

            document.body.appendChild(notification);

            // Показываем уведомление
            setTimeout(() => notification.classList.add('show'), 100);

            // Убираем уведомление
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        function updateCompareCounter(count) {
            const counter = document.getElementById('compare-count');
            const bar = document.getElementById('compare-bar');

            if (counter) {
                counter.textContent = count;
            }

            if (bar) {
                bar.style.display = count > 0 ? 'flex' : 'none';
            }
        }

        // Инициализация избранного при загрузке страницы
        function initializeFavorites() {
            const favorites = JSON.parse(localStorage.getItem('weyer_favorites') || '[]');
            favorites.forEach(productId => {
                const button = document.querySelector(`[data-action="add-to-favorites"][data-product-id="${productId}"]`);
                if (button) {
                    const icon = button.querySelector('i');
                    if (icon) {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        button.classList.add('active');
                    }
                }
            });
        }

        // Инициализация сравнения при загрузке страницы
        function initializeCompare() {
            const compareList = JSON.parse(localStorage.getItem('weyer_compare') || '[]');
            compareList.forEach(productId => {
                const button = document.querySelector(`[data-action="compare"][data-product-id="${productId}"]`);
                if (button) {
                    button.classList.add('active');
                }
            });
            updateCompareCounter(compareList.length);
        }

        // Инициализация
        initializeFavorites();
        initializeCompare();
    });
</script>card {
background: var(--white);
border-radius: var(--radius-xl);
overflow: hidden;
transition: transform var(--t-base), box-shadow var(--t-base);
position: relative;
height: 100%;
display: flex;
flex-direction: column;
border: 2px solid transparent;
}

.product-card:hover {
transform: translateY(-4px);
box-shadow: var(--shadow-xl);
border-color: var(--primary);
}

.product-badges {
position: absolute;
top: 12px;
left: 12px;
display: flex;
flex-direction: column;
gap: 6px;
z-index: 3;
}

.product-badge {
font-size: 10px;
font-weight: 700;
text-transform: uppercase;
letter-spacing: 0.5px;
padding: 4px 8px;
border-radius: var(--radius-full);
color: white;
}

.badge-hit {
background: var(--gradient-accent);
}

.badge-new {
background: var(--success);
}

.badge-discount {
background: var(--error);
}

.product-image {
height: 200px;
display: flex;
align-items: center;
justify-content: center;
padding: 16px;
background: var(--gray-50);
position: relative;
overflow: hidden;
}

.product-image img {
max-height: 160px;
max-width: 100%;
object-fit: contain;
transition: transform var(--t-slow);
}

.product-card:hover .product-image img {
transform: scale(1.05);
}

.product-compare {
position: absolute;
top: 12px;
right: 12px;
z-index: 3;
}

.compare-check {
display: none;
}

.compare-label {
width: 28px;
height: 28px;
border: 2px solid var(--primary);
border-radius: var(--radius-sm);
display: flex;
align-items: center;
justify-content: center;
cursor: pointer;
background: var(--white);
transition: all var(--t-base);
}

.compare-label i {
color: var(--primary);
opacity: 0;
transition: opacity var(--t-base);
font-size: 12px;
}

.compare-check:checked + .compare-label {
background: var(--primary);
border-color: var(--primary);
}

.compare-check:checked + .compare-label i {
opacity: 1;
color: var(--white);
}

.out-of-stock-overlay {
position: absolute;
top: 0;
left: 0;
right: 0;
bottom: 0;
background: rgba(0, 0, 0, 0.7);
display: flex;
align-items: center;
justify-content: center;
color: white;
font-weight: 600;
}

.product-content {
padding: 16px;
flex-grow: 1;
display: flex;
flex-direction: column;
}

.product-sku {
color: var(--gray-500);
font-size: 12px;
margin-bottom: 8px;
font-family: 'JetBrains Mono', monospace;
}

.product-title {
font-size: 16px;
margin-bottom: 12px;
font-weight: 600;
}

.product-title a {
color: var(--secondary);
text-decoration: none;
transition: color var(--t-base);
}

.product-title a:hover {
color: var(--primary);
}

.product-