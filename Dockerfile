FROM php:8.2-apache

# Instalar dependencias para GD
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Instalar extensiones necesarias para MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# (Opcional) habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Copiar el código al contenedor
WORKDIR /var/www/html
COPY . .