FROM ghcr.io/colinmollenhour/docker-openmage-dev:8.2-apache

ENV APACHE_DOCUMENT_ROOT /var/www/html/pub/default

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf