<?php
header('Content-Type: application/json');
require_once '../config/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contact = $_POST['contact_number'] ?? '';

    if (empty($contact)) {
        echo json_encode(['status' => 'error', 'message' => 'Contact number is required.']);
        exit();
    }

    try {
        $stmt = $pdo->prepare("
            SELECT o.*, a.title as artwork_title, a.image_url 
            FROM orders o 
            JOIN artworks a ON o.artwork_id = a.id 
            WHERE o.contact_number = ? 
            ORDER BY o.order_date DESC
        ");
        $stmt->execute([$contact]);
        $orders = $stmt->fetchAll();

        if ($orders) {
            echo json_encode(['status' => 'success', 'data' => $orders]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No orders found for this contact number.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
