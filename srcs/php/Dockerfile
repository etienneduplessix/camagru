FROM php:8.2-apache

# Install PostgreSQL extensions
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pgsql pdo pdo_pgsql

# Copy application files
COPY ./app /var/www/html/app
COPY ./public /var/www/html/public

# Set correct ownership and permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Enable mod_rewrite for Apache (important for .htaccess)
RUN a2enmod rewrite

# Restart Apache to apply changes
CMD ["apache2-foreground"]
