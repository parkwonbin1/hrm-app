# 1) composer stage
FROM docker.io/composer:latest AS composer

# 2) php runtime stage
FROM php:8.1-fpm-bullseye

# packages + php extensions
RUN apt-get update && apt-get install -y \
    git unzip zip \
  && docker-php-ext-install mysqli pdo pdo_mysql \
  && rm -rf /var/lib/apt/lists/*

# composer binary
COPY --from=composer /usr/bin/composer /usr/bin/composer

# app dir (php-fpm 기본 문서루트로 쓰기 좋게)
WORKDIR /var/www/html

# 핵심: repo 전체를 복사하지 말고 web 폴더 내용만 복사
COPY --chown=www-data:0 . .

RUN chmod -R g+w /var/www/html

USER www-data

# 여기서 composer.json은 /var/www/html/composer.json 으로 존재해야 함 (web 폴더 기준)
RUN composer install --no-dev --optimize-autoloader

EXPOSE 9000
CMD ["php-fpm"]

