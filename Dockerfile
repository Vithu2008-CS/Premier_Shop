FROM dunglas/frankenphp:latest-php8.2

# Install the pdo_mysql extension so Laravel can connect to MySQL
RUN docker-php-ext-install pdo_mysql

# Copy application source code
COPY . /app

# Set correct permissions for Laravel's writable directories
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache
