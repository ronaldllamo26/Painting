<?php
header('Content-Type: application/json');
require_once '../config/db_config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $price = $_POST['price'];
    $size = $_POST['size'];
    $medium = $_POST['medium'];
    $image_url = $_POST['cloudinary_url'];
    $cloudinary_id = $_POST['cloudinary_id'];
    $ai_description = $_POST['ai_description'];
    $ai_tags = $_POST['ai_tags'];
    $is_negotiable = isset($_POST['is_negotiable']) ? 1 : 0;

    try {
        $stmt = $pdo->prepare("INSERT INTO artworks (title, price, size, medium, image_url, cloudinary_id, ai_description, ai_tags, is_negotiable, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Available')");
        $stmt->execute([$title, $price, $size, $medium, $image_url, $cloudinary_id, $ai_description, $ai_tags, $is_negotiable]);

        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
