<!-- Footer -->
<footer>
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <h3>Продукция</h3>
                <ul class="footer-links">
                    <?php
                    // Получаем основные категории для футера
                    $categories = get_terms(array(
                        'taxonomy' => 'product_category',
                        'hide_empty' => false,
                        'number' => 5,
                        'orderby' => 'count',
                        'order' => 'DESC'
                    ));

                    if ($categories && !is_wp_error($categories)) {
                        foreach ($categories as $category) {
                            echo '<li><a href="' . get_term_link($category) . '"><i class="fas fa-chevron-right"></i> ' . esc_html($category->name) . '</a></li>';
                        }
                    } else {
                        // Fallback ссылки если категорий нет
                        $fallback_links = array(
                            'Металлорукава' => get_post_type_archive_link('product'),
                            'Кабельные вводы' => get_post_type_archive_link('product'),
                            'Соединители' => get_post_type_archive_link('product'),
                            'Защитные системы' => get_post_type_archive_link('product'),
                            'Комплектующие' => get_post_type_archive_link('product')
                        );

                        foreach ($fallback_links as $name => $url) {
                            echo '<li><a href="' . esc_url($url) . '"><i class="fas fa-chevron-right"></i> ' . $name . '</a></li>';
                        }
                    }
                    ?>
                </ul>
            </div>

            <div class="footer-col">
                <h3>Компания</h3>
                <ul class="footer-links">
                    <?php
                    // Получаем страницы для раздела "Компания"
                    $company_pages = get_pages(array(
                        'meta_key' => '_footer_section',
                        'meta_value' => 'company',
                        'number' => 5
                    ));

                    if (!empty($company_pages)) {
                        foreach ($company_pages as $page) {
                            echo '<li><a href="' . get_permalink($page) . '"><i class="fas fa-chevron-right"></i> ' . esc_html($page->post_title) . '</a></li>';
                        }
                    } else {
                        // Fallback ссылки
                        $company_links = array(
                            'О нас' => get_page_link(get_page_by_path('about')),
                            'Производство' => '#',
                            'Сертификаты' => '#',
                            'Новости' => get_permalink(get_option('page_for_posts')),
                            'Вакансии' => '#'
                        );

                        foreach ($company_links as $name => $url) {
                            if ($url && $url !== '#') {
                                echo '<li><a href="' . esc_url($url) . '"><i class="fas fa-chevron-right"></i> ' . $name . '</a></li>';
                            } else {
                                echo '<li><a href="#"><i class="fas fa-chevron-right"></i> ' . $name . '</a></li>';
                            }
                        }
                    }
                    ?>
                </ul>
            </div>

            <div class="footer-col">
                <h3>Поддержка</h3>
                <ul class="footer-links">
                    <?php
                    // Получаем страницы для раздела "Поддержка"
                    $support_pages = get_pages(array(
                        'meta_key' => '_footer_section',
                        'meta_value' => 'support',
                        'number' => 5
                    ));

                    if (!empty($support_pages)) {
                        foreach ($support_pages as $page) {
                            echo '<li><a href="' . get_permalink($page) . '"><i class="fas fa-chevron-right"></i> ' . esc_html($page->post_title) . '</a></li>';
                        }
                    } else {
                        // Fallback ссылки
                        $support_links = array(
                            'Техническая документация' => '#',
                            'FAQ' => get_page_link(get_page_by_path('faq')),
                            'Статьи' => get_permalink(get_option('page_for_posts')),
                            'Видеоинструкции' => '#',
                            'Контакты техподдержки' => get_page_link(get_page_by_path('contacts'))
                        );

                        foreach ($support_links as $name => $url) {
                            if ($url && $url !== '#') {
                                echo '<li><a href="' . esc_url($url) . '"><i class="fas fa-chevron-right"></i> ' . $name . '</a></li>';
                            } else {
                                echo '<li><a href="#"><i class="fas fa-chevron-right"></i> ' . $name . '</a></li>';
                            }
                        }
                    }
                    ?>
                </ul>
            </div>

            <div class="footer-col">
                <h3>Контакты</h3>
                <div class="footer-contact">
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt contact-icon"></i>
                        <div class="contact-text">
                            <?php echo get_theme_mod('company_address', '115280, Москва, ул. Ленинская Слобода, 26'); ?>
                        </div>
                    </div>

                    <div class="contact-item">
                        <i class="fas fa-phone-alt contact-icon"></i>
                        <div class="contact-text">
                            <a href="tel:<?php echo str_replace(array(' ', '(', ')', '-'), '', get_theme_mod('company_phone', '+7 (495) 123-45-67')); ?>">
                                <?php echo get_theme_mod('company_phone', '+7 (495) 123-45-67'); ?>
                            </a>
                        </div>
                    </div>

                    <div class="contact-item">
                        <i class="fas fa-envelope contact-icon"></i>
                        <div class="contact-text">
                            <a href="mailto:<?php echo get_theme_mod('company_email', 'info@weyer.ru'); ?>">
                                <?php echo get_theme_mod('company_email', 'info@weyer.ru'); ?>
                            </a>
                        </div>
                    </div>

                    <div class="contact-item">
                        <i class="fas fa-clock contact-icon"></i>
                        <div class="contact-text">
                            <?php echo get_theme_mod('company_hours', 'Пн-Пт: 9:00 - 18:00'); ?>
                        </div>
                    </div>
                </div>

                <div class="footer-social">
                    <?php
                    $social_links = array(
                        'vk' => get_theme_mod('social_vk', ''),
                        'telegram' => get_theme_mod('social_telegram', ''),
                        'youtube' => get_theme_mod('social_youtube', ''),
                        'linkedin' => get_theme_mod('social_linkedin', '')
                    );

                    $social_icons = array(
                        'vk' => 'fab fa-vk',
                        'telegram' => 'fab fa-telegram',
                        'youtube' => 'fab fa-youtube',
                        'linkedin' => 'fab fa-linkedin'
                    );

                    foreach ($social_links as $platform => $url) {
                        if (!empty($url)) {
                            echo '<a href="' . esc_url($url) . '" class="social-link" target="_blank" rel="noopener">';
                            echo '<i class="' . $social_icons[$platform] . '"></i>';
                            echo '</a>';
                        }
                    }

                    // Если нет настроенных соцсетей, показываем плейсхолдеры
                    if (array_filter($social_links) === array()) {
                        echo '<a href="#" class="social-link"><i class="fab fa-vk"></i></a>';
                        echo '<a href="#" class="social-link"><i class="fab fa-telegram"></i></a>';
                        echo '<a href="#" class="social-link"><i class="fab fa-youtube"></i></a>';
                        echo '<a href="#" class="social-link"><i class="fab fa-linkedin"></i></a>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php echo get_theme_mod('company_name', 'Weyer'); ?>. Все права защищены. Производство промышленных кабельных систем</p>

            <div class="footer-bottom-links">
                <a href="<?php echo get_privacy_policy_url(); ?>">Политика конфиденциальности</a>
                <a href="<?php echo get_page_link(get_page_by_path('terms')); ?>">Пользовательское соглашение</a>
                <a href="<?php echo get_page_link(get_page_by_path('cookies')); ?>">Политика cookies</a>
            </div>
        </div>
    </div>
</footer>

<!-- Кнопка "Наверх" -->
<button id="scrollToTop" class="scroll-to-top" title="Наверх">
    <i class="fas fa-chevron-up"></i>
</button>

<!-- Модальные окна -->
<div id="modal-overlay" class="modal-overlay">
    <!-- Быстрый просмотр товара -->
    <div id="quick-view-modal" class="modal quick-view-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title">Быстрый просмотр</h3>
                <button class="modal-close" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="modal-body">
                <!-- Содержимое загружается через AJAX -->
            </div>
        </div>
    </div>

    <!-- Форма обратной связи -->
    <div id="callback-modal" class="modal callback-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Обратный звонок</h3>
                <button class="modal-close" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="callback-form" method="post">
                    <?php wp_nonce_field('callback_form', 'callback_nonce'); ?>
                    <div class="form-group">
                        <label for="callback-name">Ваше имя *</label>
                        <input type="text" id="callback-name" name="callback_name" required>
                    </div>
                    <div class="form-group">
                        <label for="callback-phone">Телефон *</label>
                        <input type="tel" id="callback-phone" name="callback_phone" required>
                    </div>
                    <div class="form-group">
                        <label for="callback-message">Комментарий</label>
                        <textarea id="callback-message" name="callback_message" rows="4"></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Отправить заявку</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- CSS стили для футера -->
<style>
    footer {
        background: var(--secondary, #334155);
        color: #cbd5e1;
        padding: 60px 0 30px;
        margin-top: auto;
    }

    .footer-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 40px;
        margin-bottom: 40px;
    }

    .footer-col h3 {
        color: #ffffff;
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 20px;
        border-bottom: 2px solid var(--primary, #0052FF);
        padding-bottom: 8px;
        display: inline-block;
    }

    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links li {
        margin-bottom: 12px;
    }

    .footer-links a {
        color: #94a3b8;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: color 0.3s ease;
        font-size: 14px;
    }

    .footer-links a:hover {
        color: #ffffff;
    }

    .footer-links a i {
        font-size: 10px;
        opacity: 0.7;
    }

    .footer-contact {
        display: flex;
        flex-direction: column;
        gap: 16px;
        margin-bottom: 24px;
    }

    .contact-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .contact-icon {
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary, #0052FF);
        font-size: 16px;
        margin-top: 2px;
    }

    .contact-text {
        color: #94a3b8;
        font-size: 14px;
        line-height: 1.5;
    }

    .contact-text a {
        color: inherit;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .contact-text a:hover {
        color: #ffffff;
    }

    .footer-social {
        display: flex;
        gap: 12px;
    }

    .social-link {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        color: #94a3b8;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 18px;
    }

    .social-link:hover {
        background: var(--primary, #0052FF);
        color: #ffffff;
        transform: translateY(-2px);
    }

    .footer-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 30px;
        border-top: 1px solid #475569;
        flex-wrap: wrap;
        gap: 16px;
    }

    .footer-bottom p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }

    .footer-bottom-links {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }

    .footer-bottom-links a {
        color: #64748b;
        text-decoration: none;
        font-size: 14px;
        transition: color 0.3s ease;
    }

    .footer-bottom-links a:hover {
        color: #ffffff;
    }

    /* Кнопка наверх */
    .scroll-to-top {
        position: fixed;
        bottom: 100px;
        right: 30px;
        width: 50px;
        height: 50px;
        background: var(--gradient-primary, linear-gradient(135deg, #0052FF 0%, #3B82F6 100%));
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: none;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0, 82, 255, 0.3);
        transition: all 0.3s ease;
        z-index: 1000;
        font-size: 18px;
    }

    .scroll-to-top:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0, 82, 255, 0.4);
    }

    .scroll-to-top.visible {
        display: flex;
    }

    /* Модальные окна */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        padding: 20px;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal {
        background: white;
        border-radius: 16px;
        max-width: 600px;
        width: 100%;
        max-height: 90vh;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        transform: scale(0.8);
        opacity: 0;
        transition: all 0.3s ease;
    }

    .modal-overlay.active .modal {
        transform: scale(1);
        opacity: 1;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 24px 24px 0;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 24px;
    }

    .modal-header h3 {
        margin: 0;
        color: var(--secondary, #1a1d29);
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #64748b;
        transition: color 0.3s ease;
    }

    .modal-close:hover {
        color: var(--error, #ff3b30);
    }

    .modal-body {
        padding: 0 24px 24px;
        overflow-y: auto;
        max-height: calc(90vh - 100px);
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--secondary, #1a1d29);
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 16px;
        transition: border-color 0.3s ease;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--primary, #0052FF);
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 24px;
    }

    /* Адаптивность футера */
    @media (max-width: 1024px) {
        .footer-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
        }
    }

    @media (max-width: 768px) {
        .footer-grid {
            grid-template-columns: 1fr;
            gap: 30px;
        }

        .footer-bottom {
            flex-direction: column;
            text-align: center;
            gap: 16px;
        }

        .footer-bottom-links {
            justify-content: center;
        }

        .scroll-to-top {
            bottom: 80px;
            right: 20px;
            width: 45px;
            height: 45px;
        }
    }

    @media (max-width: 480px) {
        footer {
            padding: 40px 0 20px;
        }

        .footer-col h3 {
            font-size: 16px;
        }

        .contact-item {
            align-items: center;
        }

        .footer-social {
            justify-content: center;
        }
    }
</style>

<!-- JavaScript для футера -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Кнопка "Наверх"
        const scrollToTopBtn = document.getElementById('scrollToTop');

        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                scrollToTopBtn.classList.add('visible');
            } else {
                scrollToTopBtn.classList.remove('visible');
            }
        });

        scrollToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Модальные окна
        window.openModal = function(modalId) {
            const overlay = document.getElementById('modal-overlay');
            const modal = document.getElementById(modalId);

            if (overlay && modal) {
                // Скрываем все модальные окна
                document.querySelectorAll('.modal').forEach(m => m.style.display = 'none');

                // Показываем нужное
                modal.style.display = 'block';
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        };

        window.closeModal = function() {
            const overlay = document.getElementById('modal-overlay');
            overlay.classList.remove('active');
            document.body.style.overflow = '';

            setTimeout(() => {
                document.querySelectorAll('.modal').forEach(m => m.style.display = 'none');
            }, 300);
        };

        // Закрытие модального окна по клику вне его
        document.getElementById('modal-overlay').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Форма обратного звонка
        const callbackForm = document.getElementById('callback-form');
        if (callbackForm) {
            callbackForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(callbackForm);
                formData.append('action', 'submit_callback');

                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Заявка отправлена! Мы свяжемся с вами в ближайшее время.');
                            closeModal();
                            callbackForm.reset();
                        } else {
                            alert('Ошибка отправки заявки. Попробуйте еще раз.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Ошибка отправки заявки. Попробуйте еще раз.');
                    });
            });
        }
    });
</script>

<?php wp_footer(); ?>

</body>
</html>

<?php
/**
 * AJAX обработчик для формы обратного звонка
 */
add_action('wp_ajax_submit_callback', 'weyer_submit_callback');
add_action('wp_ajax_nopriv_submit_callback', 'weyer_submit_callback');

function weyer_submit_callback() {
    // Проверка nonce
    if (!wp_verify_nonce($_POST['callback_nonce'], 'callback_form')) {
        wp_send_json_error('Неверный токен безопасности');
    }

    // Получение и санитизация данных
    $name = sanitize_text_field($_POST['callback_name']);
    $phone = sanitize_text_field($_POST['callback_phone']);
    $message = sanitize_textarea_field($_POST['callback_message']);

    // Валидация
    if (empty($name) || empty($phone)) {
        wp_send_json_error('Заполните обязательные поля');
    }

    // Отправка email
    $to = get_option('admin_email');
    $subject = 'Заявка на обратный звонок - ' . get_bloginfo('name');
    $email_message = "Новая заявка на обратный звонок:\n\n";
    $email_message .= "Имя: {$name}\n";
    $email_message .= "Телефон: {$phone}\n";
    $email_message .= "Сообщение: {$message}\n";
    $email_message .= "Время: " . current_time('mysql') . "\n";
    $email_message .= "IP: " . $_SERVER['REMOTE_ADDR'] . "\n";

    $headers = array('Content-Type: text/html; charset=UTF-8');

    if (wp_mail($to, $subject, nl2br($email_message), $headers)) {
        wp_send_json_success('Заявка отправлена успешно');
    } else {
        wp_send_json_error('Ошибка отправки email');
    }
}
?>