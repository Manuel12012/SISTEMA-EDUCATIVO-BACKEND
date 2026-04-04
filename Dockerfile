FROM php:8.2-cli

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo pdo_mysql

# Configurar directorio
WORKDIR /app
COPY . .

# Exponer puerto
EXPOSE 8080

# Ejecutar servidor
CMD php -S 0.0.0.0:$PORT -t public