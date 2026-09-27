# Base image with PHP 8.2 and Apache web server
FROM php:8.2-apache

# Install PDO MySQL extension for database connectivity
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache rewrite module
RUN a2enmod rewrite

# Set Apache DocumentRoot to the application directory
WORKDIR /var/www/html

# Copy application source code
COPY . /var/www/html/

# Set permissions for Apache
RUN chown -R www-data:www-data /var/www/html

# Expose standard HTTP port
EXPOSE 80

CMD ["apache2-foreground"]
