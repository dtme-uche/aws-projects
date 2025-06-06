<?php
require 'db-config.php';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$result = $conn->query("SELECT NOW() AS `current_time`");
$row = $result->fetch_assoc();
echo "<h1>Connected to RDS MySQL</h1>";
echo "<p>Current DB Time: " . $row['current_time'] . "</p>";
$conn->close();
?>

