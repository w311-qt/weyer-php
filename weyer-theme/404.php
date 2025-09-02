<?php get_header(); ?>

    <main style="padding: 80px 0; background: var(--g50); min-height: 60vh;">
        <div class="container">
            <div style="text-align: center; max-width: 600px; margin: 0 auto;">
                <div style="font-size: 120px; font-weight: 900; color: var(--primary); line-height: 1; margin-bottom: 24px;">
                    404
                </div>

                <h1 style="font-size: 32px; margin-bottom: 16px; color: var(--secondary);">
                    Страница не найдена
                </h1>

                <p style="font-size: 18px; color: var(--g600); margin-bottom: 32px;">
                    К сожалению, запрашиваемая страница не существует или была перемещена.
                </p>

                <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; margin-bottom: 48px;">
                    <a href="<?php echo home_url(); ?>" class="btn btn-primary">
                        <i class="fas fa-home"></i> На главную
                    </a>
                    <a href="<?php echo get_post_type_archive_link('product'); ?>" class="btn btn-ghost">
                        <i class="fas fa-th-large"></i> Каталог товаров
                    </a>
                </div>

                <!-- Поиск -->
                <div style="background: white; padding: 32px; border-radius: 16px; box-shadow: var(--shadow-md);">
                    <h3 style="margin-bottom: 16px;">Попробуйте найти то, что искали:</h3>
                    <form role="search" method="get" action="<?php echo home_url('/'); ?>" style="display: flex; gap: 12px;">
                        <input type="text" name="s" placeholder="Введите поисковый запрос..."
                               style="flex: 1; padding: 12px 16px; border: 2px solid var(--g200); border-radius: var(--r-lg);">
                        <input type="hidden" name="post_type" value="product">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Найти
                        </button>
                    </form>
                </div>

                <!-- Популярные категории -->
                <div style="margin-top: 48px;">
                    <h3 style="margin-bottom: 24px;">Популярные категории:</h3>
                    <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                        <?php
                        $categories = get_terms(array(
                            'taxonomy' => 'product_category',
                            'hide_empty' => true,
                            'number' => 4,
                            'orderby' => 'count',
                            'order' => 'DESC'
                        ));

                        if ($categories && !is_wp_error($categories)) {
                            foreach ($categories as $category) {
                                echo '<a href="' . get_term_link($category) . '" class="btn btn-ghost">' . $category->name . '</a>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php get_footer(); ?>