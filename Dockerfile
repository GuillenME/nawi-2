# 1️⃣ Base PHP + Apache
FROM php:8.3-apache

# 2️⃣ Instalar dependencias del sistema
RUN apt -y update && apt -y install \
    git zip unzip curl libpng-dev libonig-dev libxml2-dev libzip-dev \
    nodejs npm sqlite3 \
    && apt clean && rm -rf /var/lib/apt/lists/*

# 3️⃣ Instalar extensiones PHP usando install-php-extensions
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
    pdo pdo_mysql bcmath gd zip exif pcntl mbstring ctype xml openssl tokenizer curl apcu

# 4️⃣ Copiar configuración de Apache **antes de copiar el código**
COPY docker/laravel.conf /etc/apache2/sites-available/000-default.conf

COPY docker/apcu.ini /usr/local/etc/php/conf.d/apcu.ini


# 5️⃣ Habilitar mod_rewrite
RUN a2enmod rewrite

# 6️⃣ Copiar código de Laravel
COPY . /var/www/html

# 7️⃣ Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# 8️⃣ Node y Vite
RUN npm install && npm run build

# 9️⃣ Cambiar permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 755 /var/www/html/public

# 🔟 Exponer puerto Apache
EXPOSE 80

# CMD ya está manejado por php:apache
