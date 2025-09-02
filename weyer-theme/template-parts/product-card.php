<?php
/**
 * Шаблон карточки товара - ИСПРАВЛЕННАЯ ВЕРСИЯ
 */

$product_id = get_the_ID();
$sku = get_product_sku($product_id);
$price = get_product_price($product_id);
$old_price = get_product_old_price($product_id);
$material = get_post_meta($product_id, '_product_material', true);
$diameter = get_post_meta($product_id, '_product_diameter', true);
$in_stock = is_product_in_stock($product_id);
$is_hit = is_product_hit($product_id);

// Получаем изображение товара
$product_image = get_the_post_thumbnail_url($product_id, 'product-thumb');
if (!$product_image) {
    $product_image = 'https://via.placeholder.com/300x300/f8fafc/64748b?text=No+Image';
}

// Определяем бейджи товара
$badges = array();

// Скидка
if ($old_price && $old_price > $price) {
    $discount = round((($old_price - $price) / $old_price) * 100);
    $badges[] = array('text' => "-{$discount}%", 'class' => 'sale');
}

// Новинка (если товар добавлен за последние 30 дней)
$post_date = get_the_date('U');
if ((time() - $post_date) < (30 * 24 * 60 * 60)) {
    $badges[] = array('text' => 'НОВИНКА', 'class' => 'new');
}

// Хит продаж
if ($is_hit) {
    $badges[] = array('text' => 'ХИТ', 'class' => 'hit');
}

// Определяем классы для товара
$classes = array('p');
if ($old_price && $old_price > $price) $classes[] = 'sale';
if ($is_hit) $classes[] = 'hit';
if ((time() - $post_date) < (30 * 24 * 60 * 60)) $classes[] = 'new';
?>

<article class="<?php echo implode(' ', $classes); ?>" data-id="<?php echo $product_id; ?>">

    <!-- Бейджи товара -->
    <?php if (!empty($badges)): ?>
        <div class="badges">
            <?php foreach ($badges as $badge): ?>
                <span class="b <?php echo $badge['class']; ?>"><?php echo $badge['text']; ?></span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Кнопка избранного -->
    <div class="fav" onclick="toggleFavorite(<?php echo $product_id; ?>)" title="Добавить в избранное">
        <i class="far fa-heart"></i>
    </div>

    <!-- Изображение товара -->
    <div class="img">
        <img src="<?php echo esc_url($product_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy">

        <!-- Индикатор отсутствия -->
        <?php if (!$in_stock): ?>
            <div class="out-of-stock-overlay">
                <span>Нет в наличии</span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Контент карточки -->
    <div class="body">
        <!-- Артикул -->
        <?php if ($sku): ?>
            <div class="sku"><?php echo esc_html($sku); ?></div>
        <?php endif; ?>

        <!-- Название товара -->
        <h3>
            <a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a>
        </h3>

        <!-- Характеристики -->
        <?php if ($material): ?>
            <div class="spec">
                <span>Материал</span>
                <strong><?php echo esc_html($material); ?></strong>
            </div>
        <?php endif; ?>

        <?php if ($diameter): ?>
            <div class="spec">
                <span>Диаметр</span>
                <strong><?php echo esc_html($diameter); ?></strong>
            </div>
        <?php endif; ?>

        <!-- Футер карточки -->
        <div class="foot">
            <!-- Цена -->
            <div class="price">
                <?php if ($old_price && $old_price > $price): ?>
                    <span class="was"><?php echo number_format($old_price, 0, '.', ' '); ?> ₽</span>
                <?php endif; ?>
                <span class="now"><?php echo number_format($price, 0, '.', ' '); ?> ₽</span>
            </div>
        </div>
    </div>

    <!-- Быстрые действия (показываются при ховере) -->
    <div class="quick">
        <div class="a" onclick="window.location.href='<?php echo get_permalink(); ?>'" title="Подробнее">
            <i class="fas fa-eye"></i>
        </div>
        <div class="a" onclick="toggleCompare(<?php echo $product_id; ?>)" title="Сравнить">
            <i class="fas fa-balance-scale"></i>
        </div>
        <?php if ($in_stock): ?>
            <div class="a" onclick="requestQuote(<?php echo $product_id; ?>)" title="Запрос КП">
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
</article>

<script>
    // Функция запроса КП (если не определена глобально)
    if (typeof requestQuote === 'undefined') {
        function requestQuote(productId) {
            if (typeof openModal === 'function') {
                openModal('quote-modal');
                // Устанавливаем ID товара в форму
                const productIdInput = document.querySelector('#quote-modal input[name="product_id"]');
                if (productIdInput) {
                    productIdInput.value = productId;
                }
            } else {
                // Fallback - переход на страницу товара
                window.location.href = '/product/' + productId;
            }
        }
    }
</script>