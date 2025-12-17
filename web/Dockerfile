FROM php:8.1-fpm-bullseye

# 필수 패키지 설치
RUN apt-get update && apt-get install -y \
    git unzip zip \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Composer 복사
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# [OKD 호환] 파일 소유권은 www-data, 그룹 권한(g+w) 추가 (중요!)
COPY --chown=www-data:0 . .
RUN chmod -R g+w /var/www/html

# 일반 유저로 전환
USER www-data

# Composer 설치
RUN composer install --no-dev --optimize-autoloader

EXPOSE 9000
CMD ["php-fpm"]
