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

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id'])) {
    $id = $data['id'];

    try {
        // Optional: Delete physical file if you want
        // $stmt = $pdo->prepare("SELECT image_url FROM artworks WHERE id = ?");
        // $stmt->execute([$id]);
        // $art = $stmt->fetch();
        // if ($art) @unlink('../' . $art['image_url']);

        $stmt = $pdo->prepare("DELETE FROM artworks WHERE id = ?");
        $stmt->execute([$id]);

        ob_clean();
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        ob_clean();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
