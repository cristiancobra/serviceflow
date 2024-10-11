FROM php:8.2-apache

RUN a2enmod rewrite

WORKDIR /usr/local/apache2/htdocs/

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions pdo pdo_mysql gd zip exif intl

RUN docker-php-ext-install mysqli pdo pdo_mysql exif 

# Setup Apache2 config
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# CMD [ "php", "./index.php" ]

RUN echo "ServerName SERVICEFLOW" >> /etc/apache2/apache2.conf

# Instalação do Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Define o diretório de cache do Composer
ENV COMPOSER_HOME /tmp/composer

# Define o usuário padrão
USER 1000:1000

WORKDIR /var/www/html