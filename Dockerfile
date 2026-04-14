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

# Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-plugins --no-scripts

# Environment
ENV WEBROOT=/var/www/html/public \
    PHP_UPLOAD_MAX_FILESIZE=100M \
    PHP_POST_MAX_SIZE=100M \
    PHP_MEMORY_LIMIT=512M

# Tạo thư mục Laravel
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache

# Permission (đặt trước CMD)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy nginx config
COPY nginx.conf /etc/nginx/sites-available/default

EXPOSE ${PORT:-80}

# Clear Laravel cache khi start
CMD php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    /start.sh
    CMD php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    php artisan cache:clear && \
    /start.sh