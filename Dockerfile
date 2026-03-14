FROM php:8.2-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) gd pdo pdo_mysql mysqli zip

# Enable Apache modules
RUN a2enmod rewrite headers

# Increase PHP upload limits
RUN echo "upload_max_filesize = 10M" > /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size = 12M" >> /usr/local/etc/php/conf.d/uploads.ini

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Create uploads directory and set permissions
RUN mkdir -p /var/www/html/uploads && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    chmod -R 777 /var/www/html/uploads

# Configure Apache
RUN echo '<Directory /var/www/html>' > /etc/apache2/conf-available/app.conf && \
    echo '    Options Indexes FollowSymLinks' >> /etc/apache2/conf-available/app.conf && \
    echo '    AllowOverride All' >> /etc/apache2/conf-available/app.conf && \
    echo '    Require all granted' >> /etc/apache2/conf-available/app.conf && \
    echo '</Directory>' >> /etc/apache2/conf-available/app.conf && \
    a2enconf app

# Security headers
RUN echo 'ServerSignature Off' >> /etc/apache2/apache2.conf && \
    echo 'ServerTokens Prod' >> /etc/apache2/apache2.conf && \
    echo 'TraceEnable Off' >> /etc/apache2/apache2.conf

# Expose port 80
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

# Entrypoint: ensure uploads writable at runtime then start Apache
CMD ["sh", "-c", "mkdir -p /var/www/html/uploads && chmod 777 /var/www/html/uploads && apache2-foreground"]
