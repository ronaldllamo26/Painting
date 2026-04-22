<?php
header('Content-Type: application/json');
require_once '../config/db_config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $price = $_POST['price'];
    $size = $_POST['size'];
    $medium = $_POST['medium'];
    $status = $_POST['status'];
    $ai_description = $_POST['ai_description'];
    $ai_tags = $_POST['ai_tags'];
    $is_negotiable = isset($_POST['is_negotiable']) ? 1 : 0;

    try {
        $stmt = $pdo->prepare("UPDATE artworks SET title = ?, price = ?, size = ?, medium = ?, status = ?, ai_description = ?, ai_tags = ?, is_negotiable = ? WHERE id = ?");
        $stmt->execute([$title, $price, $size, $medium, $status, $ai_description, $ai_tags, $is_negotiable, $id]);

        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
