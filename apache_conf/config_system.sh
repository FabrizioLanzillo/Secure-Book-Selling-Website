#!/bin/bash
# update system
apt-get update -y
apt install unzip
# install base programs
apt-get install -y vim
apt-get install -y net-tools
# install and configure compose and its tools
curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
HASH=`curl -sS https://composer.github.io/installer.sig`
php -r "if (hash_file('SHA384', '/tmp/composer-setup.php') === '$HASH') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
(cd /var/www/html && composer require phpmailer/phpmailer)
(cd /var/www/html && composer install)
# this copy all the e-book file from the e-books-mounted volume mounted with the docker-compose file
# this is a trick in order to change the file owner of the directory and of the files inside it
(cd /home/bookselling && mkdir e-books)
(cd /home/bookselling && cp e-books-mounted/* e-books)

# install mysql tools
apt-get install -y libmysqli-dev
docker-php-ext-install mysqli
docker-php-ext-enable mysqli
# apache configuration
a2enmod ssl
a2ensite bookselling.conf
service apache2 reload
# start of Apache in foreground
apache2-foreground
