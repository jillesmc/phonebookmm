FROM php:7.2-apache

RUN apt-get update
RUN apt-get install -y libcurl4-openssl-dev
RUN apt-get install -y mysql-client
RUN apt-get install -y curl
RUN apt-get install -y gnupg
RUN docker-php-source extract

RUN docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install json

RUN docker-php-source delete

# Put apache config for phonebook
COPY .docker/certs/local/phonebook.com.br.crt /etc/ssl/certs/server.crt
COPY .docker/certs/local/device.key /etc/ssl/private/server.key

COPY .docker/apache2.conf /etc/apache2/sites-available/00-pb.conf
COPY .docker/apache2-ssl.conf /etc/apache2/sites-available/00-pb-ssl.conf

RUN a2dissite 000-default.conf && a2dissite default-ssl.conf && a2ensite 00-pb.conf && a2ensite 00-pb-ssl.conf
RUN a2enmod headers rewrite ssl

# Change uid and gid of apache to docker user uid/gid
RUN usermod -u 1000 www-data && groupmod -g 1001 www-data