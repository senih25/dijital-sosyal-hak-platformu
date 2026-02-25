# Dockerfile

# Use PHP 8.1 as the base image
FROM php:8.1-cli

# Set the working directory
WORKDIR /app

# Install dependencies required for Composer
RUN apt-get update && \
    apt-get install -y libzip-dev unzip && \
    docker-php-ext-install zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy the application code
COPY . .

# Install application dependencies using Composer
RUN composer install

# DevCycle SDK Configuration for Development Environment
ENV DEVCYCLE_ENV=development
ENV DEVCYCLE_SDK_KEY=your_devcycle_sdk_key

# Command to run the application (if applicable)
CMD ["php", "your_script.php"]