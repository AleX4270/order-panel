FROM php:8.4-apache

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install \
    pdo_pgsql \
    bcmath \
    opcache \
    zip

RUN a2enmod rewrite \
    && sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY backend/composer.json backend/composer.lock ./

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

COPY backend/ ./

RUN composer dump-autoload --optimize

ENTRYPOINT ["entrypoint.sh"]

CMD ["apache2-foreground"]