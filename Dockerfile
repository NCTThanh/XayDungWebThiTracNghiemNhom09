FROM php:8.2-fpm

# Cài extension cần thiết
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    npm \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Cài Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Fix lỗi upload size (❗ sửa 100MM -> 100M)
RUN echo "upload_max_filesize=100M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size=100M" >> /usr/local/etc/php/conf.d/uploads.ini

# Set working dir
WORKDIR /var/www

# Copy source code
COPY . .

# ❌ KHÔNG dùng prestissimo (đã bỏ hoàn toàn)

# Cài dependency PHP
RUN composer install --no-dev --optimize-autoloader

# Build frontend (nếu có Vite)
RUN npm install && npm run build

# Set quyền (quan trọng với Laravel)
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# Copy nginx config
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Cài nginx
RUN apt-get install -y nginx

# Expose port
EXPOSE 80

# Start services
CMD service nginx start && php-fpm