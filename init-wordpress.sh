#!/bin/bash
# Скрипт автоматической настройки WordPress для темы Weyer

echo "🚀 Начинаем инициализацию WordPress для темы Weyer..."

# Ждем готовности базы данных
echo "⏳ Ожидание готовности базы данных..."
while ! mysql -h"$WORDPRESS_DB_HOST" -u"$WORDPRESS_DB_USER" -p"$WORDPRESS_DB_PASSWORD" -e "SELECT 1" > /dev/null 2>&1; do
    echo "   База данных еще не готова. Ждем 5 секунд..."
    sleep 5
done

echo "✅ База данных готова!"

# Переходим в директорию WordPress
cd /var/www/html

# Проверяем, установлен ли уже WordPress
if ! wp core is-installed --allow-root 2>/dev/null; then
    echo "📦 Устанавливаем WordPress..."

    # Скачиваем WP-CLI если его нет
    if ! command -v wp &> /dev/null; then
        curl -O https://raw.githubusercontent.com/wp-cli/wp-cli/v2.8.1/utils/wp-completion.bash
        curl -O https://raw.githubusercontent.com/wp-cli/wp-cli/v2.8.1/utils/wp-cli.phar
        chmod +x wp-cli.phar
        mv wp-cli.phar /usr/local/bin/wp
    fi

    # Создаем wp-config.php если его нет
    if [ ! -f wp-config.php ]; then
        echo "⚙️ Создаем wp-config.php..."
        wp config create \
            --dbname="$WORDPRESS_DB_NAME" \
            --dbuser="$WORDPRESS_DB_USER" \
            --dbpass="$WORDPRESS_DB_PASSWORD" \
            --dbhost="$WORDPRESS_DB_HOST" \
            --allow-root
    fi

    # Устанавливаем WordPress
    echo "🔧 Устанавливаем WordPress..."
    wp core install \
        --url="http://localhost:8080" \
        --title="Weyer Industrial Systems" \
        --admin_user="admin" \
        --admin_password="admin123" \
        --admin_email="admin@weyer.local" \
        --skip-email \
        --allow-root

    echo "✅ WordPress установлен!"
    echo "👤 Логин: admin"
    echo "🔑 Пароль: admin123"
fi

# Активируем тему Weyer
if wp theme is-active weyer-theme --allow-root; then
    echo "✅ Тема Weyer уже активна"
else
    echo "🎨 Активируем тему Weyer..."
    wp theme activate weyer-theme --allow-root || echo "⚠️ Не удалось активировать тему. Проверьте файлы темы."
fi

# Устанавливаем полезные плагины
echo "🔌 Устанавливаем полезные плагины..."
wp plugin install contact-form-7 --activate --allow-root || true
wp plugin install yoast-seo --activate --allow-root || true
wp plugin install wp-mail-smtp --activate --allow-root || true

# Настраиваем постоянные ссылки
echo "🔗 Настраиваем постоянные ссылки..."
wp option update permalink_structure '/%postname%/' --allow-root

# Создаем демо-контент если его еще нет
if ! wp post list --post_type=product --format=count --allow-root | grep -q '[1-9]'; then
    echo "📦 Создаем демо-товары..."

    # Создаем категории
    wp term create product_category "Металлорукава" --description="Гибкие металлические рукава" --allow-root || true
    wp term create product_category "Кабельные вводы" --description="Герметичные вводы для кабелей" --allow-root || true
    wp term create product_category "Соединители" --description="Промышленные соединители" --allow-root || true

    # Создаем демо-товары
    PRODUCT1_ID=$(wp post create --post_type=product --post_title="Металлорукав нержавеющий 12мм" --post_content="Высококачественный металлорукав из нержавеющей стали для защиты кабелей в агрессивных средах." --post_status=publish --porcelain --allow-root)
    wp post meta add $PRODUCT1_ID _product_sku "MR-12-SS" --allow-root
    wp post meta add $PRODUCT1_ID _product_price "850" --allow-root
    wp post meta add $PRODUCT1_ID _product_old_price "980" --allow-root
    wp post meta add $PRODUCT1_ID _product_material "AISI 304" --allow-root
    wp post meta add $PRODUCT1_ID _product_diameter "12 мм" --allow-root
    wp post meta add $PRODUCT1_ID _product_temperature "-25°C +150°C" --allow-root
    wp post meta add $PRODUCT1_ID _product_protection "IP68" --allow-root
    wp post meta add $PRODUCT1_ID _product_in_stock "1" --allow-root
    wp post meta add $PRODUCT1_ID _product_is_hit "1" --allow-root

    PRODUCT2_ID=$(wp post create --post_type=product --post_title="Кабельный ввод Ex M20" --post_content="Взрывозащищенный кабельный ввод для использования во взрывоопасных зонах." --post_status=publish --porcelain --allow-root)
    wp post meta add $PRODUCT2_ID _product_sku "CG-M20-EX" --allow-root
    wp post meta add $PRODUCT2_ID _product_price "1790" --allow-root
    wp post meta add $PRODUCT2_ID _product_old_price "1990" --allow-root
    wp post meta add $PRODUCT2_ID _product_material "Никелированная латунь" --allow-root
    wp post meta add $PRODUCT2_ID _product_diameter "M20" --allow-root
    wp post meta add $PRODUCT2_ID _product_temperature "-40°C +100°C" --allow-root
    wp post meta add $PRODUCT2_ID _product_protection "Ex d IIC" --allow-root
    wp post meta add $PRODUCT2_ID _product_in_stock "1" --allow-root
    wp post meta add $PRODUCT2_ID _product_is_hit "0" --allow-root

    echo "✅ Демо-товары созданы!"
fi

# Создаем базовые страницы
echo "📄 Создаем базовые страницы..."
wp post create --post_type=page --post_title="О компании" --post_name="about" --post_content="Информация о компании Weyer" --post_status=publish --allow-root || true
wp post create --post_type=page --post_title="Контакты" --post_name="contacts" --post_content="Наши контакты" --post_status=publish --allow-root || true
wp post create --post_type=page --post_title="Сравнение товаров" --post_name="compare" --post_content="[weyer_compare_page]" --post_status=publish --allow-root || true
wp post create --post_type=page --post_title="Избранное" --post_name="favorites" --post_content="[weyer_favorites_page]" --post_status=publish --allow-root || true

# Настройки темы
echo "⚙️ Настраиваем тему..."
wp option update blogdescription "15 000+ позиций промышленного оборудования от ведущих производителей" --allow-root

echo "🎉 Инициализация завершена!"
echo "🌐 Сайт доступен по адресу: http://localhost:8080"
echo "👨‍💼 Админка: http://localhost:8080/wp-admin"
echo "📊 phpMyAdmin: http://localhost:8081"
echo "📧 MailHog: http://localhost:8025"