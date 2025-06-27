#!/bin/bash

FOLDER_PATH="/home/ubuntu/uploads/"
BUCKET_NAME="my-uploads-bucket"
REGION="us-east-1"  # Change to your preferred AWS region

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

