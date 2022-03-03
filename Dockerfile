ARG DOCKER_IMAGE=php:8.1-cli

FROM ${DOCKER_IMAGE}

LABEL maintainer="Aliaksei Panasik, unnfly@gmail.com"

RUN apt-get update \
    && apt-get -y install \
    apt-utils \
    libssl-dev \
    libxslt-dev \
    libpng-dev \
    g++ \
    gnupg \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install -j$(nproc) xsl zip opcache \
    && docker-php-ext-configure gd \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-configure hash --with-mhash

RUN pecl install ev && docker-php-ext-enable ev
