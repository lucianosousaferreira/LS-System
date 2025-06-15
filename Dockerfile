# Usa imagem oficial do PHP com Apache
FROM php:8.2-apache

# Instala dependências do sistema + Composer
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_mysql

# Instala o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Define o diretório de trabalho dentro do container
WORKDIR /var/www/html

# Copia os arquivos do projeto para o container
COPY . .

# Roda composer install ao construir a imagem
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Permissões (opcional, ajuste conforme seu app)
RUN chown -R www-data:www-data /var/www/html

# Expõe a porta do Apache
EXPOSE 80
