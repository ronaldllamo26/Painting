<?php
ob_start();
header('Content-Type: application/json');
require_once '../config/db_config.php';
session_start();

// Suppress errors to prevent breaking JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);

// CSRF Verification
$headers = getallheaders();
$csrfToken = $headers['X-CSRF-Token'] ?? '';
if (!verifyCSRF($csrfToken)) {
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token.']);
    exit();
}

if (!isset($_SESSION['admin_logged_in'])) {
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

// This script now uses **local storage** instead of Cloudinary.
// It also returns dummy AI description/tags so the front‑end can still display them.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ---------------------------------------------------------------
    // 1. Validate that an image was uploaded.
    // ---------------------------------------------------------------
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        ob_clean();
        echo json_encode(['status' => 'error', 'message' => 'No image provided or upload error.']);
        exit();
    }

    // ---------------------------------------------------------------
    // 2. Prepare the local upload directory (InfinityFree friendly).
    // ---------------------------------------------------------------
    $file = $_FILES['image'];
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    // Validate extension
    if (!in_array($fileExtension, $allowedExtensions)) {
        ob_clean();
        echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only images are allowed.']);
        exit();
    }

    // Validate MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (strpos($mimeType, 'image/') !== 0) {
        ob_clean();
        echo json_encode(['status' => 'error', 'message' => 'File is not a valid image.']);
        exit();
    }

    $uploadDir = '../uploads/artworks/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // ---------------------------------------------------------------
    // 3. Move the uploaded file to the local folder.
    // ---------------------------------------------------------------
    $tmpPath      = $file['tmp_name'];
    // Generate a unique filename to avoid collisions and directory traversal
    $safeName    = time() . '_' . bin2hex(random_bytes(8)) . '.' . $fileExtension;
    $targetPath  = $uploadDir . $safeName;
    $relativeUrl = 'uploads/artworks/' . $safeName;

    if (!move_uploaded_file($tmpPath, $targetPath)) {
        ob_clean();
        echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file.']);
        exit();
    }

    // ---------------------------------------------------------------
    // 4. Generate **dummy** AI data.
    //    (You can later replace this block with a real OpenAI call.)
    // ---------------------------------------------------------------
    $origName   = $file['name'];
    $baseTitle   = pathinfo($origName, PATHINFO_FILENAME);
    $dummyDescription = "A captivating original painting titled \"$baseTitle\" featuring vibrant colors and expressive brushwork. Perfect for modern interior décor.";
    $dummyTags        = "Abstract, Contemporary, Vibrant, Hand-Painted";

    // ---------------------------------------------------------------
    // 5. Return JSON in the same shape the front‑end expects.
    // ---------------------------------------------------------------
    ob_clean();
    echo json_encode([
        'status'          => 'success',
        // The front‑end expects "cloudinary_url" & "cloudinary_id" – we reuse the same names.
        'cloudinary_url'  => $relativeUrl,
        'cloudinary_id'   => $safeName,
        'image_url'       => $relativeUrl,
        'description'     => $dummyDescription,
        'tags'            => $dummyTags,
    ]);
    exit();
}
?>
