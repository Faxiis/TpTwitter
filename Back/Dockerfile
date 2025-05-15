# @todo - Utiliser une image plus sécurisée ?
FROM debian:11.2-slim

LABEL description="Debian 11.2 + Apache 2 + PHP 8.1"

ENV DEBIAN_FRONTEND noninteractive

RUN apt update && apt install -y

# Apache
RUN apt install -y apache2
RUN mv /var/www/html /var/www/application

# @todo - Sécuriser le vhost
COPY ./config/apache2/000-default.conf /etc/apache2/sites-enabled/000-default.conf

# PHP
RUN apt install -y \
    apt-transport-https \
    ca-certificates \
    gnupg2 \
    lsb-release \
    wget

RUN echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/sury-php.list
RUN wget -qO - https://packages.sury.org/php/apt.gpg | apt-key add -

RUN apt update && apt install -y \
    php8.2-fpm \
    php8.2-intl \
    php8.2-mysql \
    php8.2-xml \
    php8.2-zip

RUN a2enmod rewrite
RUN a2enmod proxy_fcgi
RUN a2enconf php8.2-fpm

# Installer curl et Composer
RUN apt-get update && apt-get install -y curl
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/application

# Démarrage des services
COPY ./config/os/start.sh /opt/start.sh
RUN chmod +x /opt/start.sh
ENTRYPOINT ["/opt/start.sh"]