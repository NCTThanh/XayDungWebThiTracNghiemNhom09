FROM php:8.2-apache

# Cài đặt các thư viện hệ thống cần thiết
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl

# Xóa cache để giảm dung lượng image
RUN apt-get clean && rm -rf /var/list/apt/lists/*

# Cài đặt PHP extensions (Đã thêm zip)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Bật Apache Rewrite cho Laravel
RUN a2enmod rewrite

# Copy code vào thư mục web
COPY . /var/www/html

# Cấu hình Document Root của Apache vào thư mục /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Cài đặt dependencies (Bỏ qua kiểm tra platform nếu cần, nhưng ở trên đã cài đủ zip rồi)
RUN composer install --no-dev --optimize-autoloader

# Cấp quyền cho thư mục storage và cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80