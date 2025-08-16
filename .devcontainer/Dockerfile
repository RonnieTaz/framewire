# Start from the official Microsoft PHP 8.3 dev container image.
# It includes PHP, Composer, PECL, and other common dev tools.
FROM mcr.microsoft.com/devcontainers/php:8.4-bookworm
LABEL authors="vicent"

# Install system dependencies required for certain PHP extensions.
RUN sudo apt-get update && export DEBIAN_FRONTEND=noninteractive \
    && sudo apt-get -y install --no-install-recommends \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libicu-dev \
        libpq-dev \
    && sudo apt-get clean \
    && sudo rm -rf /var/lib/apt/lists/*

# Use the built-in script to install common PHP extensions.
RUN docker-php-ext-install \
    pdo_mysql \
    pdo_pgsql \
    bcmath \
    intl \
    opcache \
    sockets \
    zip \
    pcntl \
    posix

# Configure and install the GD extension for image manipulation.
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Set the working directory for your project.
WORKDIR /workspaces/${containerWorkspaceFolder}
