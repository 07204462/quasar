FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql

COPY ./public /var/www/html
WORKDIR /var/www/html

RUN a2enmod rewrite
