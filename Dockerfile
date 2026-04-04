FROM php:8.2-cli

# Instalar extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Copiar proyecto
WORKDIR /app
COPY . .

# Servir app
CMD php -S 0.0.0.0:$PORT -t public