# Project 01: Deploy a Website Using EC2 + Apache

## Overview
In this project, I launch an EC2 instance, install Apache HTTP Server, and host a simple website (HTML page). This project demonstrates basic EC2 setup, web server deployment, and Security Group configuration.

## 🛠️ AWS Services Used
- EC2
- Elastic IP
- Security Groups

## Website Preview
![Website Screenshot](./screenshots/website_preview.png)

---

## 📁 Project Files

| File | Description |
|------|-------------|
| `index.html` | Simple HTML page for the website |
| `setup-apache.sh` | Script to install and configure Apache |
| `README.md` | This project documentation |
| `screenshots/` | Screenshots of EC2 setup, Apache installation, and the final webpage |

## Steps to Reproduce

1. **Launch an EC2 Instance**
   - AMI: Ubuntu Server 20.04 (Free Tier eligible)
   - Instance Type: t2.micro
   - Key Pair: Create or use existing
   - Security Group:
     - Allow HTTP (port 80)
     - Allow SSH (port 22)
   - Launch the instance

### ✅ Step 2: Prepare SSH Access

On your local machine (Git Bash or Terminal):
```bash
chmod 400 apache-website.pem
```


Now connect to the instance:
```bash
ssh -i apache-website.pem ubuntu@<your-ec2-public-ip>
```
✅ Step 3: Create Project Files Locally
In your local aws-projects/project-02-deploy-apache/ folder:
```bash
touch index.html setup-apache.sh README.md 
```
✅ Step 4: Transfer Files to EC2 Using scp
From your local terminal, run:
```bash
scp -i apache-website.pem setup-apache.sh index.html ubuntu@<your-ec2-public-ip>:~
```
This command securely copies your index.html and setup-apache.sh into the home directory of your EC2 instance. 

✅ Step 5: Transfer Files to EC2 Using scp
From your local terminal, run:
```bash
scp -i apache-website.pem setup-apache.sh index.html ubuntu@<your-ec2-public-ip>:~
```
This command securely copies your index.html and setup-apache.sh into the home directory of your EC2 instance.
```bash
chmod +x setup-apache.sh
./setup-apache.sh
```
This will install Apache and copy your HTML file to the web root.

✅ Step 6: View the Website
Open your browser and go to:

http://<your-ec2-public-ip>

You should see your custom webpage that says:

> Hello from EC2 + Apache!

✅ To avoid ongoing charges:

- Terminate the EC2 instance 
- Release the Elastic IP if used

