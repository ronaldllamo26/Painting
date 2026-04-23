<?php
ob_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't echo errors to output, we'll catch them

require_once '../config/db_config.php';
session_start();

// CSRF Verification via POST
$csrfToken = $_POST['csrf_token'] ?? '';
if (!verifyCSRF($csrfToken)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid security token.']);
    exit();
}

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $price = $_POST['price'];
    $size = $_POST['size'];
    $medium = $_POST['medium'];
    $ai_description = $_POST['description'] ?? $_POST['ai_description'];
    $ai_tags = $_POST['tags'] ?? $_POST['ai_tags'];
    $is_negotiable = isset($_POST['is_negotiable']) ? 1 : 0;
    
    $image_url = $_POST['cloudinary_url'] ?? '';
    $cloudinary_id = $_POST['cloudinary_id'] ?? '';

    // Handle File Upload if provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($ext, $allowed)) {
            $uploadDir = '../uploads/artworks/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            
            $safeName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $safeName)) {
                $image_url = 'uploads/artworks/' . $safeName;
                $cloudinary_id = $safeName;
            }
        }
    }

    if (empty($image_url)) {
        ob_clean();
        echo json_encode(['status' => 'error', 'message' => 'Image is required.']);
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO artworks (title, price, size, medium, image_url, cloudinary_id, ai_description, ai_tags, is_negotiable, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Available')");
        $stmt->execute([$title, $price, $size, $medium, $image_url, $cloudinary_id, $ai_description, $ai_tags, $is_negotiable]);

        ob_clean();
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        ob_clean();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
