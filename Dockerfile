# Sử dụng image PHP 8.2 có sẵn Nginx
FROM richarvey/nginx-php-fpm:latest

# Thiết lập thư mục làm việc
COPY . /var/www/html

# Cấu hình môi trường cho Laravel
ENV WEBROOT /var/www/html/public
ENV PHP_UPLOAD_MAX_FILESIZE 100M
ENV PHP_POST_MAX_SIZE 100M

# Cài đặt các dependencies của PHP
RUN composer install --no-dev --optimize-autoloader

# Cài đặt dependencies của Frontend (NPM) và Build Vite
RUN apk add --no-cache nodejs npm && \
    npm install && \
    npm run build

# Phân quyền cho Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 80
EXPOSE 80

# Lệnh khởi chạy: chạy migrate và khởi động server
CMD ["/start.sh"]