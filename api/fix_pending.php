<?php
require_once '../config/db_config.php';
// I-set lahat ng Artworks na 'Pending' pero wala sa Orders table back to 'Available'
$stmt = $pdo->query("UPDATE artworks SET status = 'Available' WHERE status = 'Pending' AND id NOT IN (SELECT artwork_id FROM orders WHERE order_status = 'Pending')");
echo "Fixed " . $stmt->rowCount() . " artworks!";
?>
