# Imagen base con PHP 8.3 y FPM (FastCGI Process Manager, lo que usa Nginx
# para ejecutar PHP). Usamos 8.3 porque Laravel 11.54 lo soporta perfectamente
# y nos da margen frente a 8.2.
FROM php:8.4-fpm

# Dependencias del sistema necesarias para compilar extensiones PHP
# y para que Composer pueda clonar repos git si hace falta.
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Extensiones de PHP que Laravel + MySQL + Redis necesitan:
# - pdo_mysql: para que Eloquent hable con MySQL
# - mbstring, xml, zip, bcmath: requeridas internamente por Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip
RUN pecl install redis && docker-php-ext-enable redis

# Instalamos Composer copiando el binario oficial desde su imagen.
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Directorio de trabajo dentro del contenedor: aquí vivirá el código.
WORKDIR /var/www

# Copiamos todo el código del proyecto al contenedor.
COPY . .

# Instalamos las dependencias de Composer.
# No usamos --no-dev porque necesitaremos PHPUnit/Pest para los tests.
RUN composer install

# Permisos: Laravel necesita poder escribir en storage/ y bootstrap/cache/
# (logs, cache de vistas, sesiones, etc.)
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Puerto que expone PHP-FPM (Nginx se conectará aquí)
EXPOSE 9000

CMD ["php-fpm"]