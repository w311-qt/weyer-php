# Makefile для проекта Weyer WordPress
.PHONY: build up down restart logs shell db-shell clean install dev prod backup restore

# Цвета для вывода
RED=\033[0;31m
GREEN=\033[0;32m
YELLOW=\033[1;33m
BLUE=\033[0;34m
NC=\033[0m # No Color

# Основные команды
help: ## Показать справку
	@echo -e "${BLUE}🚀 Команды для проекта Weyer WordPress:${NC}"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "${GREEN}%-20s${NC} %s\n", $$1, $$2}'

install: ## Первоначальная установка проекта
	@echo -e "${BLUE}📦 Устанавливаем проект Weyer...${NC}"
	@mkdir -p uploads mysql-data
	@chmod 755 uploads
	@echo -e "${GREEN}✅ Структура папок создана${NC}"

build: ## Собрать Docker образы
	@echo -e "${BLUE}🔨 Собираем Docker образы...${NC}"
	docker-compose build --no-cache
	@echo -e "${GREEN}✅ Образы собраны${NC}"

up: ## Запустить проект
	@echo -e "${BLUE}🚀 Запускаем проект...${NC}"
	docker-compose up -d
	@echo -e "${YELLOW}⏳ Ожидаем запуска сервисов...${NC}"
	@sleep 10
	@echo -e "${GREEN}✅ Проект запущен!${NC}"
	@echo -e "${BLUE}🌐 WordPress: http://localhost:8080${NC}"
	@echo -e "${BLUE}👨‍💼 Админка: http://localhost:8080/wp-admin${NC}"
	@echo -e "${BLUE}📊 phpMyAdmin: http://localhost:8081${NC}"
	@echo -e "${BLUE}📧 MailHog: http://localhost:8025${NC}"

down: ## Остановить проект
	@echo -e "${YELLOW}🛑 Останавливаем проект...${NC}"
	docker-compose down
	@echo -e "${GREEN}✅ Проект остановлен${NC}"

restart: down up ## Перезапустить проект

logs: ## Показать логи
	docker-compose logs -f

logs-wp: ## Показать логи WordPress
	docker-compose logs -f wordpress

logs-db: ## Показать логи базы данных
	docker-compose logs -f db

shell: ## Войти в контейнер WordPress
	@echo -e "${BLUE}🐚 Вход в контейнер WordPress...${NC}"
	docker-compose exec wordpress bash

db-shell: ## Войти в MySQL
	@echo -e "${BLUE}🗄️ Вход в MySQL...${NC}"
	docker-compose exec db mysql -uweyer_user -pweyer_pass weyer_wp

wp-cli: ## Выполнить WP-CLI команду (использование: make wp-cli CMD="plugin list")
	docker-compose exec wordpress wp $(CMD) --allow-root

# Команды разработки
dev: ## Запустить в режиме разработки с живой перезагрузкой
	@echo -e "${BLUE}🔧 Запуск в режиме разработки...${NC}"
	docker-compose -f docker-compose.yml -f docker-compose.dev.yml up -d
	@echo -e "${GREEN}✅ Режим разработки активен${NC}"

watch: ## Следить за изменениями в теме
	@echo -e "${BLUE}👀 Отслеживание изменений...${NC}"
	docker-compose exec wordpress bash -c "while inotifywait -e modify -r /var/www/html/wp-content/themes/weyer-theme; do echo 'Theme files changed'; done"

# Управление данными
clean: ## Очистить все данные (ОСТОРОЖНО!)
	@echo -e "${RED}⚠️ ВНИМАНИЕ: Это удалит все данные!${NC}"
	@read -p "Вы уверены? [y/N]: " confirm && [ "$$confirm" = "y" ] || exit 1
	docker-compose down -v
	docker system prune -f
	rm -rf mysql-data uploads
	@echo -e "${GREEN}✅ Данные очищены${NC}"

backup: ## Создать бэкап базы данных и файлов
	@echo -e "${BLUE}💾 Создание бэкапа...${NC}"
	@mkdir -p backups
	@TIMESTAMP=$$(date +%Y%m%d_%H%M%S) && \
	docker-compose exec -T db mysqldump -uweyer_user -pweyer_pass weyer_wp > backups/db_backup_$$TIMESTAMP.sql && \
	tar -czf backups/files_backup_$$TIMESTAMP.tar.gz weyer-theme uploads && \
	echo -e "${GREEN}✅ Бэкап создан: backups/backup_$$TIMESTAMP${NC}"

restore: ## Восстановить из бэкапа (использование: make restore BACKUP=20231201_120000)
	@echo -e "${BLUE}📥 Восстановление из бэкапа...${NC}"
	@if [ -z "$(BACKUP)" ]; then echo -e "${RED}❌ Укажите имя бэкапа: make restore BACKUP=20231201_120000${NC}"; exit 1; fi
	docker-compose exec -T db mysql -uweyer_user -pweyer_pass weyer_wp < backups/db_backup_$(BACKUP).sql
	tar -xzf backups/files_backup_$(BACKUP).tar.gz
	@echo -e "${GREEN}✅ Данные восстановлены${NC}"

# Диагностика
status: ## Показать статус сервисов
	@echo -e "${BLUE}📊 Статус сервисов:${NC}"
	docker-compose ps

health: ## Проверить здоровье сервисов
	@echo -e "${BLUE}🏥 Проверка здоровья сервисов:${NC}"
	@echo -e "${YELLOW}WordPress:${NC}"
	@curl -s -o /dev/null -w "HTTP %{http_code} - %{time_total}s\n" http://localhost:8080 || echo "❌ Недоступен"
	@echo -e "${YELLOW}phpMyAdmin:${NC}"
	@curl -s -o /dev/null -w "HTTP %{http_code} - %{time_total}s\n" http://localhost:8081 || echo "❌ Недоступен"
	@echo -e "${YELLOW}MailHog:${NC}"
	@curl -s -o /dev/null -w "HTTP %{http_code} - %{time_total}s\n" http://localhost:8025 || echo "❌ Недоступен"

fix-permissions: ## Исправить права доступа
	@echo -e "${BLUE}🔧 Исправление прав доступа...${NC}"
	docker-compose exec wordpress chown -R www-data:www-data /var/www/html/wp-content/
	docker-compose exec wordpress chmod -R 755 /var/www/html/wp-content/
	sudo chown -R $(USER):$(USER) weyer-theme uploads
	@echo -e "${GREEN}✅ Права доступа исправлены${NC}"

# Быстрые команды
open: ## Открыть сайт в браузере
	@echo -e "${BLUE}🌐 Открываем сайт...${NC}"
	@python3 -m webbrowser http://localhost:8080 2>/dev/null || python -m webbrowser http://localhost:8080 2>/dev/null || xdg-open http://localhost:8080 2>/dev/null || open http://localhost:8080 2>/dev/null || echo "Откройте http://localhost:8080"

admin: ## Открыть админку WordPress
	@echo -e "${BLUE}👨‍💼 Открываем админку...${NC}"
	@python3 -m webbrowser http://localhost:8080/wp-admin 2>/dev/null || python -m webbrowser http://localhost:8080/wp-admin 2>/dev/null || xdg-open http://localhost:8080/wp-admin 2>/dev/null || open http://localhost:8080/wp-admin 2>/dev/null || echo "Откройте http://localhost:8080/wp-admin"

# По умолчанию показываем справку
.DEFAULT_GOAL := help