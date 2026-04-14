# Stage 1: Build assets (Node)
FROM node:22-alpine AS builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: PHP + Nginx Runtime
FROM php:8.2-fpm

# Cài đặt extension và Nginx
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev nginx \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Cài Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY --from=builder /app .

# Fix lỗi upload size chuẩn 100M
RUN echo "upload_max_filesize=100M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size=100M" >> /usr/local/etc/php/conf.d/uploads.ini

# Cài đặt PHP dependencies (Đã dọn dẹp prestissimo)
RUN composer install --no-dev --optimize-autoloader --no-plugins --no-scripts

# Cấu hình Nginx: Xóa bản mặc định, nạp bản của mình
COPY nginx.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# Cấp quyền cho Laravel
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

# Chạy cả PHP-FPM và Nginx
CMD php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan migrate --force && \
    php-fpm -D && nginx -g "daemon off;"