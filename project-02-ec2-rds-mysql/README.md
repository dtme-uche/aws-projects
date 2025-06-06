# Project 03: Connect EC2 Website to RDS MySQL Database

## ✨ Overview

In this project, I extended my basic EC2-hosted website by connecting it to an Amazon RDS MySQL database. The goal was to demonstrate how a dynamic PHP site could pull data from a managed MySQL instance in the cloud. This hands-on experience helped me understand how to securely link AWS services together.

## 💡 What I Learned

* How to launch a MySQL database using Amazon RDS (Free Tier)
* How to configure EC2 and RDS connectivity using Security Groups
* How to run PHP on Apache to serve dynamic content from MySQL
* How to troubleshoot real-world issues like PHP errors, SQL syntax, and firewall rules

## 🚀 Stack and AWS Services Used

* **Amazon EC2** (Ubuntu 20.04 LTS)
* **Amazon RDS** (MySQL 8.x)
* **Apache2 + PHP**
* **MySQL Client**

## 🔧 Step-by-Step Process

### 1. Launching the EC2 Instance

I started by launching a t2.micro EC2 instance with Ubuntu 20.04. I made sure to:

* Attach a security group that allows HTTP (port 80) and SSH (port 22)
* Allocate and associate an Elastic IP for stable access

### 2. Installing Apache and PHP

Once connected via SSH, I ran the following to install Apache and PHP:

```bash
sudo apt update
sudo apt install apache2 php libapache2-mod-php -y
```

### 3. Creating the PHP Website

I created two main files in `/var/www/html/`:

* `index.php`: displays the current time from the database
* `db-config.php`: stores the database credentials (excluded from GitHub via `.gitignore`)

### 4. Setting Up Amazon RDS

I created a new MySQL RDS instance in the default VPC with:

* Public access enabled
* Security group that allows traffic on port 3306 from the EC2 security group

### 5. Installing MySQL Client on EC2

To connect to the RDS instance, I installed the MySQL client:

```bash
sudo apt install -y mysql-client
```

### 6. Creating the Database and Granting Privileges

After logging into RDS from EC2:

```bash
mysql -h <rds-endpoint> -u admin -p
```

I ran:

```sql
CREATE DATABASE testdb;
GRANT ALL PRIVILEGES ON testdb.* TO 'admin'@'%';
FLUSH PRIVILEGES;
```

### 7. Connecting PHP to RDS

Finally, I edited `db-config.php` with:

```php
<?php
$db_host = 'your-rds-endpoint';
$db_user = 'admin';
$db_pass = 'your-password';
$db_name = 'testdb';
?>
```

And in `index.php`, I made sure to enable error reporting and run a simple SQL query to display the current database time.

## ❌ Issues I Faced and How I Solved Them

### 1. **SQL Syntax Error in PHP Query**

**What happened:** I used this SQL query in PHP:

```sql
SELECT NOW() as current_time;
```

**Error:**

```text
You have an error in your SQL syntax; check the manual...
```

**Fix:** I realized MySQL was interpreting `current_time` incorrectly. I changed the query to:

```sql
SELECT NOW() AS `current_time`;
```

### 2. **Unknown Database 'database-1'**

**What happened:** My `db-config.php` was pointing to a database that didn’t exist yet.
**Fix:** I connected to the RDS instance and created it manually:

```sql
CREATE DATABASE testdb;
GRANT ALL PRIVILEGES ON testdb.* TO 'admin'@'%';
FLUSH PRIVILEGES;
```

### 3. **RDS Connection Timeout**

**What happened:** I tried connecting to RDS from EC2, but the connection timed out.
**Fix:** I updated the RDS security group to allow inbound traffic on port 3306 from the EC2 instance’s security group.

### 4. **PHP File Shows Blank Page**

**What happened:** Accessing `index.php` showed a blank white screen.
**Fix:** I added the following lines to the top of my file:

```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

That revealed the real error, which helped me debug further.

### 5. **Permission Denied When Writing PHP File**

**What happened:** I tried using `echo` to create PHP files in `/var/www/html`, but got permission errors.
**Fix:** I used `sudo tee` instead:

```bash
echo "<?php phpinfo(); ?>" | sudo tee /var/www/html/info.php
```

---

✅ After resolving all the issues, visiting the EC2 public IP finally showed:

```
Connected to RDS MySQL
Current DB Time: 2025-06-06 19:37:08
```

📁 **Note:** All project files are organized under `project-03-ec2-rds-mysql/`, with screenshots stored in the `/screenshots` directory. The `db-config.php` file is excluded from GitHub using `.gitignore`.

Let me know if you'd like a detailed architecture diagram or bash automation script added!

