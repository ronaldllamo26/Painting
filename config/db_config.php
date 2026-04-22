<?php
// db_config.php - Database connection using PDO

$host = 'localhost';
$db   = 'art_gallery';
$user = 'root';
$pass = ''; // Default XAMPP password is empty
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Cloudinary Configuration (Artist needs to fill these)
define('CLOUDINARY_URL', 'cloudinary://API_KEY:API_SECRET@CLOUD_NAME');
define('CLOUDINARY_CLOUD_NAME', 'YOUR_CLOUD_NAME');
define('CLOUDINARY_API_KEY', 'YOUR_API_KEY');
define('CLOUDINARY_API_SECRET', 'YOUR_API_SECRET');

// AI API Keys (OpenAI or Google Vision)
define('OPENAI_API_KEY', 'YOUR_OPENAI_API_KEY');
