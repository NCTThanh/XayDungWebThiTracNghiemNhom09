# Sử dụng image PHP + Nginx ổn định
FROM richarvey/nginx-php-fpm:latest

# Copy toàn bộ code
COPY . /var/www/html

# Thiết lập biến cho image
ENV WEBROOT=/var/www/html/public
ENV PHP_UPLOAD_MAX_FILESIZE=100M
ENV PHP_POST_MAX_SIZE=100M
ENV PHP_MEMORY_LIMIT=512M

# Cài Composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Cài Node.js + Build Vite (nếu bạn dùng Vite)
RUN apk add --no-cache nodejs npm && \
    npm install && \
    npm run build

# Tạo thư mục Laravel
RUN mkdir -p /var/www/html/storage/framework/{sessions,views,cache} \
    && mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/bootstrap/cache

# Phân quyền
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port theo Railway
EXPOSE ${PORT:-80}

# Khởi chạy (image này dùng start.sh)
CMD ["/start.sh"]