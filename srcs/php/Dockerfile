FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    zip \
    unzip \
    sendmail \
    sendmail-bin \
    mailutils \
    && docker-php-ext-install pgsql pdo pdo_pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN echo "sendmail_path=/usr/sbin/sendmail -t -i" >> /usr/local/etc/php/conf.d/sendmail.ini

# Configure sendmail
RUN mkdir -p /etc/mail/authinfo && \
    echo "AuthInfo:smtp.gmail.com \"U:root\" \"I:esusagence@gmail.com\" \"P:nbxrjksvrwsspatq\"" > /etc/mail/authinfo/gmail && \
    makemap hash /etc/mail/authinfo.db < /etc/mail/authinfo/gmail && \
    chmod 600 /etc/mail/authinfo/gmail /etc/mail/authinfo.db && \
    m4 /etc/mail/sendmail.mc > /etc/mail/sendmail.cf

# Setup Gmail auth
RUN mkdir -p /etc/mail/authinfo && \
    echo "AuthInfo:smtp.gmail.com \"U:root\" \"I:esusagence@gmail.com\" \"P:nbxrjksvrwsspatq\"" > /etc/mail/authinfo/gmail && \
    makemap hash /etc/mail/authinfo.db < /etc/mail/authinfo/gmail && \
    chmod 600 /etc/mail/authinfo/gmail /etc/mail/authinfo.db && \
    m4 /etc/mail/sendmail.mc > /etc/mail/sendmail.cf

WORKDIR /var/www/html
COPY ./ /var/www/html/
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html
RUN a2enmod rewrite headers

EXPOSE 80

# Start sendmail before Apache
CMD service sendmail start && apache2-foreground