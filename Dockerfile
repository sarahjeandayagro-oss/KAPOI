FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
COPY artisan ./
COPY app ./app
COPY bootstrap ./bootstrap
COPY config ./config
COPY database ./database
COPY routes ./routes
COPY resources ./resources
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

FROM node:20-bookworm-slim AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM php:8.3-cli-bookworm
WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       git \
       unzip \
       libpq-dev \
       libzip-dev \
       libpng-dev \
       libicu-dev \
       libonig-dev \
    && docker-php-ext-install pdo_pgsql pgsql mbstring zip intl \
    && rm -rf /var/lib/apt/lists/*

COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build
COPY . .

RUN chmod +x /var/www/html/entrypoint.sh

EXPOSE 10000
CMD ["/var/www/html/entrypoint.sh"]
