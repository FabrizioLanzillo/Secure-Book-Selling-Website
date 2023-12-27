#!/bin/bash

apt-get update -y

apt-get install -y vim
apt-get install -y net-tools

apt-get install -y libmysqli-dev
docker-php-ext-install mysqli
docker-php-ext-enable mysqli

a2enmod ssl
a2ensite bookselling.conf
service apache2 reload

# Avvia Apache in foreground (non in background)
apache2-foreground
