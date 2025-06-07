# 📸 Project 04: Build a Dynamic Photo Gallery with EC2 + S3 + RDS

This project allows users to upload image files through a web form on an EC2-hosted PHP application. The uploaded images are stored in an S3 bucket, and metadata (filename, URL, timestamp) is stored in an RDS MySQL database. The homepage (`index.php`) dynamically renders the gallery by fetching the image records from RDS and displaying the S3 images.

---

## 📁 Folder Structure

project-04-photo-gallery/
│
├── upload.html # Form to upload image
├── upload.php # Handles upload to S3 + inserts record to RDS
├── index.php # Displays gallery images dynamically from RDS
├── db-config.php # Database connection credentials
├── setup.sh # Installs Apache, PHP, Composer, AWS SDK
├── composer.json # PHP dependencies
├── vendor/ # Installed AWS SDK libraries
├── .gitignore # To ignore vendor and sensitive files
└── screenshots/ # Project screenshots (optional)


---

## 🚀 Services Used

- **EC2**: Host the PHP web app
- **IAM Role**: Attached to EC2 with S3 + RDS permissions
- **S3**: Stores uploaded image files
- **RDS (MySQL)**: Stores image metadata

---

## 🔨 Setup Summary

1. ✅ Created an S3 bucket named `uchegallerybucket`
2. ✅ Launched an EC2 instance with an IAM role `ec2-s3-rds`
3. ✅ Created a MySQL RDS DB named `gallery` with table `gallery`
4. ✅ Installed Apache, PHP, Composer and AWS SDK using `setup.sh`
5. ✅ Used `upload.php` to:
   - Validate and upload image to S3
   - Insert filename + S3 URL + timestamp into RDS
6. ✅ Used `index.php` to:
   - Fetch data from RDS
   - Render each image using the public S3 URL

---

## 🛠️ Key Code Snippets

### ✅ PHP to Upload to S3 and Insert to RDS (`upload.php`)
```php
$result = $s3->putObject([
    'Bucket' => $bucketName,
    'Key'    => $fileName,
    'SourceFile' => $fileTmpPath,
    'ContentType' => $fileType
]);

// Insert metadata into RDS
$stmt = $pdo->prepare("INSERT INTO gallery (filename, s3_url) VALUES (?, ?)");
$stmt->execute([$fileName, $result['ObjectURL']]);
```

## ⚠️ Errors I Faced & Fixes
### ❌ Error: 403 Forbidden or AccessDenied from S3 on upload
Cause: IAM role didn't have permission
Fix: Attach this bucket policy to uchegallerybucket:

```
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Sid": "AllowEC2Upload",
      "Effect": "Allow",
      "Principal": {
        "AWS": "arn:aws:iam::<YOUR_ACCOUNT_ID>:role/ec2-s3-rds"
      },
      "Action": ["s3:PutObject", "s3:PutObjectAcl"],
      "Resource": "arn:aws:s3:::uchegallerybucket/*"
    },
    {
      "Sid": "PublicReadForGalleryImages",
      "Effect": "Allow",
      "Principal": "*",
      "Action": "s3:GetObject",
      "Resource": "arn:aws:s3:::uchegallerybucket/*"
    }
  ]
}
```
## ❌ Image not rendering in index.php
Cause: S3 objects were private
Fix: Added public-read policy to bucket (see above)

## ❌ No file uploaded or upload error.
Cause: File size exceeded upload_max_filesize and post_max_size
Fix: Increased limits in /etc/php/8.x/apache2/php.ini
```
upload_max_filesize = 20M
post_max_size = 25M
```
Then restart Apache:

```
sudo systemctl restart apache2
```

## ❌ composer.json is not writable or autoload.php not found
Fixes:

Gave write permission to current user:

```
sudo chown -R $USER:$USER /var/www/html
```
Then installed AWS SDK again:

```
composer require aws/aws-sdk-php
```

## 🧪 Example Output
### ✅ Upload success
```
File uploaded successfully!
S3 URL: https://uchegallerybucket.s3.eu-north-1.amazonaws.com/sample.jpg
```
### ✅ Rendered Gallery

## 🙌 What I Learned
- How IAM roles grant secure access from EC2 to S3 and RDS
- How to debug 403 errors using S3 bucket policies
- PHP error reporting and debugging file uploads
- Working with the AWS SDK for PHP and Composer
- Inserting and fetching data from RDS using PDO
