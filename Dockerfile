FROM php:8.2.2-fpm

# Installe les dépendances et extensions PHP nécessaires
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev git zip && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd pdo pdo_mysql

# Installe Composer
COPY --from=composer:2.5.5 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
