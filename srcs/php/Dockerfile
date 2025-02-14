FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libpq-dev \
    zip \
    unzip \
    vim \
    ssmtp \
    ca-certificates \
    && docker-php-ext-install pgsql pdo pdo_pgsql
    
# Configure ssmtp
RUN echo "root=esusagence@gmail.com\n\
mailhub=smtp.gmail.com:587\n\
AuthUser=esusagence@gmail.com\n\
AuthPass=cahankmunwvjtcqf\n\
UseTLS=YES\n\
UseSTARTTLS=YES\n\
FromLineOverride=YES\n\
hostname=localhost" > /etc/ssmtp/ssmtp.conf

# Configure revaliases
RUN echo "root:esusagence@gmail.com:smtp.gmail.com:587\n\
www-data:esusagence@gmail.com:smtp.gmail.com:587" > /etc/ssmtp/revaliases

# Configure PHP to use ssmtp
RUN echo "sendmail_path=/usr/sbin/ssmtp -t" > /usr/local/etc/php/conf.d/php-sendmail.ini


# Copy application files
COPY . /var/www/html
COPY php.ini /usr/local/etc/php/

# Enable necessary PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo pdo_mysql gd

# Set permissions
RUN chown -R www-data:www-data /var/www/html

RUN a2enmod rewrite
RUN service apache2 restart
# Set working directory
WORKDIR /var/www/html