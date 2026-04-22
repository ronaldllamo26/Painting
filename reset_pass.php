<?php
require_once 'config/db_config.php';
$new_pass = password_hash('admin123', PASSWORD_DEFAULT);
try {
    $stmt = $pdo->prepare("UPDATE admin SET password = ? WHERE username = 'admin'");
    $stmt->execute([$new_pass]);
    echo "Password reset to 'admin123' successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
