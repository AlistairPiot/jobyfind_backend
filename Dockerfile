FROM php:8.2-fpm

# Installer les dépendances et extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libmariadb-dev-compat \
    libmariadb-dev \
    git \
    zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd \
    && docker-php-ext-enable pdo_mysql # Activer pdo_mysql

# Installer Composer
COPY --from=composer:2.5.5 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
