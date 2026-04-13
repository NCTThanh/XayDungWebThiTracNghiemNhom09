# Sử dụng image PHP 8.2 có sẵn Nginx
FROM richarvey/nginx-php-fpm:latest

# Thiết lập thư mục làm việc
COPY . /var/www/html

# Cấu hình môi trường cho Laravel
ENV WEBROOT /var/www/html/public
ENV PHP_UPLOAD_MAX_FILESIZE 100M
ENV PHP_POST_MAX_SIZE 100M

# Cài đặt các dependencies của PHP (Composer)
RUN composer install --no-dev --optimize-autoloader

# NÂNG CẤP NODE.JS LÊN PHIÊN BẢN 22 ĐỂ PHÙ HỢP VỚI VITE 7
RUN apk add --no-cache nodejs-current npm && \
    npm install && \
    npm run build

# Tạo các thư mục framework nếu chưa có và cấp quyền
RUN mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/bootstrap/cache

# Phân quyền cho Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 80
EXPOSE 80

# Lệnh khởi chạy
CMD ["/start.sh"]