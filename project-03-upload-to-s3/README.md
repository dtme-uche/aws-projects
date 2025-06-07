# 🖼️ Project 03: Upload Images to S3 from EC2 using PHP

## ✨ Overview  
In this project, I built a simple web app hosted on an EC2 instance that allows users to upload images directly to an Amazon S3 bucket using PHP. The main objective was to get hands-on experience with IAM roles, the AWS SDK for PHP, and secure file uploads to S3 — all without hardcoding credentials.

---

## 💡 What I Learned  
- How to assign IAM roles to EC2 for secure access to S3  
- How to install and use the AWS SDK for PHP  
- How to handle file uploads and validate errors in PHP  
- How to troubleshoot SDK and permissions errors in a real-world setup  

---

## 🚀 Stack and AWS Services Used  
- Amazon EC2 (Ubuntu 20.04 LTS)  
- Amazon S3  
- IAM Roles  
- Apache2 + PHP  
- Composer (for dependency management)  
- AWS SDK for PHP  

---

## 🔧 Step-by-Step Process

### 1. Launching the EC2 Instance  
I launched a `t2.micro` EC2 instance with Ubuntu 20.04 and made sure to:
- Attach an IAM role with the `AmazonS3FullAccess` policy (note: in production, this should be scoped down).
- Open HTTP (port 80) and SSH (port 22) in the security group.

---

### 2. Installing Apache, PHP, and Composer  
I used a `setup.sh` script to automatically install:
- Apache
- PHP and necessary extensions
- Composer

The script also installed the AWS SDK for PHP via Composer.

---

### 3. Project File Structure  
I created the following files in `/var/www/html/`:
- `upload.html`: a simple form to select and upload images
- `setup.sh`: includes automation files to install dependencies and Composer 
- `upload.php`: handles the upload and sends the image to S3   
- `composer.json`: created by setup.sh and defines the AWS SDK dependency  
- `vendor/`: created by setup.sh by Composer and contains the AWS SDK and its dependencies  
- `.gitignore`: excludes `vendor/` and `composer.lock` from version control  

---

### 4. Creating the S3 Bucket  
I created an S3 bucket manually via the AWS Console.  
I left "Block All Public Access" **enabled** (default), which led to an issue later when I tried to set uploaded files as `public-read`.

---

## ❌ Issues I Faced and How I Solved Them

### 1. Composer SDK Error: Unknown Named Parameter `$instance`  
**What happened**:  
The default AWS SDK version from `composer install` was outdated and incompatible.  

**Fix**:  
I upgraded to a newer version using:  
```bash
sudo composer require aws/aws-sdk-php:^3.269.0 -W
```
### 2. File Upload Error: UPLOAD_ERR_INI_SIZE (1)
What happened:
PHP was silently rejecting image files larger than 2MB.

Fix:
I checked current limits with:

```
echo 'upload_max_filesize = ' . ini_get('upload_max_filesize');
echo 'post_max_size = ' . ini_get('post_max_size');
```
Then I edited the php.ini file:

```
upload_max_filesize = 10M
post_max_size = 20M
```
Restarted Apache to apply changes:

```
sudo systemctl restart apache2
```

### 3. S3 Upload Error: The bucket does not allow ACLs
What happened:
My upload.php was setting 'ACL' => 'public-read', but my bucket had ACLs disabled (a recent default in AWS).

Fix:

* I removed the 'ACL' => 'public-read' parameter from the putObject() call in upload.php.

* I did not modify the bucket policy. Uploaded files remain private by default.

### 4. IAM Role Debugging
What happened:
Initially, uploads failed silently because the EC2 instance didn’t have permissions to access S3.

Fix:
I confirmed that the instance had an IAM role attached with the AmazonS3FullAccess policy, which resolved the issue.

## ✅ Final Result
Once all issues were fixed, I successfully uploaded images from my EC2-hosted form to my private S3 bucket. The flow is seamless, and no AWS credentials are exposed in the code thanks to IAM role-based access.

