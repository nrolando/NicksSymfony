FROM php:8.2-apache

# System deps
RUN apt-get update && apt-get install -y \
    git unzip curl nano vim \
    libzip-dev libicu-dev libpng-dev libjpeg-dev libfreetype6-dev \
    libxml2-dev \
    python3 make g++ \
    && curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql intl zip bcmath gd soap

COPY .narden/apache/https-only.conf /etc/apache2/conf-available/https-only.conf
COPY .narden/apache/ssl.conf        /etc/apache2/sites-available/ssl.conf
COPY .narden/entrypoint.sh /entrypoint.sh

RUN a2enmod ssl rewrite headers \
    && a2ensite ssl \
    && ln -s /usr/bin/python3 /usr/bin/python \
    && chmod +x /entrypoint.sh \
    && rm -rf /var/lib/apt/lists/*

# Apache configs
#COPY .narden/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
#COPY .narden/apache/ssl.conf /etc/apache2/sites-available/ssl.conf

# Needed for mounting with Windows host machine (git doesn't like directory permissions)
RUN git config --global --add safe.directory /var/www/html

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# App code
#COPY . /var/www/html

WORKDIR /var/www/html

# Symfony build
#RUN composer install --no-dev --optimize-autoloader \
 #&& npm install \
 #&& npm run encore production

EXPOSE 80 443

ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]
