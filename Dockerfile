FROM php:8.2-apache

# Copia os arquivos para o Apache
COPY . /var/www/html/

# Instala extensões do PHP (como mysqli)
RUN docker-php-ext-install mysqli pdo pdo_mysql
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# Ajusta permissões do Apache
RUN chown -R www-data:www-data /var/www/html

# Ativa mod_rewrite (opcional, útil para URLs amigáveis)
RUN a2enmod rewrite

# Reinicia Apache (boa prática, mas o container já faz isso)
CMD ["apache2-foreground"]
