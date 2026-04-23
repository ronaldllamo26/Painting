<?php
header('Content-Type: application/json');
require_once '../config/db_config.php';

if (!isset($_SESSION['collector_phone'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$phone = $_SESSION['collector_phone'];

// Fetch only APPROVED orders to show in "My Collection"
$stmt = $pdo->prepare("
    SELECT a.*, o.order_date, o.order_status 
    FROM artworks a
    JOIN orders o ON a.id = o.artwork_id
    WHERE o.contact_number = ? AND o.order_status = 'Approved'
    ORDER BY o.order_date DESC
");
$stmt->execute([$phone]);
$artworks = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['status' => 'success', 'artworks' => $artworks]);
