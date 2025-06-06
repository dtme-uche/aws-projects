#!/bin/bash
sudo apt update
sudo apt install apache2 php libapache2-mod-php php-mysql -y
sudo systemctl restart apache2

