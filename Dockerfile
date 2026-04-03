FROM php:8.4-apache

RUN apt-get update && apt-get install -y unzip
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install pdo pdo_mysql
RUN a2enmod rewrite

COPY apache.conf /etc/apache2/sites-enabled/000-default.conf
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

COPY composer.json composer.lock /var/www/html/
COPY src/ /var/www/html/
RUN composer install --no-dev --working-dir=/var/www/html && ls /var/www/html/

CMD ["/entrypoint.sh"]