<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db_config.php';

// CSRF Verification via POST (Safe for InfinityFree)
$csrfToken = $_POST['csrf_token'] ?? '';
if (!verifyCSRF($csrfToken)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid security token. Please refresh.']);
    exit();
}

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'update_studio') {
    try {
        $keys = ['studio_name', 'contact_number', 'email_address', 'facebook_link', 'messenger_id'];
        $pdo->beginTransaction();
        foreach ($keys as $key) {
            if (isset($_POST[$key])) {
                $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
                $stmt->execute([$_POST[$key], $key]);
            }
        }
        if (!empty($_FILES['gcash_qr_file']['name'])) {
            $targetDir = "../assets/img/";
            $fileName = "gcash-qr-" . time() . ".png";
            $targetPath = $targetDir . $fileName;
            if (move_uploaded_file($_FILES['gcash_qr_file']['tmp_name'], $targetPath)) {
                $relativeUrl = 'assets/img/' . $fileName;
                $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'gcash_qr'");
                $stmt->execute([$relativeUrl]);
            }
        }
        $pdo->commit();
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} 
elseif ($action === 'update_password') {
    $new_pass = $_POST['new_password'];
    if (empty($new_pass)) { echo json_encode(['status' => 'error', 'message' => 'Password cannot be empty']); exit; }
    try {
        $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE admin SET password = ? WHERE username = ?");
        $stmt->execute([$hashed_pass, $_SESSION['admin_user']]);
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
elseif ($action === 'update_ai') {
    $apiKey = $_POST['openai_api_key'] ?? '';
    try {
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('openai_api_key', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$apiKey, $apiKey]);
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
