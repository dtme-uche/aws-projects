# 📦 Project 05: Host a WordPress Site on AWS EC2

> In this project, I deployed a fully functional WordPress CMS on an Ubuntu EC2 instance using the LAMP stack. No control panel. Just Linux, terminal, and cloud.

---

## 🎯 What I Did

- 🛠 Launched an EC2 instance (Ubuntu 22.04)
- 🔐 Configured security groups to allow HTTP (80), HTTPS (443), and SSH (22)
- 💡 Installed Apache, PHP, and MariaDB (LAMP stack)
- 🐘 Created a MySQL database for WordPress
- 🌐 Downloaded and configured WordPress
- ⚙️ Set correct permissions and cleaned up default Apache page
- ✅ Accessed WordPress via Public IP and completed setup via browser

---

## 🧠 Steps I Took

### 1️⃣ EC2 Launch
- Region: `eu-north-1`
- Instance type: `t2.micro`
- OS: Ubuntu 22.04 LTS
- Security Group:
  - `HTTP (80)` — from Anywhere
  - `HTTPS (443)` — from Anywhere
  - `SSH (22)` — from My IP

### 2️⃣ SSH Access
```bash
ssh -i "my-key.pem" ubuntu@<EC2-Public-IP>
```

### 3️⃣ Installed LAMP Stack
```bash
sudo apt update
sudo apt install apache2 php libapache2-mod-php php-mysql mariadb-server unzip wget -y
```

### 4️⃣ Configured MySQL
```bash
sudo mysql_secure_installation

# Inside MySQL shell:
CREATE DATABASE wordpress;
CREATE USER 'wp_user'@'localhost' IDENTIFIED BY 'YourStrongPassword';
GRANT ALL PRIVILEGES ON wordpress.* TO 'wp_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 5️⃣ Installed WordPress
```bash
cd /var/www/html
sudo wget https://wordpress.org/latest.tar.gz
sudo tar -xzf latest.tar.gz
sudo mv wordpress/* .
sudo rm -rf wordpress latest.tar.gz
```

### 6️⃣ Gave WordPress Correct Permissions
```bash
sudo chown -R www-data:www-data /var/www/html/
sudo chmod -R 755 /var/www/html/
```

### 7️⃣ Configured wp-config.php
```bash
sudo cp wp-config-sample.php wp-config.php
sudo vi wp-config.php
```

## Updated:
```bash
define('DB_NAME', 'wordpress');
define('DB_USER', 'wp_user');
define('DB_PASSWORD', 'YourStrongPassword');
define('DB_HOST', 'localhost');
```

### 8️⃣ Replaced Apache Default Page
```bash
sudo rm /var/www/html/index.html
```

### 9️⃣ Restarted Apache
```bash
sudo systemctl restart apache2
```

# VISIT http://<EC2-Public-IP>











