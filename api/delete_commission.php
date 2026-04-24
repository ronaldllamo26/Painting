<?php
ob_start();
header('Content-Type: application/json');
require_once '../config/db_config.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

// CSRF Verification via POST
$csrfToken = $_POST['csrf_token'] ?? '';
if (!verifyCSRF($csrfToken)) {
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => 'Invalid security token.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM commissions WHERE id = ?");
        $stmt->execute([$id]);
        ob_clean();
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        ob_clean();
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete request.']);
    }
}
?>
