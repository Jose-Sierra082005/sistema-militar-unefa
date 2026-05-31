# ==============================================================================
#  Dockerfile para Despliegue de Laravel en Render.com
#  PHP 8.4 + Apache — Optimizado para producción con SQLite/MySQL
# ==============================================================================

FROM php:8.4-apache

# 1. Instalar dependencias del sistema y extensiones PHP requeridas por Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    unzip \
    git \
    zip \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql pdo_sqlite mbstring zip opcache exif pcntl bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Configuración de OPcache para producción
RUN { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=4000'; \
    echo 'opcache.revalidate_freq=2'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.enable_cli=1'; \
} > /usr/local/etc/php/conf.d/opcache-recommended.ini

# 3. Habilitar mod_rewrite para rutas amigables de Laravel
RUN a2enmod rewrite

# 4. Redirigir el DocumentRoot de Apache al directorio /public de Laravel
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/000-default.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

# 5. Copiar Composer desde la imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 6. Establecer directorio de trabajo
WORKDIR /var/www/html

# 7. Copiar archivos del proyecto (vendor excluido vía .dockerignore)
COPY . .

# 8. Crear .env desde .env.example para que Laravel arranque correctamente
#    Las variables reales serán inyectadas por Render en tiempo de ejecución
RUN cp .env.example .env

# 9. Instalar dependencias de Composer (solo producción)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# 10. Generar clave de aplicación y optimizar Laravel
RUN php artisan key:generate --force \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# 11. Crear directorios necesarios y asignar permisos al usuario de Apache
RUN mkdir -p storage/framework/{cache,sessions,views} \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 12. Exponer puerto 80
EXPOSE 80

# 13. Script de inicio:
#     1) Crea el archivo SQLite si no existe
#     2) Ejecuta migraciones pendientes
#     3) Recarga config con las variables de entorno inyectadas por Render
#     4) Arranca Apache en primer plano
CMD bash -c "\
    touch /var/www/html/database/database.sqlite && \
    chown www-data:www-data /var/www/html/database/database.sqlite && \
    php artisan migrate --force --no-interaction && \
    php artisan config:clear && \
    php artisan config:cache && \
    apache2-foreground"
