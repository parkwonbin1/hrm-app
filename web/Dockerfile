# 1️⃣ composer stage 선언
FROM composer:latest AS composer

# 2️⃣ php runtime stage
FROM php:8.1-fpm-bullseye

# 필수 패키지 설치
RUN apt-get update && apt-get install -y \
    git unzip zip \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# composer 바이너리
COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY --chown=www-data:0 . .
RUN chmod -R g+w /var/www/html

# 일반 유저로 전환
USER www-data

# Composer 의존성 설치
RUN composer install --no-dev --optimize-autoloader

EXPOSE 9000
CMD ["php-fpm"]

