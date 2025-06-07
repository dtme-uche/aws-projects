#!/bin/bash

# Exit on error
set -e

echo "🔄 Updating system packages..."
sudo apt update

echo "📦 Installing Apache, PHP, unzip..."
sudo apt install -y apache2 php php-cli unzip

echo "📥 Installing Composer..."
cd ~
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

echo "📚 Installing AWS SDK for PHP..."
composer require aws/aws-sdk-php

echo "✅ Setup complete!"

