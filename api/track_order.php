<?php
header('Content-Type: application/json');
require_once '../config/db_config.php';
session_start();

// CSRF Verification via POST
$csrfToken = $_POST['csrf_token'] ?? '';
if (!verifyCSRF($csrfToken)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid security token.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contact = $_POST['contact_number'];

    try {
        $stmt = $pdo->prepare("SELECT o.*, a.title as art_title, a.image_url as art_img 
                             FROM orders o 
                             JOIN artworks a ON o.artwork_id = a.id 
                             WHERE o.contact_number = ? 
                             ORDER BY o.order_date DESC");
        $stmt->execute([$contact]);
        $orders = $stmt->fetchAll();

        if ($orders) {
            echo json_encode(['status' => 'success', 'data' => $orders]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No orders found for this contact number.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error.']);
    }
}
?>
