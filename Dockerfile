FROM php:8.1-apache

RUN a2enmod rewrite

WORKDIR /usr/local/apache2/htdocs/

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions pdo pdo_mysql gd zip exif

RUN docker-php-ext-install mysqli pdo pdo_mysql exif 

# Setup Apache2 config
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# CMD [ "php", "./index.php" ]

RUN echo "ServerName PROJETOS" >> /etc/apache2/apache2.conf

# RUN a2dissite 000-default
# RUN a2ensite 000-default.conf
# RUN service apache2 restart

# RUN apt-get update
# RUN apt-get install  libapache2-mod-php -y

# RUN service apache2 restart
USER www-data

WORKDIR /var/www/html