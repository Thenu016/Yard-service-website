FROM php:8.0-apache

# Install and enable the mysqli PHP extension
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Ensure the uploads directory exists and is writable by Apache
RUN mkdir -p /var/www/html/uploads && chown -R www-data:www-data /var/www/html/uploads

# Expose port 80
EXPOSE 80
