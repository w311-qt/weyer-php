#!/bin/bash
# Скрипт быстрого развертывания проекта Weyer в Docker

set -e

# Цвета
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🚀 Быстрое развертывание проекта Weyer WordPress${NC}"
echo "=================================================="

# Проверка Docker
if ! command -v docker &> /dev/null; then
    echo -e "${RED}❌ Docker не найден. Установите Docker и попробуйте снова.${NC}"
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo -e "${RED}❌ Docker Compose не найден. Установите Docker Compose и попробуйте снова.${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Docker найден${NC}"

# Создание структуры проекта
echo -e "${BLUE}📁 Создание структуры проекта...${NC}"

# Создаем необходимые директории
mkdir -p weyer-theme/{assets/{js,css,images},template-parts,inc}
mkdir -p uploads
mkdir -p mysql-data
mkdir -p backups

# Устанавливаем права
chmod 755 uploads mysql-data
chmod +x init-wordpress.sh 2>/dev/null || true

echo -e "${GREEN}✅ Структура создана${NC}"

# Создание файлов темы
echo -e "${BLUE}📝 Создание файлов темы...${NC}"

# Основные файлы уже должны быть скопированы из артефактов
# Проверяем наличие ключевых файлов
if [ ! -f "weyer-theme/style.css" ]; then
    echo -e "${YELLOW}⚠️ Файл style.css не найден. Скопируйте файлы темы из артефактов.${NC}"
fi

if [ ! -f "weyer-theme/functions.php" ]; then
    echo -e "${YELLOW}⚠️ Файл functions.php не найден. Скопируйте файлы темы из артефактов.${NC}"
fi

# Создаем .env файл для настроек
cat > .env << EOF
# Настройки базы данных
DB_NAME=weyer_wp
DB_USER=weyer_user
DB_PASSWORD=weyer_pass
DB_ROOT_PASSWORD=rootpass123

# WordPress настройки
WP_DEBUG=true
WP_DEBUG_LOG=true

# Порты
WORDPRESS_PORT=8080
PHPMYADMIN_PORT=8081
MAILHOG_PORT=8025
MYSQL_PORT=3306
EOF

echo -e "${GREEN}✅ Файл .env создан${NC}"

# Создаем .gitignore
cat > .gitignore << EOF
# WordPress
wp-config.php
wp-content/uploads/
wp-content/cache/
wp-content/backups/

# Docker
mysql-data/
uploads/
backups/

# IDE
.vscode/
.idea/

# OS
.DS_Store
Thumbs.db

# Logs
*.log
error_log

# Environment
.env.local
EOF

echo -e "${GREEN}✅ Файл .gitignore создан${NC}"

# Проверка доступности портов
echo -e "${BLUE}🔍 Проверка доступности портов...${NC}"

check_port() {
    if lsof -Pi :$1 -sTCP:LISTEN -t >/dev/null ; then
        echo -e "${YELLOW}⚠️ Порт $1 занят. Завершите процесс или измените порт в docker-compose.yml${NC}"
        return 1
    fi
    return 0
}

PORTS_OK=true
check_port 8080 || PORTS_OK=false
check_port 8081 || PORTS_OK=false
check_port 3306 || PORTS_OK=false

if [ "$PORTS_OK" = false ]; then
    echo -e "${RED}❌ Некоторые порты заняты. Освободите их или измените настройки.${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Порты свободны${NC}"

# Запуск контейнеров
echo -e "${BLUE}🐳 Запуск Docker контейнеров...${NC}"

docker-compose down -v 2>/dev/null || true
docker-compose up -d

echo -e "${YELLOW}⏳ Ожидание готовности сервисов...${NC}"

# Ждем готовности WordPress
for i in {1..60}; do
    if curl -s http://localhost:8080 > /dev/null; then
        break
    fi
    if [ $i -eq 60 ]; then
        echo -e "${RED}❌ WordPress не запустился за 60 секунд${NC}"
        docker-compose logs wordpress
        exit 1
    fi
    sleep 2
    echo -n "."
done

echo ""
echo -e "${GREEN}✅ Сервисы запущены!${NC}"

# Инициализация WordPress
echo -e "${BLUE}⚙️ Инициализация WordPress...${NC}"

# Ждем еще немного для полной готовности
sleep 10

# Активируем тему через WP-CLI
echo -e "${BLUE}🎨 Активация темы Weyer...${NC}"
docker-compose exec wordpress wp theme activate weyer-theme --allow-root 2>/dev/null || {
    echo -e "${YELLOW}⚠️ Не удалось активировать тему автоматически. Активируйте вручную в админке.${NC}"
}

# Настройка постоянных ссылок
docker-compose exec wordpress wp rewrite structure '/%postname%/' --allow-root 2