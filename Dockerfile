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

# Configure PHP Upload Limits
RUN echo "upload_max_filesize = 10M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 10M" >> /usr/local/etc/php/conf.d/uploads.ini
 
# Install Node.js (Current Stable)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs
 
# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
 
# Set working directory
WORKDIR /var/www/html
 
# Copy Nginx config
COPY docker/nginx.conf /etc/nginx/sites-available/default
 
# Copy Composer dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-progress

# Copy Node dependencies
COPY package.json package-lock.json ./
RUN npm install

# Copy application code
COPY . /var/www/html

# Build frontend assets
RUN npm run build
 
# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
 
# Make startup script executable
RUN chmod +x /var/www/html/docker/startup.sh
 
# Expose port 80
EXPOSE 80
 
# Start the application
CMD ["/var/www/html/docker/startup.sh"]
