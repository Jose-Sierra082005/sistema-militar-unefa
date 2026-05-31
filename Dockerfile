# ==============================================================================
#  Dockerfile para Despliegue de Laravel en Render.com
#  PHP 8.4 + Apache ÔÇö Optimizado para producci├│n
# ==============================================================================

FROM php:8.4-apache

# 1. Instalar dependencias del sistema (todas las librer├¡as de desarrollo necesarias)
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libsqlite3-dev \
    unzip \
    git \
    zip \
    curl \
    && rm -rf /var/lib/apt/lists/*

# 2. Configurar e instalar extensiones PHP (sin exif ni pcntl para evitar conflictos)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo \
        pdo_mysql \
        pdo_sqlite \
        mbstring \
        zip \
        opcache \
        bcmath

# 3. Configuraci├│n de OPcache para producci├│n
RUN { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=4000'; \
    echo 'opcache.revalidate_freq=2'; \
    echo 'opcache.fast_shutdown=1'; \
} > /usr/local/etc/php/conf.d/opcache.ini

# 4. Habilitar mod_rewrite para rutas amigables de Laravel
RUN a2enmod rewrite

# 5. Redirigir DocumentRoot de Apache al directorio /public de Laravel
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/000-default.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

# 6. Copiar Composer desde imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 7. Directorio de trabajo
WORKDIR /var/www/html

# 8. Copiar archivos del proyecto (vendor excluido v├¡a .dockerignore)
COPY . .

# 9. Crear .env desde .env.example para que el build no falle
RUN cp .env.example .env

# 10. Instalar dependencias PHP (solo producci├│n)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# 11. Generar clave y cachear configuraci├│n de Laravel
RUN php artisan key:generate --force \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# 12. Permisos correctos para Apache
RUN mkdir -p storage/framework/{cache,sessions,views} \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 13. Exponer puerto 80
EXPOSE 80

# 14. Al arrancar: crear SQLite, migrar, recargar config y lanzar Apache
CMD bash -c "\
    touch /var/www/html/database/database.sqlite && \
    chown www-data:www-data /var/www/html/database/database.sqlite && \
    php artisan migrate --force --no-interaction && \
    php artisan config:clear && \
    php artisan config:cache && \
    apache2-foreground"
