# ===================== STAGE 1: Build Assets (Node) =====================
FROM node:22-alpine AS builder

WORKDIR /app
COPY package*.json ./
RUN npm install

COPY . .
RUN npm run build

# ===================== STAGE 2: PHP + Nginx Runtime =====================
FROM richarvey/nginx-php-fpm:latest

# Copy code từ builder
COPY --from=builder /app /var/www/html

WORKDIR /var/www/html

# Cấu hình môi trường cho Image
ENV WEBROOT=/var/www/html/public \
    PHP_UPLOAD_MAX_FILESIZE=100M \
    PHP_POST_MAX_SIZE=100M \
    PHP_MEMORY_LIMIT=512M \
    COMPOSER_ALLOW_SUPERUSER=1

# Cài đặt Composer dependencies (Loại bỏ các plugin gây lỗi như prestissimo)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-plugins --no-scripts

# Tạo các thư mục framework (Sửa cú pháp dấu ngoặc nhọn)
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache

# Cấp quyền (Sửa lỗi Permission denied)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Railway dùng port động, image này tự nhận qua biến PORT
EXPOSE 80

# Lệnh khởi chạy duy nhất (Kết hợp clear cache và chạy migration)
CMD php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    php artisan cache:clear && \
    php artisan migrate --force && \
    /start.sh