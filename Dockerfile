FROM php:8.2-fpm

#COPY composer.json /var/www/

WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libicu-dev \
    libzip-dev \
    libxml2-dev \
    libpq-dev \
    g++ \
    libonig-dev \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    unzip \
    git \
    curl \
    lua-zlib-dev \
    libmemcached-dev

# clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# Install extensions
RUN docker-php-ext-install intl opcache pdo pdo_pgsql pgsql zip exif pcntl mbstring -j$(nproc) gd sockets soap

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

COPY . /var/www

COPY --chown=www:www . /var/www

# change current user to www
USER www

# expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
