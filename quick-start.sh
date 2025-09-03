#!/bin/bash

echo "🚀 Быстрый запуск Weyer WordPress (MySQL версия для начала)"

# Останавливаем текущие контейнеры
docker-compose down -v 2>/dev/null || true

# Создаем простой docker-compose.yml с MySQL
cat > docker-compose.yml << 'EOF'
version: '3.8'

services:
  # MySQL база данных (проще для начала)
  db:
    image: mysql:8.0
    container_name: weyer_mysql
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: rootpass123
      MYSQL_DATABASE: weyer_wp
      MYSQL_USER: weyer_user
      MYSQL_PASSWORD: weyer_pass
    volumes:
      - db_data:/var/lib/mysql
    ports:
      - "3306:3306"
    command: --default-authentication-plugin=mysql_native_password

  # WordPress
  wordpress:
    image: wordpress:6.4-apache
    container_name: weyer_wordpress
    restart: always
    depends_on:
      - db
    environment:
      WORDPRESS_DB_HOST: db:3306
      WORDPRESS_DB_USER: weyer_user
      WORDPRESS_DB_PASSWORD: weyer_pass
      WORDPRESS_DB_NAME: weyer_wp
      WORDPRESS_DEBUG: 1
    ports:
      - "8080:80"
    volumes:
      - wordpress_data:/var/www/html
      - ./weyer-theme:/var/www/html/wp-content/themes/weyer-theme
      - ./uploads:/var/www/html/wp-content/uploads

  # phpMyAdmin для управления БД
  phpmyadmin:
    image: phpmyadmin:latest
    container_name: weyer_phpmyadmin
    restart: always
    depends_on:
      - db
    environment:
      PMA_HOST: db
      PMA_USER: weyer_user
      PMA_PASSWORD: weyer_pass
    ports:
      - "8081:80"

volumes:
  db_data:
  wordpress_data:
EOF

# Создаем структуру папок
mkdir -p weyer-theme/{assets/{js,css,images},template-parts}
mkdir -p uploads
chmod 755 uploads

# Создаем минимальные файлы темы
echo "📁 Создаем файлы темы..."

# style.css
cat > weyer-theme/style.css << 'EOF'
/*
Theme Name: Weyer Industrial Theme
Description: Промышленная тема WordPress
Version: 1.0.0
*/

:root {
    --primary: #0052FF;
    --secondary: #1A1D29;
    --success: #00D084;
    --g50: #F8FAFC;
    --g100: #F1F5F9;
    --g200: #E2E8F0;
    --g600: #475569;
    --g700: #334155;
    --g800: #1E293B;
    --gradient-primary: linear-gradient(135deg, #0052FF 0%, #3B82F6 100%);
    --shadow-md: 0 4px 20px rgba(16,24,40,.08);
    --r-lg: 16px;
    --r-xl: 24px;
}

body {
    font-family: 'Inter', sans-serif;
    margin: 0;
    color: var(--g800);
    background: var(--g50);
}

.container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 24px;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    padding: 12px 18px;
    border-radius: var(--r-lg);
    border: 0;
    cursor: pointer;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.25s ease;
}

.btn-primary {
    background: var(--gradient-primary);
    color: #fff;
}

.btn-primary:hover {
    transform: translateY(-2px);
}
EOF

# functions.php (базовая версия)
cat > weyer-theme/functions.php << 'EOF'
<?php
function weyer_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    register_nav_menus(array('primary' => 'Основное меню'));
}
add_action('after_setup_theme', 'weyer_theme_setup');

function weyer_enqueue_scripts() {
    wp_enqueue_style('weyer-style', get_stylesheet_uri());
    wp_enqueue_style('weyer-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css');
}
add_action('wp_enqueue_scripts', 'weyer_enqueue_scripts');

// Custom Post Type для товаров
function weyer_register_product_post_type() {
    register_post_type('product', array(
        'labels' => array('name' => 'Товары', 'singular_name' => 'Товар'),
        'public' => true,
        'has_archive' => 'catalog',
        'menu_icon' => 'dashicons-products',
        'supports' => array('title', 'editor', 'thumbnail'),
    ));
}
add_action('init', 'weyer_register_product_post_type');
EOF

# index.php (главная страница)
cat > weyer-theme/index.php << 'EOF'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weyer Industrial Systems</title>
    <?php wp_head(); ?>
</head>
<body>
    <header style="background: white; padding: 20px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <h1 style="margin: 0; color: var(--primary);">WEYER</h1>
            <nav>
                <a href="/" style="margin: 0 15px; text-decoration: none;">Главная</a>
                <a href="/catalog" style="margin: 0 15px; text-decoration: none;">Каталог</a>
            </nav>
        </div>
    </header>

    <main style="padding: 80px 0; text-align: center;">
        <div class="container">
            <h1 style="font-size: 48px; margin-bottom: 20px;">Weyer Industrial Systems</h1>
            <p style="font-size: 18px; margin-bottom: 30px;">Промышленные решения нового поколения</p>
            <a href="/wp-admin" class="btn btn-primary">Войти в админку</a>
        </div>
    </main>

    <footer style="background: var(--secondary); color: white; padding: 40px 0; text-align: center;">
        <div class="container">
            <p>&copy; 2024 WEYER. Все права защищены.</p>
        </div>
    </footer>

    <?php wp_footer(); ?>
</body>
</html>
EOF

echo "✅ Файлы темы созданы"

# Запускаем контейнеры
echo "🐳 Запускаем Docker контейнеры..."
docker-compose up -d

echo "⏳ Ждем запуска сервисов..."
sleep 15

# Проверяем доступность
echo "🔍 Проверяем доступность сервисов..."

if curl -s http://localhost:8080 > /dev/null; then
    echo "✅ WordPress запущен: http://localhost:8080"
else
    echo "❌ WordPress не доступен"
fi

if curl -s http://localhost:8081 > /dev/null; then
    echo "✅ phpMyAdmin запущен: http://localhost:8081"
else
    echo "❌ phpMyAdmin не доступен"
fi

echo ""
echo "🎉 Готово!"
echo "🌐 WordPress: http://localhost:8080"
echo "👨‍💼 Админка: http://localhost:8080/wp-admin"
echo "📊 phpMyAdmin: http://localhost:8081"
echo ""
echo "После установки WordPress активируйте тему Weyer в админке"

# Открываем браузер
if command -v start &> /dev/null; then
    start http://localhost:8080
elif command -v xdg-open &> /dev/null; then
    xdg-open http://localhost:8080
elif command -v open &> /dev/null; then
    open http://localhost:8080
fi
EOF

chmod +x quick-start.sh

echo "✅ Скрипт создан: quick-start.sh"
echo "🚀 Запустите: ./quick-start.sh"