# Usar la imagen base de PHP 8.2
FROM php:8.2-cli

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo pdo_mysql

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configurar directorio de trabajo
WORKDIR /app

# Copiar los archivos de la aplicación al contenedor
COPY . .

# Instalar dependencias de Composer (esto asegurará que todas las dependencias de tu proyecto se instalen)
RUN composer install --no-dev --optimize-autoloader

# Exponer el puerto en el que la aplicación estará escuchando
EXPOSE 8080

# Ejecutar el servidor
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]