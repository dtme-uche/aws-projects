#!/bin/bash

# Update system packages
sudo apt update -y

# Install Apache, PHP, MariaDB, and other required packages
sudo apt install -y apache2 php libapache2-mod-php php-mysql mariadb-server unzip wget

# Start and enable Apache
sudo systemctl start apache2
sudo systemctl enable apache2

# Start and enable MariaDB
sudo systemctl start mariadb
sudo systemctl enable mariadb

# Secure MariaDB installation (interactive)
echo "You should now run: sudo mysql_secure_installation"

# Create WordPress database and user
sudo mysql -e "CREATE DATABASE wordpress;"
sudo mysql -e "CREATE USER 'wp_user'@'localhost' IDENTIFIED BY 'YourStrongPassword';"
sudo mysql -e "GRANT ALL PRIVILEGES ON wordpress.* TO 'wp_user'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

# Download and extract WordPress
cd /var/www/html
sudo wget https://wordpress.org/latest.tar.gz
sudo tar -xzf latest.tar.gz
sudo mv wordpress/* .
sudo rm -rf wordpress latest.tar.gz

# Copy and update wp-config.php
sudo cp wp-config-sample.php wp-config.php
sudo sed -i "s/database_name_here/wordpress/" wp-config.php
sudo sed -i "s/username_here/wp_user/" wp-config.php
sudo sed -i "s/password_here/YourStrongPassword/" wp-config.php

# Set permissions
sudo chown -R www-data:www-data /var/www/html/
sudo chmod -R 755 /var/www/html/

# Remove Apache default page
sudo rm -f /var/www/html/index.html

# Restart Apache
sudo systemctl restart apache2

# Done
echo "WordPress setup complete. Visit your EC2 Public IP to finish configuration in the browser."

