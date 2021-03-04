FROM php:7.2-fpm-stretch
USER root

RUN apt-get update --fix-missing

RUN apt-get install -y \
    curl \
    zip \
    zlib1g-dev \
    libicu-dev \
    libxslt-dev \
    libpq-dev \
    libpng-dev \
    libjpeg-dev \
    zlib1g-dev

# installing xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN docker-php-ext-configure gd \
        --with-png-dir="$(dirname $(whereis libpng))" \
        --with-jpeg-dir="$(dirname $(whereis libjpeg))" \
        --with-gd

RUN docker-php-ext-install \
        gd \
        pdo \
        pdo_pgsql \
        intl \
        xsl \
        zip

RUN apt-get update --fix-missing

RUN apt install -y poppler-utils
RUN apt install -y zbar-tools

#imageMagick для формирования пдф
RUN apt-get install -y \
    libmagickwand-dev \
    graphicsmagick-imagemagick-compat \
    --no-install-recommends \
    && \
    rm -rf /var/lib/apt/lists/*
RUN pecl install imagick-beta
RUN apt-get update
RUN apt-get install -y ghostscript
RUN apt-get install -y imagemagick

RUN sed -i 's/upload_max_filesize\ =\ .*$/upload_max_filesize = 300M/g' /usr/local/etc/php/php.ini-production
RUN sed -i 's/upload_max_filesize\ =\ .*$/upload_max_filesize = 300M/g' /usr/local/etc/php/php.ini-development

RUN sed -i 's/post_max_size\ =\ .*$/post_max_size = 300M/g' /usr/local/etc/php/php.ini-production
RUN sed -i 's/post_max_size\ =\ .*$/post_max_size = 300M/g' /usr/local/etc/php/php.ini-development

RUN sed -i 's/max_execution_time\ =\ .*$/max_execution_time = 300/g' /usr/local/etc/php/php.ini-production
RUN sed -i 's/max_execution_time\ =\ .*$/max_execution_time = 300/g' /usr/local/etc/php/php.ini-development

RUN sed -i 's/max_input_time\ =\ .*$/max_input_time = 300/g' /usr/local/etc/php/php.ini-production
RUN sed -i 's/max_input_time\ =\ .*$/max_input_time = 300/g' /usr/local/etc/php/php.ini-development

RUN sed -i 's/memory_limit\ =\ .*$/memory_limit = 512M/g' /usr/local/etc/php/php.ini-production
RUN sed -i 's/memory_limit\ =\ .*$/memory_limit = 512M/g' /usr/local/etc/php/php.ini-development

COPY ./xdebug-settings /root/xdebug-settings.ini
RUN cat /root/xdebug-settings.ini >> /usr/local/etc/php/php.ini-development
RUN cat /root/xdebug-settings.ini >> /usr/local/etc/php/php.ini-production


RUN apt-get install -y locales locales-all
ENV LC_ALL ru_RU.UTF-8
ENV LANG ru_RU.UTF-8
ENV LANGUAGE ru_RU.UTF-8

RUN apt-get install -y \
    cron \
    rsyslog

COPY ./entry.sh /root/entry.sh
COPY gost.so /usr/lib/x86_64-linux-gnu/engines-1.1/gost.so
COPY openssl.cnf /etc/ssl/openssl.cnf
RUN rm /app/Dockerfile

RUN curl -sS https://getcomposer.org/installer -o composer-setup.php
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN rm composer-setup.php
WORKDIR "/app"

# для фронта
RUN apt -y update;
RUN curl -sL https://deb.nodesource.com/setup_13.x | bash - ;
RUN apt install -y nodejs;

RUN chmod 755 /root/entry.sh

ENTRYPOINT ["/bin/bash", "/root/entry.sh"]
