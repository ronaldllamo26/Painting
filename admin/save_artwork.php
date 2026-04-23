<?php
ob_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't echo errors to output, we'll catch them

require_once '../config/db_config.php';
session_start();

// CSRF Verification
$headers = getallheaders();
$csrfToken = $headers['X-CSRF-Token'] ?? '';
if (!verifyCSRF($csrfToken)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token.']);
    exit();
}

if (!isset($_SESSION['admin_logged_in'])) {
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
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    try {
        $stmt = $pdo->prepare("INSERT INTO artworks (title, price, size, medium, image_url, cloudinary_id, ai_description, ai_tags, is_negotiable, is_featured, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Available')");
        $stmt->execute([$title, $price, $size, $medium, $image_url, $cloudinary_id, $ai_description, $ai_tags, $is_negotiable, $is_featured]);

        ob_clean();
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        ob_clean();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
