<?php
require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

echo 'upload_max_filesize = ' . ini_get('upload_max_filesize') . "<br>";
echo 'post_max_size = ' . ini_get('post_max_size') . "<br>";

// Instantiate S3 client with region and default credentials (IAM role on EC2)
$s3 = new S3Client([
    'version' => 'latest',
    'region'  => 'eu-north-1',
]);

$bucket = 'my-ec2-image-uploads'; // Replace with your bucket name

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fileToUpload'])) {
    $file = $_FILES['fileToUpload'];

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo "File upload error: " . $file['error'];
        exit;
    }

    // Basic security: allow only images (jpeg, png, gif)
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!in_array($mimeType, $allowedMimeTypes)) {
        echo "Invalid file type. Only JPG, PNG, and GIF allowed.";
        exit;
    }

    // Use a safe file name to avoid overwriting and injection issues
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $key = uniqid('upload_', true) . '.' . $ext;

    try {
        $result = $s3->putObject([
            'Bucket' => $bucket,
            'Key'    => $key,
            'SourceFile' => $file['tmp_name'],
            //'ACL'    => 'public-read',  // Consider if public-read is appropriate
            'ContentType' => $mimeType, // Set proper content-type on S3
        ]);
        echo "File uploaded successfully. <a href='{$result['ObjectURL']}' target='_blank'>View File</a>";
 } catch (AwsException $e) {
        echo "Upload error: " . $e->getAwsErrorMessage();
    }
} else {
    echo "No file uploaded.";
}
?>

