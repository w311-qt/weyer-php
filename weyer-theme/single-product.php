<?php get_header(); ?>

    <main class="product-page">
        <div class="container">
            <nav class="breadcrumbs"><?php weyer_breadcrumbs(); ?></nav>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin: 40px 0;">
                <!-- Изображение -->
                <div>
                    <?php if (has_post_thumbnail()): ?>
                        <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'large'); ?>"
                             alt="<?php the_title(); ?>" style="width: 100%; border-radius: 16px;">
                    <?php endif; ?>
                </div>

                <!-- Информация -->
                <div>
                    <?php if ($sku = get_product_sku()): ?>
                        <div style="color: #64748b; margin-bottom: 8px;">Артикул: <?php echo $sku; ?></div>
                    <?php endif; ?>

                    <h1 style="margin: 0 0 16px;"><?php the_title(); ?></h1>

                    <div style="display: flex; gap: 12px; align-items: center; margin-bottom: 24px;">
                        <?php if ($old_price = get_product_old_price()): ?>
                            <span style="text-decoration: line-through; color: #94a3b8;">
                            <?php echo number_format($old_price, 0, '.', ' '); ?> ₽
                        </span>
                        <?php endif; ?>
                        <span style="font-size: 28px; font-weight: 700; color: #0052ff;">
                        <?php echo number_format(get_product_price(), 0, '.', ' '); ?> ₽
                    </span>
                    </div>

                    <!-- Характеристики -->
                    <div style="background: #f8fafc; padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                        <?php
                        $specs = array(
                            'Материал' => get_post_meta(get_the_ID(), '_product_material', true),
                            'Диаметр' => get_post_meta(get_the_ID(), '_product_diameter', true),
                            'Температура' => get_post_meta(get_the_ID(), '_product_temperature', true),
                            'Защита' => get_post_meta(get_the_ID(), '_product_protection', true),
                        );

                        foreach ($specs as $label => $value):
                            if ($value): ?>
                                <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #e2e8f0;">
                                    <span><?php echo $label; ?>:</span>
                                    <strong><?php echo $value; ?></strong>
                                </div>
                            <?php endif;
                        endforeach; ?>
                    </div>

                    <!-- Кнопки -->
                    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                        <button class="btn btn-primary btn-lg" onclick="openModal('quote-modal')">
                            Запросить КП
                        </button>
                        <button class="btn btn-ghost" onclick="toggleFavorite(<?php echo get_the_ID(); ?>)">
                            ♥ В избранное
                        </button>
                        <button class="btn btn-ghost" onclick="toggleCompare(<?php echo get_the_ID(); ?>)">
                            ⚖ Сравнить
                        </button>
                    </div>
                </div>
            </div>

            <!-- Описание -->
            <?php if (get_the_content()): ?>
                <div style="background: white; padding: 32px; border-radius: 16px; margin: 40px 0;">
                    <h2>Описание</h2>
                    <?php the_content(); ?>
                </div>
            <?php endif; ?>

            <!-- Похожие товары -->
            <section style="margin: 60px 0;">
                <h2>Похожие товары</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-top: 32px;">
                    <?php
                    $related = new WP_Query(array(
                        'post_type' => 'product',
                        'posts_per_page' => 4,
                        'post__not_in' => array(get_the_ID()),
                    ));
                    while ($related->have_posts()): $related->the_post();
                        get_template_part('template-parts/product-card');
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
            </section>
        </div>
    </main>

    <!-- Модальное окно для КП -->
    <div id="quote-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Запрос КП</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form onsubmit="submitQuote(event)">
                    <input type="hidden" name="product_id" value="<?php echo get_the_ID(); ?>">
                    <div class="form-group">
                        <label>Имя *</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Телефон *</label>
                        <input type="tel" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label>Количество</label>
                        <input type="number" name="quantity" value="1" min="1">
                    </div>
                    <button type="submit" class="btn btn-primary">Отправить</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function submitQuote(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            formData.append('action', 'request_quote');
            formData.append('nonce', '<?php echo wp_create_nonce('weyer_quote_nonce'); ?>');

            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                body: formData
            }).then(r => r.json()).then(data => {
                alert(data.success ? 'Запрос отправлен!' : 'Ошибка отправки');
                if (data.success) {
                    closeModal();
                    form.reset();
                }
            });
        }
    </script>

<?php get_footer(); ?>