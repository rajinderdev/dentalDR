<?php
 phpinfo();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h3>Starting DB Test...</h3>";

$host = "127.0.0.1";   // try also "localhost"
$user = "dental_erp";
$pass = "QIw3q(,X(qk-";
$db   = "dental_erp";
$port = 3306;

if (!function_exists('mysqli_connect')) {
    die("❌ MySQLi extension is not enabled in PHP!");
}

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
echo "✅ Database connection successful!";
