# WordPress с поддержкой PostgreSQL
FROM wordpress:6.4-php8.2-apache

# Установка дополнительных пакетов
RUN apt-get update && apt-get install -y \
    wget \
    curl \
    vim \
    unzip \
    git \
    postgresql-client \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Установка PHP расширений для PostgreSQL
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Увеличиваем лимиты PHP
RUN echo 'memory_limit = 512M' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini && \
    echo 'upload_max_filesize = 100M' >> /usr/local/etc/php/conf.d/docker-php-uploads.ini && \
    echo 'post_max_size = 100M' >> /usr/local/etc/php/conf.d/docker-php-uploads.ini && \
    echo 'max_execution_time = 300' >> /usr/local/etc/php/conf.d/docker-php-time.ini && \
    echo 'max_input_vars = 3000' >> /usr/local/etc/php/conf.d/docker-php-vars.ini

# Включаем mod_rewrite
RUN a2enmod rewrite

# Устанавливаем WP-CLI
RUN curl -O https://raw.githubusercontent.com/wp-cli/wp-cli/v2.8.1/utils/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp

# Скачиваем и устанавливаем плагин PG4WP для поддержки PostgreSQL
RUN cd /tmp && \
    wget https://github.com/PostgreSQL-For-Wordpress/postgresql-for-wordpress/archive/master.zip && \
    unzip master.zip && \
    mkdir -p /var/www/html/wp-content && \
    mv postgresql-for-wordpress-master /var/www/html/wp-content/pg4wp

# Копируем конфиг для PostgreSQL
COPY ./wp-config-postgres.php /var/www/html/wp-config-postgres.php

# Копируем скрипт инициализации
COPY ./init-wordpress-postgres.sh /usr/local/bin/init-wordpress.sh
RUN chmod +x /usr/local/bin/init-wordpress.sh

# Копируем тему
COPY ./weyer-theme /var/www/html/wp-content/themes/weyer-theme

# Устанавливаем права
RUN chown -R www-data:www-data /var/www/html/wp-content/

# Создаем кастомную точку входа
COPY ./docker-entrypoint-postgres.sh /usr/local/bin/docker-entrypoint-postgres.sh
RUN chmod +x /usr/local/bin/docker-entrypoint-postgres.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint-postgres.sh"]
CMD ["apache2-foreground"]