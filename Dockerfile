FROM php:8.2-apache

# Copia os arquivos para o Apache
COPY . /var/www/html/

# Instala extensões do PHP (como mysqli)
RUN docker-php-ext-install mysqli pdo pdo_mysql
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
