FROM php:8.4-cli

# Build arguments for dynamic user ID and group ID
ARG UID=1000
ARG GID=1000
ARG USER=developer

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    default-mysql-client \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
    && mkdir -p /etc/mysql/conf.d && printf '[client]\nssl = 0\n' > /etc/mysql/conf.d/disable-ssl.cnf

# Install PHP extensions required by Laravel
RUN docker-php-ext-install \
    pdo_mysql \
    pdo_pgsql \
    pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    opcache

# Copy Composer binary from official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user matching host UID/GID
RUN groupadd -g ${GID} ${USER} || true \
    && useradd -u ${UID} -g ${GID} -m -s /bin/bash ${USER} || true \
    && mkdir -p /home/${USER}/.composer \
    && chown -R ${USER}:${USER} /home/${USER}

# Set custom PHP configuration
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

# Set working directory
WORKDIR /var/www

# Copy composer files first for optimal Docker layer caching
COPY composer.json composer.lock ./

# Install Composer production dependencies without running scripts
RUN composer install --no-dev --no-interaction --no-scripts --prefer-dist --optimize-autoloader

# Copy application source code
COPY --chown=${USER}:${USER} . /var/www

# Run composer dump-autoload (executes package:discover since app files now exist)
RUN composer dump-autoload --optimize

# Ensure correct permissions for storage, cache, and entrypoint
RUN chown -R ${USER}:${USER} /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache \
    && chmod +x /var/www/docker/entrypoint.sh

# Switch to non-root user
USER ${USER}

# Expose port for "php artisan serve"
EXPOSE 8000

# Run container via entrypoint script
CMD ["/var/www/docker/entrypoint.sh"]
