<?php get_header(); ?>

    <main style="padding: 60px 0; background: #f8fafc;">
        <div class="container">
            <nav class="breadcrumbs" style="margin-bottom: 32px;">
                <a href="<?php echo home_url(); ?>">Главная</a> > <?php the_title(); ?>
            </nav>

            <div style="background: white; padding: 48px; border-radius: 24px; box-shadow: 0 4px 20px rgba(16,24,40,0.08);">
                <h1 style="margin-bottom: 32px;"><?php the_title(); ?></h1>

                <?php while (have_posts()): the_post(); ?>
                    <div class="page-content">
                        <?php the_content(); ?>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </main>

<?php get_footer(); ?>