FROM richarvey/nginx-php-fpm:latest

# Copy code
COPY . /var/www/html

WORKDIR /var/www/html

# Thiết lập biến
ENV WEBROOT=/var/www/html/public
ENV PHP_UPLOAD_MAX_FILESIZE=100M
ENV PHP_POST_MAX_SIZE=100M
ENV PHP_MEMORY_LIMIT=512M

# Cài system dependencies cho Tailwind Oxide + Node
RUN apk add --no-cache \
    nodejs \
    npm \
    build-base \
    python3 \
    py3-pip \
    && npm install

# Build Vite (Tailwind)
RUN npm run build

# Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Tạo thư mục Laravel
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache

# Phân quyền
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE ${PORT:-80}

CMD ["/start.sh"]