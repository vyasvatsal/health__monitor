FROM php:8.2-fpm
 
# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libpq-dev \
    libicu-dev \
    nginx
 
# Install PHP extensions
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd intl zip
 
# Install Node.js (Current Stable)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs
 
# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
 
# Set working directory
WORKDIR /var/www/html
 
# Copy Nginx config
COPY docker/nginx.conf /etc/nginx/sites-available/default
 
# Copy application code
COPY . /var/www/html
 
# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader
 
# Install NPM dependencies and build assets
RUN npm install && npm run build
 
# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
 
# Make startup script executable
RUN chmod +x /var/www/html/docker/startup.sh
 
# Expose port 80
EXPOSE 80
 
# Start the application
CMD ["/var/www/html/docker/startup.sh"]
