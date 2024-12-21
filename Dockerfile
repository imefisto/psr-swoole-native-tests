ARG PHP_VERSION
FROM phpswoole/swoole:${PHP_VERSION}

# Install git and other dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set up working directory
WORKDIR /var/www/html

# Copy composer files first to leverage Docker cache
COPY composer.json composer.lock* ./

# Install dependencies
RUN composer install --no-scripts --no-autoloader

# Copy the rest of the application
COPY . .

# Generate autoloader
RUN composer dump-autoload ; composer show ; php -v ; php -r "echo 'Swoole version: ' . swoole_version() . PHP_EOL;"

# Default command to run tests
CMD ["vendor/bin/phpunit"]
