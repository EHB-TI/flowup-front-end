FROM php:7.4-fpm

# Copy composer.lock and composer.json
COPY composer.lock composer.json /var/www/

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    libonig-dev \
    curl

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl sockets
RUN docker-php-ext-configure gd --with-jpeg=/usr/include/ --with-freetype=/usr/include/
RUN docker-php-ext-install gd

# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy existing application directory contents
COPY . /var/www

# Install composer
RUN curl -s https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy existing application directory permissions
COPY --chown=www:www . /var/www

# Change current user to www
USER www

# Expose port 8080 and start php-fpm server
#EXPOSE 8080
# Expose port 4433 and start php-fpm server
EXPOSE 4433
RUN chmod +x dockerStart.sh
ENTRYPOINT [ "./dockerStart.sh" ]

#ENTRYPOINT [ "/bin/bash" ]