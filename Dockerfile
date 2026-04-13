# ===================== STAGE 1: Build Assets (Node) =====================
FROM node:22-alpine AS builder

WORKDIR /app

# Copy package files
COPY package*.json ./

# ←←← SỬA Ở ĐÂY
RUN npm install

# Copy toàn bộ source
COPY . .

# Build Vite
RUN npm run build

# ===================== STAGE 2: PHP + Nginx Runtime =====================
FROM richarvey/nginx-php-fpm:latest

COPY --from=builder /app /var/www/html

WORKDIR /var/www/html

RUN composer install --no-dev --optimize-autoloader --no-interaction

ENV WEBROOT=/var/www/html/public \
    PHP_UPLOAD_MAX_FILESIZE=100M \
    PHP_POST_MAX_SIZE=100M \
    PHP_MEMORY_LIMIT=512M

RUN mkdir -p storage/framework/{sessions,views,cache} \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

COPY nginx.conf /etc/nginx/sites-available/default

EXPOSE ${PORT:-80}

CMD ["/start.sh"]