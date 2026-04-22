<?php
require_once 'config/db_config.php';
$stmt = $pdo->query("SELECT * FROM admin");
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($admins);
?>
