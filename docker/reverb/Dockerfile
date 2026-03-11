FROM php:8.4-cli as base

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install \
    pdo_pgsql \
    bcmath \
    pcntl \
    && docker-php-ext-configure \
    pcntl --enable-pcntl

FROM base as local

CMD ["php", "artisan", "reverb:start"]
