FROM php:8.2-apache

# 1. Habilitar mod_rewrite para que las rutas de Laravel funcionen
RUN a2enmod rewrite

# 2. Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev

# 3. Instalar extensiones de PHP necesarias
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 4. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Definir directorio de trabajo
WORKDIR /var/www/html

# 6. Copiar el código del proyecto
COPY . .

# 7. Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader

# 8. APUNTAR APACHE A LA CARPETA /public DE LARAVEL (Soluciona el pantallazo de phpinfo)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 9. Asignar permisos a storage y cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 10. Exponer el puerto 80 de producción
EXPOSE 80

# 11. Optimizar Laravel y arrancar Apache en primer plano
CMD php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    apache2-foreground