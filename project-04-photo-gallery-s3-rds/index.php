<?php
// Enable error reporting (remove in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'db-config.php';

try {
    // Connect to RDS MySQL
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all images from gallery table
    $stmt = $pdo->query("SELECT filename, s3_url, uploaded_at FROM gallery ORDER BY uploaded_at DESC");
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>📷 My AWS Gallery</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background: #f9f9f9;
        }
        h1 {
            margin-top: 30px;
        }
        .gallery {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 20px;
        }
        .image-card {
            background: white;
            margin: 10px;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            width: 250px;
        }
        .image-card img {
            max-width: 100%;
 	    border-radius: 4px;
        }
        .image-card .info {
            margin-top: 8px;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>

<h1>📸 AWS S3 Photo Gallery</h1>

<div class="gallery">
    <?php if ($images): ?>
        <?php foreach ($images as $img): ?>
            <div class="image-card">
                <img src="<?= htmlspecialchars($img['s3_url']) ?>" alt="<?= htmlspecialchars($img['filename']) ?>">
                <div class="info"><?= htmlspecialchars($img['filename']) ?><br>
                    <small><?= htmlspecialchars($img['uploaded_at']) ?></small>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No images uploaded yet.</p>
    <?php endif; ?>
</div>

</body>
</html>

