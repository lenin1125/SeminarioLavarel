# Usamos PHP 8.4 para cumplir los requerimientos de Composer
FROM php:8.4-apache

# 1. Habilitar mod_rewrite de Apache
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

# 3. Instalar extensiones de PHP necesarias (incluyendo pdo_mysql)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 4. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Definir directorio de trabajo
WORKDIR /var/www/html

# 6. Copiar el código del proyecto
COPY . .

# 7. Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# 8. Apuntar Apache a la carpeta /public de Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 9. Asignar permisos a storage y cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 10. Exponer el puerto 80
EXPOSE 80

# 11. Ejecutar migraciones limpias, crear enlace simbólico, optimizar cache y arrancar Apache
CMD php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    php artisan storage:link && \
    php artisan migrate:fresh --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    apache2-foreground