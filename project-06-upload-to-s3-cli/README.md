# ☁️ Project 06: Upload Files from EC2 to S3 using AWS CLI

> This project demonstrates how to upload files from an EC2 instance to an S3 bucket using the AWS CLI, with an IAM role for secure access. I also learned how to transfer files from my local machine to the EC2 instance via `scp`.

---

## 🧰 Tools & Services
- EC2 (Ubuntu 22.04)
- S3 bucket
- IAM Role (with S3 Full Access)
- AWS CLI

---

## 🔐 IAM Role Setup

- Attached the managed policy `AmazonS3FullAccess` to the EC2 instance's IAM role
- This allowed the instance to create and upload to any S3 bucket without storing access keys

---

## 📁 Transfer Files from Local to EC2

I used `scp` to copy a file from my local computer to the EC2 instance:

```bash
scp -i path/to/my-key.pem image.jpg ubuntu@<EC2-PUBLIC-IP>:~/uploads/
```

> This placed `image.jpg` inside `/home/ubuntu/uploads/` on the EC2 instance.

---

## ⚙️ Setup AWS CLI on EC2

Initially, I tried:

```bash
sudo apt install awscli -y
```

But got this error:
```
Package 'awscli' is not available
```

### ✅ Solution: Manual AWS CLI v2 Installation

```bash
curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"
unzip awscliv2.zip
sudo ./aws/install
aws --version
```

---

## 📝 Upload Script

I created a script named `upload-script.sh` to:
- Create the bucket (if it doesn't exist)
- Upload files from the `uploads` folder

```bash
#!/bin/bash

FOLDER_PATH="/home/ubuntu/uploads/"
BUCKET_NAME="my-uploads-bucket"
REGION="us-east-1"

# Create the S3 bucket (if it doesn't already exist)
echo "Creating S3 bucket: $BUCKET_NAME (if it doesn't exist)"
aws s3api head-bucket --bucket "$BUCKET_NAME" 2>/dev/null

if [ $? -ne 0 ]; then
    aws s3api create-bucket \
        --bucket "$BUCKET_NAME" \
        --region "$REGION" \
        --create-bucket-configuration LocationConstraint="$REGION"
    echo "Bucket created."
else
    echo "Bucket already exists."
fi

# Upload files to the S3 bucket
echo "Uploading files to S3..."
aws s3 cp "$FOLDER_PATH" s3://$BUCKET_NAME/ --recursive

echo "Done. Files uploaded to s3://$BUCKET_NAME/"
```

---

## 🧯 Issues I Faced & Fixes

| Issue | Fix |
|-------|-----|
| `aws: command not found` | Installed AWS CLI v2 manually |
| `Package 'awscli' is not available` | Used `curl` + `unzip` to install manually |
| `Access Denied` | Used IAM role with `AmazonS3FullAccess` attached |

---

## ✅ Result

I was able to upload files from `/home/ubuntu/uploads/` on my EC2 instance to `s3://my-uploads-bucket/` using the AWS CLI — fully automated via the script.

---
