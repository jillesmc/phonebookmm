FROM php:7.2-apache

RUN apt-get update
#RUN apt-get install -y libicu-dev
#RUN apt-get install -y libxml2-dev
RUN apt-get install -y libcurl4-openssl-dev
RUN apt-get install -y mysql-client
RUN apt-get install -y curl
#RUN apt-get install -y build-essential
RUN apt-get install -y gnupg
#RUN apt-get install -y software-properties-common
#RUN apt-get install -y locales-all
RUN docker-php-source extract

RUN docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd
RUN docker-php-ext-install pdo_mysql
#RUN docker-php-ext-install dom
#RUN docker-php-ext-install simplexml
#RUN docker-php-ext-install curl
#RUN docker-php-ext-install hash
#RUN docker-php-ext-install intl
#RUN docker-php-ext-install xml
#RUN docker-php-ext-install mbstring
RUN docker-php-ext-install json

RUN docker-php-source delete
# Install nodejs
RUN curl -sL https://deb.nodesource.com/setup_8.x | bash -
RUN apt-get update && apt-get install -y nodejs

# Install php composer
RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer

# Put apache config for phonebook
COPY .docker/certs/local/phonebook.com.br.crt /etc/ssl/certs/server.crt
COPY .docker/certs/local/device.key /etc/ssl/private/server.key

COPY .docker/apache2.conf /etc/apache2/sites-available/00-pb.conf
COPY .docker/apache2-ssl.conf /etc/apache2/sites-available/00-pb-ssl.conf

RUN a2dissite 000-default.conf && a2dissite default-ssl.conf && a2ensite 00-pb.conf && a2ensite 00-pb-ssl.conf
RUN a2enmod headers rewrite ssl

# Change uid and gid of apache to docker user uid/gid
RUN usermod -u 1000 www-data && groupmod -g 1001 www-data