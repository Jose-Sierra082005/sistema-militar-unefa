# ==============================================================================
#  Dockerfile para Despliegue de Laravel 13 en Render.com
#  Usa PHP 8.4 con Apache optimizado para producción
# ==============================================================================

FROM php:8.4-apache

# 1. Instalar dependencias del sistema y extensiones de PHP necesarias para Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip opcache

# 2. Habilitar módulo mod_rewrite de Apache para las rutas amigables de Laravel
RUN a2enmod rewrite

# 3. Configurar el DocumentRoot de Apache para que apunte a /public de Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 4. Copiar Composer desde la imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 5. Establecer directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

# 6. Copiar los archivos del proyecto al contenedor
COPY . .

# 7. Ejecutar Composer para instalar dependencias de producción
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 8. Asignar permisos del servidor web (www-data) a las carpetas de Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 9. Exponer puerto 80
EXPOSE 80

# 10. Arrancar el servidor Apache en primer plano
CMD ["apache2-foreground"]
