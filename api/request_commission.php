<?php
header('Content-Type: application/json');
require_once '../config/db_config.php';

// CSRF Verification via POST
$csrfToken = $_POST['csrf_token'] ?? '';
if (!verifyCSRF($csrfToken)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid security token.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['customer_name'];
    $contact = trim($_POST['contact_number']);
    $email = $_POST['email'] ?? null;
    $subject = $_POST['subject'];
    $description = $_POST['description'];
    $budget = !empty($_POST['budget']) ? $_POST['budget'] : null;

    try {
        $stmt = $pdo->prepare("INSERT INTO commissions (customer_name, contact_number, email, subject, description, budget) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $contact, $email, $subject, $description, $budget]);

        echo json_encode(['status' => 'success', 'message' => 'Your commission request has been sent! Matthew will contact you soon.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send request. Please try again.']);
    }
}
?>
