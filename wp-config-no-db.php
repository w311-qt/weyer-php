<?php
/**
 * WordPress конфигурация БЕЗ базы данных
 * Для демонстрации статического контента
 */

// Отключаем базу данных
define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASSWORD', '');
define('DB_HOST', '');
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

// Отключаем попытки подключения к БД
define('WP_INSTALLING', true);

// Базовые настройки WordPress
$table_prefix = 'wp_';
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', true);

// Отключаем функции требующие БД
define('WP_SETUP_CONFIG', true);
define('ABSPATH', dirname(__FILE__) . '/');

// Переопределяем функции для работы без БД
function wp_redirect($location, $status = 302) {
    // Блокируем редиректы на установку
    if (strpos($location, 'wp-admin/install.php') !== false) {
        return false;
    }
    header("Location: $location", true, $status);
    exit;
}

// Перехватываем запросы и показываем наш статический контент
if (!defined('WP_USE_THEMES')) {
    define('WP_USE_THEMES', true);
}

// Если запрос к статическому сайту
if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/static-site') === 0) {
    $file = __DIR__ . $_SERVER['REQUEST_URI'];
    if (file_exists($file) && is_file($file)) {
        if (pathinfo($file, PATHINFO_EXTENSION) == 'php') {
            include $file;
        } else {
            readfile($file);
        }
        exit;
    }
}

// Простая заглушка для главной страницы
if (basename($_SERVER['PHP_SELF']) == 'index.php' && !isset($_GET['p'])) {
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Weyer Industrial Systems - Demo</title>
        <style>
            body {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
                margin: 0;
                background: linear-gradient(135deg, #0F172A 0%, #1F2937 100%);
                color: white;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .container {
                text-align: center;
                max-width: 800px;
                padding: 40px;
                background: rgba(255,255,255,0.1);
                border-radius: 24px;
                backdrop-filter: blur(10px);
            }
            h1 {
                font-size: 3rem;
                margin-bottom: 16px;
                background: linear-gradient(135deg, #0052FF 0%, #3B82F6 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            p { font-size: 18px; opacity: 0.9; margin-bottom: 32px; }
            .btn {
                display: inline-block;
                padding: 16px 32px;
                background: linear-gradient(135deg, #0052FF 0%, #3B82F6 100%);
                color: white;
                text-decoration: none;
                border-radius: 12px;
                font-weight: 600;
                margin: 0 8px;
                transition: transform 0.2s ease;
            }
            .btn:hover { transform: translateY(-2px); }
            .links { margin-top: 40px; }
            .links a {
                display: inline-block;
                margin: 8px 16px;
                color: #94a3b8;
                text-decoration: none;
            }
            .links a:hover { color: #3B82F6; }
        </style>
    </head>
    <body>
    <div class="container">
        <h1>WEYER INDUSTRIAL</h1>
        <p>Демонстрация фронтенда без базы данных</p>

        <div>
            <a href="/static-site/" class="btn">📱 Статический сайт</a>
            <a href="http://localhost:8090" class="btn">⚡ PHP демо</a>
        </div>

        <div class="links">
            <a href="/wp-admin">WordPress админка</a>
            <a href="/wp-content/themes/weyer-theme/">Файлы темы</a>
        </div>

        <p style="font-size: 14px; margin-top: 40px; opacity: 0.7;">
            🚀 Запущено в Docker без базы данных<br>
            Все данные хранятся в файлах
        </p>
    </div>
    </body>
    </html>
    <?php
    exit;
}

// Минимальная инициализация WordPress
require_once ABSPATH . 'wp-settings.php';
?>