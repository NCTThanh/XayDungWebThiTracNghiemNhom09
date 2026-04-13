# ===================== STAGE 1: Build Assets (Node) =====================
FROM node:22-alpine AS builder

WORKDIR /app

# Copy package files trước để cache
COPY package*.json ./
RUN npm ci --frozen-lockfile

# Copy toàn bộ source
COPY . .

# Build Vite (Tailwind, JS, CSS...)
RUN npm run build

# ===================== STAGE 2: PHP + Nginx Runtime =====================
FROM richarvey/nginx-php-fpm:latest

# Copy code từ builder
COPY --from=builder /app /var/www/html

WORKDIR /var/www/html

# Cài Composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Thiết lập biến môi trường cho image
ENV WEBROOT=/var/www/html/public \
    PHP_UPLOAD_MAX_FILESIZE=100M \
    PHP_POST_MAX_SIZE=100M \
    PHP_MEMORY_LIMIT=512M

# Tạo thư mục Laravel
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache

# Phân quyền
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy nginx config (rất quan trọng)
COPY nginx.conf /etc/nginx/sites-available/default

EXPOSE ${PORT:-80}

CMD ["/start.sh"]