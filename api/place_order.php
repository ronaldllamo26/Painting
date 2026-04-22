<?php
header('Content-Type: application/json');
require_once '../config/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $artwork_id = $_POST['artwork_id'];
    $name = $_POST['customer_name'];
    $contact = $_POST['contact_number'];
    $address = $_POST['address'];
    $payment = $_POST['payment_method'];
    $proposed_price = !empty($_POST['proposed_price']) ? $_POST['proposed_price'] : null;
    $receipt_url = null;

    try {
        $pdo->beginTransaction();

        // 1. Check if artwork is still available
        $stmt = $pdo->prepare("SELECT status FROM artworks WHERE id = ? FOR UPDATE");
        $stmt->execute([$artwork_id]);
        $art = $stmt->fetch();

        if (!$art || $art['status'] !== 'Available') {
            throw new Exception("Artwork is no longer available.");
        }

        // 2. Handle Receipt Upload if GCash
        if ($payment === 'GCash' && isset($_FILES['receipt'])) {
            $targetDir = "../uploads/receipts/";
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
            
            $fileName = time() . '_' . basename($_FILES["receipt"]["name"]);
            $targetFilePath = $targetDir . $fileName;
            
            if (move_uploaded_file($_FILES["receipt"]["tmp_name"], $targetFilePath)) {
                $receipt_url = 'uploads/receipts/' . $fileName;
            } else {
                throw new Exception("Failed to upload receipt.");
            }
        }

        // 3. Create Order
        $stmt = $pdo->prepare("INSERT INTO orders (artwork_id, customer_name, contact_number, address, payment_method, receipt_url, proposed_price) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$artwork_id, $name, $contact, $address, $payment, $receipt_url, $proposed_price]);

        // 4. Update Artwork Status to Pending
        $stmt = $pdo->prepare("UPDATE artworks SET status = 'Pending' WHERE id = ?");
        $stmt->execute([$artwork_id]);

        $pdo->commit();
        echo json_encode(['status' => 'success']);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
