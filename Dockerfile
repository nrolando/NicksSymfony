# Notes: You should break this up into a docker-compose and have mysql and apache in separate containers.

# Current Version 2.1
FROM ubuntu:18.04

RUN apt-get upgrade; apt-get update -y;

ENV DEBIAN_FRONTEND=noninteractive

# Install Helpers
RUN apt-get install -y curl zsh nano git htop vim unzip sudo; \
    sh -c "$(curl -fsSL https://raw.githubusercontent.com/robbyrussell/oh-my-zsh/master/tools/install.sh)"; \
    sed -i s^robbyrussell^example^g ~/.zshrc;

# Install SSH deps
RUN apt-get install -y openssh-server; \
    mkdir -p /root/.ssh; \
    touch /root/.ssh/authorized_keys;

# Install Apache
RUN apt-get install -y apache2; \
    a2enmod actions alias ssl rewrite headers setenvif; \
    rm -rf /var/www/html/*; \
    mkdir -p /var/www/html/public; \
    echo "ServerName localhost\n$(cat /etc/apache2/apache2.conf)" > /etc/apache2/apache2.conf;

### This was taken from magento dockerfile for making web root @ /var/www/html/pub
RUN tmp=$(mktemp); \
    def=/etc/apache2/sites-enabled/000-default.conf; \
    head -n 12 $def > $tmp; \
    echo "" >> $tmp; \
    echo "        <Directory /var/www/html/public>" >> $tmp; \
    echo "            RewriteEngine On" >> $tmp; \
    echo "            Options +FollowSymlinks" >> $tmp; \
    echo "            AllowOverride All" >> $tmp; \
    echo "            Require all granted" >> $tmp; \
    echo "        </Directory>" >> $tmp; \
    tail -n +13 $def >> $tmp; \
    cat $tmp > $def; \
    sed -i "s^DocumentRoot /var/www/html^DocumentRoot /var/www/html/public^g" /etc/apache2/sites-enabled/000-default.conf;

# Doing copy web files here after apache stuff
COPY assets /var/www/html/assets/
COPY bin /var/www/html/bin/
COPY config /var/www/html/config/
COPY migrations /var/www/html/migrations/
COPY src /var/www/html/src/
COPY templates /var/www/html/templates/
COPY .env .gitignore composer.* Dockerfile LICENSE package.json sfcourse_bu.sql symfony.lock webpack.config.js yarn.lock /var/www/html/
COPY public/.htaccess public/index.php /var/www/html/public/


# Install PHP
RUN apt-get install -y software-properties-common zip; \
    add-apt-repository -y ppa:ondrej/php; \
    apt-get update -y; \
    apt-get install -y php7.3 php7.3-cli php7.3-common php7.3-dev php7.3-curl \
        php7.3-mbstring php7.3-zip php7.3-mysql php7.3-xml php7.3-intl \
        php7.3-json libapache2-mod-php7.3 php7.3-bcmath php7.3-gd php7.3-soap; \
    a2enmod php7.3; \
    curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/local/bin --filename=composer; \
    sed -i "s^memory_limit = -1^memory_limit=1024M^g" /etc/php/7.3/cli/php.ini; \
    sed -i "s^memory_limit = 128M^memory_limit=1024M^g" /etc/php/7.3/apache2/php.ini; \
    sed -i "s^max_execution_time = 30^max_execution_time = 300^g" /etc/php/7.3/apache2/php.ini;

# Install MySQL Server. Create user, password, database. Restore db from backup file.
RUN apt-get install -y mysql-server mysql-client; \
    sed -i "s^bind-address		= 127.0.0.1^bind-address = 0.0.0.0^g" /etc/mysql/mysql.conf.d/mysqld.cnf; \
    service mysql start; \
    mysql -e "CREATE USER 'sfcourse_user'@'%' IDENTIFIED BY 'easypw123';"; \
    mysql -e "CREATE DATABASE sfcourse;"; \
    mysql -e "GRANT ALL PRIVILEGES ON sfcourse.* TO 'sfcourse_user'@'%';"; \
    mysql -e "FLUSH PRIVILEGES;"; \
	mysql -u root sfcourse < /var/www/html/sfcourse_bu.sql;

# Install NodeJS and Yarn
RUN curl -fsSL https://deb.nodesource.com/setup_12.x | sudo -E bash -; \
    echo "deb https://dl.yarnpkg.com/debian/ stable main" | sudo tee /etc/apt/sources.list.d/yarn.list; \
    curl -fsSL https://dl.yarnpkg.com/debian/pubkey.gpg | sudo apt-key add -; \
    apt-get update -y; \
    apt-get install -y nodejs yarn;

# Create web-user and add to group www-data, and set permssions
RUN useradd -d /home/web-user -m web-user -p easypw123; \
	usermod -a -G www-data web-user; \
	chown -R web-user:web-user /var/www/html/*; \
	# In these find statements, the 1st semi-colon is escaped so that it is a terminator for the exec statement and not the entire find statement
	# which the 2nd semi-colon does. https://stackoverflow.com/questions/19737525/find-type-f-exec-chmod-644
	find /var/www/html/ -type f -exec chmod 644 {} \;; \
	find /var/www/html/ -type d -exec chmod 755 {} \;;

WORKDIR /var/www/html

RUN composer install --no-interaction --no-dev --optimize-autoloader; \
    yarn install --non-interactive; \
    yarn run encore dev;

EXPOSE 80 443 3306

# Run mysql service, and apache in foreground
CMD service mysql start; apachectl -D FOREGROUND;
