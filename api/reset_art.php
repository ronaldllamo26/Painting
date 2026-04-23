<?php
session_start();
require_once '../config/db_config.php';
$id = $_POST['id'];
$stmt = $pdo->prepare("UPDATE artworks SET status = 'Available' WHERE id = ?");
$stmt->execute([$id]);
echo json_encode(['status' => 'success']);
?>
