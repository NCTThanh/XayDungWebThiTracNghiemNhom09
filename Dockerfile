# ===================== STAGE 1: Build Assets =====================
FROM node:22-alpine AS builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# ===================== STAGE 2: Runtime =====================
FROM richarvey/nginx-php-fpm:latest

WORKDIR /var/www/html
COPY --from=builder /app .

# Fix lỗi 100MM và các thông số PHP
ENV PHP_UPLOAD_MAX_FILESIZE=100M \
    PHP_POST_MAX_SIZE=100M \
    WEBROOT=/var/www/html/public \
    COMPOSER_ALLOW_SUPERUSER=1

# Cài đặt PHP dependencies (Bỏ qua scripts để tránh lỗi plugin prestissimo)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-plugins --no-scripts

# Tạo cấu trúc thư mục chuẩn
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache

# Phân quyền "mạnh tay" để không bao giờ lỗi 500
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy cấu hình Nginx vào đúng vị trí của image richarvey
COPY nginx.conf /etc/nginx/sites-available/default.conf

EXPOSE 80

# Lệnh khởi chạy: Dọn rác, Migrate và Lên sóng
CMD php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    php artisan cache:clear && \
    php artisan migrate --force && \
    /start.sh