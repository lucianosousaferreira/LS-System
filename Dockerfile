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
# Garantir que variáveis estejam disponíveis no PHP rodando no Apache
ENV DB_HOST=${DB_HOST}
ENV DB_USER=${DB_USER}
ENV DB_PASSWORD=${DB_PASSWORD}
ENV DB_NAME=${DB_NAME}
ENV DB_PORT=${DB_PORT}

