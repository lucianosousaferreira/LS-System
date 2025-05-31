# Usa uma imagem oficial do PHP com Apache
FROM php:8.2-apache

# Instala extensões necessárias (ex: mysqli)
RUN docker-php-ext-install mysqli

# Copia o conteúdo do projeto para a pasta do Apache
COPY . /var/www/html/

# Dá permissão ao Apache
RUN chown -R www-data:www-data /var/www/html

# Ativa o módulo reescrita (se necessário)
RUN a2enmod rewrite

# Porta padrão do Apache
EXPOSE 80
