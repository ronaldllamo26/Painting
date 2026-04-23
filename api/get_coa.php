<?php
header('Content-Type: application/json');
require_once '../config/db_config.php';

if (!isset($_SESSION['collector_phone'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$artwork_id = $_GET['id'] ?? 0;
$phone = $_SESSION['collector_phone'];

// Verify that the artwork belongs to the collector and is approved
$stmt = $pdo->prepare("
    SELECT a.*, o.order_date, o.customer_name
    FROM artworks a
    JOIN orders o ON a.id = o.artwork_id
    WHERE a.id = ? AND o.contact_number = ? AND o.order_status = 'Approved'
");
$stmt->execute([$artwork_id, $phone]);
$art = $stmt->fetch(PDO::FETCH_ASSOC);

if ($art) {
    $coa = [
        'title' => $art['title'],
        'medium' => $art['medium'],
        'size' => $art['size'],
        'date' => date('F d, Y', strtotime($art['order_date'])),
        'collector' => $art['customer_name'],
        'security_id' => strtoupper(substr(md5($art['id'] . $art['order_date']), 0, 12))
    ];
    echo json_encode(['status' => 'success', 'coa' => $coa]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Artwork not found or not yet approved.']);
}
