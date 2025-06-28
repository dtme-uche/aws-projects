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


## ⚡️ S3 → Lambda Integration

### 🛠️ How I Created the Lambda Function

1. **Go to AWS Console → Lambda → Create function**
2. Choose **Author from scratch**
3. Set name to `s3-upload-logger`
4. Runtime: **Node.js 20.x**
5. Click **Create function**

### 🔗 Add Trigger (S3 Bucket)
1. After function is created, go to **Configuration → Triggers**
2. Click **Add trigger**
3. Choose **S3**
4. Select your bucket (e.g., `uche-cli-uploads-bucket`)
5. Event type: **PUT (ObjectCreated)**
6. Leave prefix/suffix blank (or specify if needed)
7. Click **Add**

### 🔐 IAM Role for Lambda Function
Ensure the Lambda has permissions to log to CloudWatch and optionally read from S3:

**IAM Role used:**
- Attached policy: `AWSLambdaBasicExecutionRole`

Optional (if Lambda needs to read S3 file contents):
- Add: `AmazonS3ReadOnlyAccess`

You can view/edit the role by going to:
- **Lambda → Configuration → Permissions → Execution Role → Click Role Name**

### 5. **Created Lambda Function (`s3-upload-logger`)**
- Runtime: **Node.js 20.x**
- Trigger: **S3 → uche-cli-uploads-bucket**, event type `PUT`

### 6. **Fixed Runtime Error**
- Error: `exports is not defined in ES module scope`
- Fix: Used ESM-style export for Node.js 20

### ✅ Final Lambda Code:
```js
export const handler = async (event) => {
    console.log("🚨 Raw Event Received:", JSON.stringify(event, null, 2));

    if (!event.Records || event.Records.length === 0) {
        console.log("⚠️ No records found in event. Upload may not have triggered correctly.");
        return { statusCode: 400, body: JSON.stringify('No S3 upload records received.') };
    }

    for (const record of event.Records) {
        const bucket = record.s3.bucket.name;
        const key = decodeURIComponent(record.s3.object.key.replace(/\+/g, ' '));
        const size = record.s3.object.size;
        console.log(`✅ File uploaded to S3: ${key} (${size} bytes) in bucket ${bucket}`);
    }

    return { statusCode: 200, body: JSON.stringify('Upload logged successfully!') };
};
```

### 7. **Verified CloudWatch Logs**
- After upload, logs show the file name, size, and bucket name

---

## 🧯 Issues I Faced & Fixed

| Issue | Fix |
|-------|-----|
| `aws: command not found` | Installed AWS CLI v2 manually |
| `Package 'awscli' is not available` | Used zip installer instead of apt |
| Lambda crash (`exports is not defined`) | Used `export const handler` for ESM in Node.js 20 |
| No logs in CloudWatch | Added CloudWatch permissions + verified S3 trigger |

---

## ✅ Outcome
- Files are uploaded from EC2 → S3 via CLI
- Each upload triggers a Lambda function
- Uploads are logged in CloudWatch

---
