FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql

# make Apache listen on the PORT env var that Cloud Run provides
RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf /etc/apache2/sites-enabled/000-default.conf

COPY src/ /var/www/html/