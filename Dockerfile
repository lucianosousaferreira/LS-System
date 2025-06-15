# Imagem base com Apache + PHP + extensões
FROM php:8.2-apache

# Instala dependências de sistema e extensões PHP
RUN apt-get update && apt-get install -y \
    unzip \
    curl \
    git \
    && docker-php-ext-install mysqli pdo pdo_mysql

# Instala o Composer globalmente
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copia os arquivos do projeto para o diretório do Apache
COPY . /var/www/html/

# Executa o Composer install (assume que composer.json está no projeto)
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --working-dir=/var/www/html

# Dá permissão de leitura/escrita ao Apache
RUN chown -R www-data:www-data /var/www/html

# Habilita o módulo rewrite (para URLs amigáveis, ex: Laravel, Slim etc)
RUN a2enmod rewrite

# Define a porta que o Render espera
EXPOSE 8080

# Altera Apache para escutar na porta 8080
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf
