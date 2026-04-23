<?php
header('Content-Type: application/json');
require_once '../config/db_config.php';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $price = $_POST['price'];
    $size = $_POST['size'];
    $medium = $_POST['medium'];
    $status = $_POST['status'];
    $ai_description = $_POST['ai_description'];
    $ai_tags = $_POST['ai_tags'];
    $is_negotiable = isset($_POST['is_negotiable']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    try {
        $pdo->beginTransaction();

        // Handle Image Update if provided
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (!in_array($fileExtension, $allowedExtensions)) {
                throw new Exception("Invalid file type.");
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            if (strpos($mimeType, 'image/') !== 0) {
                throw new Exception("File is not a valid image.");
            }

            $uploadDir = '../uploads/artworks/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            
            $safeName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $fileExtension;
            $targetPath = $uploadDir . $safeName;
            $relativeUrl = 'uploads/artworks/' . $safeName;

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $stmt = $pdo->prepare("UPDATE artworks SET image_url = ? WHERE id = ?");
                $stmt->execute([$relativeUrl, $id]);
            }
        }

        $stmt = $pdo->prepare("UPDATE artworks SET title = ?, price = ?, size = ?, medium = ?, status = ?, ai_description = ?, ai_tags = ?, is_negotiable = ?, is_featured = ? WHERE id = ?");
        $stmt->execute([$title, $price, $size, $medium, $status, $ai_description, $ai_tags, $is_negotiable, $is_featured, $id]);

        $pdo->commit();
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
