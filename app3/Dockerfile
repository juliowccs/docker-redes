# Usando a imagem base do PHP com Apache
FROM php:8.1-apache

# Instala a extensão MySQLi necessária
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install mysqli pdo_mysql

# Copia os arquivos da aplicação para o container
COPY . /var/www/html/

# Configura permissões apropriadas
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Define o ServerName no Apache (opcional, útil em algumas configurações)
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Habilita o mod_rewrite do Apache
RUN a2enmod rewrite

# Reinicia o Apache para aplicar as alterações
CMD ["apache2-foreground"]
