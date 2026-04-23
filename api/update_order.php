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

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['order_id']) && isset($data['status'])) {
    $order_id = $data['order_id'];
    $new_status = $data['status']; // 'Approved' or 'Cancelled'

    try {
        $pdo->beginTransaction();

        // Get artwork ID for this order
        $stmt = $pdo->prepare("SELECT artwork_id FROM orders WHERE id = ?");
        $stmt->execute([$order_id]);
        $order = $stmt->fetch();

        if (!$order) throw new Exception("Order not found.");

        $artwork_id = $order['artwork_id'];

        // Update Order Status
        $stmt = $pdo->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);

        // Update Artwork Status & Generate COA if Approved
        if ($new_status === 'Approved') {
            $stmt = $pdo->prepare("UPDATE artworks SET status = 'Sold' WHERE id = ?");
            $stmt->execute([$artwork_id]);

            // Generate unique COA: MR-YEAR-ORDERID-RANDOM
            $coa = "MR-" . date('Y') . "-" . str_pad($order_id, 4, '0', STR_PAD_LEFT) . "-" . strtoupper(bin2hex(random_bytes(2)));
            $stmt = $pdo->prepare("UPDATE orders SET coa_number = ? WHERE id = ?");
            $stmt->execute([$coa, $order_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE artworks SET status = 'Available' WHERE id = ?");
            $stmt->execute([$artwork_id]);
        }

        $pdo->commit();
        echo json_encode(['status' => 'success']);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
