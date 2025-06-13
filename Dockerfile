# Imagem base com Apache + PHP + extensões
FROM php:8.2-apache

# Instala extensões necessárias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copia os arquivos para o diretório padrão do Apache
COPY . /var/www/html/

# Dá permissão de leitura/escrita
RUN chown -R www-data:www-data /var/www/html

# Habilita o módulo reescrita (se necessário)
RUN a2enmod rewrite

# Define a porta esperada pelo Render
EXPOSE 8080

# Muda a porta padrão do Apache para 8080
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf
