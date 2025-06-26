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

