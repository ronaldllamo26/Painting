<?php
header('Content-Type: application/json');
require_once '../config/db_config.php';

$data = json_decode(file_get_contents('php://input'), true);
$phone = $data['phone'] ?? '';

if (empty($phone)) {
    echo json_encode(['status' => 'error', 'message' => 'Phone number is required.']);
    exit();
}

// Check if phone number exists in orders table
$stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE contact_number = ?");
$stmt->execute([$phone]);
$count = $stmt->fetchColumn();

if ($count > 0) {
    $_SESSION['collector_phone'] = $phone;
    echo json_encode(['status' => 'success', 'message' => 'Login successful.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No purchase history found for this contact number. Please ensure you use the number provided during checkout.']);
}
