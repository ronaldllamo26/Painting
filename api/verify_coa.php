<?php
header('Content-Type: application/json');
require_once '../config/db_config.php';
session_start();

// CSRF Verification
$headers = getallheaders();
$csrfToken = $headers['X-CSRF-Token'] ?? '';
if (!verifyCSRF($csrfToken)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $coa = trim($_POST['coa_number']);

    try {
        // Find the order with this COA number
        $stmt = $pdo->prepare("SELECT o.*, a.title as art_title, a.image_url as art_img, a.medium, a.size 
                             FROM orders o 
                             JOIN artworks a ON o.artwork_id = a.id 
                             WHERE o.coa_number = ? AND o.order_status = 'Approved'");
        $stmt->execute([$coa]);
        $result = $stmt->fetch();

        if ($result) {
            echo json_encode([
                'status' => 'success', 
                'data' => [
                    'title' => $result['art_title'],
                    'img' => $result['art_img'],
                    'collector' => $result['customer_name'],
                    'date' => date('F Y', strtotime($result['order_date'])),
                    'specs' => $result['size'] . ' | ' . $result['medium']
                ]
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid COA Number. This record does not exist in our studio registry.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Verification failed. Please try again.']);
    }
}
?>
