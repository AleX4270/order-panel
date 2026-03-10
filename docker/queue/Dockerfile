FROM php:8.4-cli as base

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install \
    pdo_pgsql \
    bcmath

FROM base as dev

CMD php artisan queue:listen
