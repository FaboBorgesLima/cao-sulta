FROM php:8.3-fpm

RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-enable pdo_mysql