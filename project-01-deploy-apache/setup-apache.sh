#!/bin/bash

sudo apt update -y
sudo apt install apache2 -y
sudo systemctl start apache2
sudo systemctl enable apache2

# Copy index.html to web root
sudo cp index.html /var/www/html/index.html

