<?php


// Show all errors for debugging - remove or comment out in production
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

require 'vendor/autoload.php';
require 'db-config.php';


use Aws\S3\S3Client;
use Aws\Exception\AwsException;

// S3 bucket name
$bucketName = 'uchegallerybucket';  // <-- Replace with your bucket name

// Instantiate S3 client using IAM role (no credentials)
$s3 = new S3Client([
    'version' => 'latest',
    'region'  => 'eu-north-1',
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = basename($_FILES['image']['name']);
        $fileSize = $_FILES['image']['size'];
        $fileType = $_FILES['image']['type'];

        // Basic validation: allow only images (png, jpg, jpeg, gif)
        $allowedMimeTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif'];
        if (!in_array($fileType, $allowedMimeTypes)) {
            die('Error: Only image files (png, jpg, jpeg, gif) are allowed.');
        }

        // Optional: You can sanitize file name here
        $fileName = preg_replace("/[^A-Za-z0-9_\-\.]/", '_', $fileName);

        try {
            // Upload to S3 bucket
            $result = $s3->putObject([
                'Bucket' => $bucketName,
                'Key'    => $fileName,
                'SourceFile' => $fileTmpPath,
                // Do NOT set ACL since bucket disables ACLs by default
                //'ACL' => 'public-read',
                'ContentType' => $fileType,
	]);

            echo "File uploaded successfully!<br>";
            echo "S3 URL: <a href='" . $result['ObjectURL'] . "' target='_blank'>" . $result['ObjectURL'] . "</a>";

            // TODO: Insert file info into your RDS database here if needed

        // ✅ Insert metadata into RDS
            try {
                $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $stmt = $pdo->prepare("INSERT INTO gallery (filename, s3_url) VALUES (:filename, :s3_url)");
                $stmt->execute([
                    ':filename' => $fileName,
                    ':s3_url'   => $result['ObjectURL']
                ]);

                echo "Database insert successful!";
            } catch (PDOException $e) {
                echo "Database Error: " . $e->getMessage();
            }


        } catch (AwsException $e) {
            echo "AWS Error: " . $e->getMessage();
        }
    } else {
        echo "Error: No file uploaded or upload error.";
    }
} else {
    echo "Invalid request method.";
}

