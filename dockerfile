# Gunakan image PHP dengan Apache
FROM php:8.1-apache

# Install dependensi yang dibutuhkan untuk Composer
RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Download dan install Composer secara manual
# RUN curl -sS https://getcomposer.org/installer | php && \
# mv composer.phar /usr/local/bin/composer

# Download dan install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set document root ke project/
WORKDIR /var/www/html
COPY project/ /var/www/html/

# Copy semua file src ke dalam /var/www/src
#COPY src/ /var/www/src/

# Install dependensi PHP menggunakan Composer
#RUN composer install --no-dev --prefer-dist --optimize-autoloader
RUN composer require kreait/firebase-php
RUN composer require vlucas/phpdotenv

RUN ls -al

# Pastikan index.php ditemukan sebagai halaman utama
RUN echo "DirectoryIndex index.php" >> /etc/apache2/apache2.conf

# Set ServerName untuk menghindari error
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Expose port 80 (default untuk Apache)
EXPOSE 80

# Start Apache ketika container berjalan
CMD ["apache2-foreground"]