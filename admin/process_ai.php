<?php
header('Content-Type: application/json');
require_once '../config/db_config.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Content-Type: application/json');
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
        echo json_encode(['status' => 'error', 'message' => 'No image provided or upload error.']);
        exit();
    }

    // ---------------------------------------------------------------
    // 2. Prepare the local upload directory (InfinityFree friendly).
    // ---------------------------------------------------------------
    $uploadDir = '../uploads/artworks/';
    if (!is_dir($uploadDir)) {
        // Use recursive mkdir with 0755 permissions (shared hosting limit).
        mkdir($uploadDir, 0755, true);
    }

    // ---------------------------------------------------------------
    // 3. Move the uploaded file to the local folder.
    // ---------------------------------------------------------------
    $tmpPath      = $_FILES['image']['tmp_name'];
    $origName    = basename($_FILES['image']['name']);
    $extension   = pathinfo($origName, PATHINFO_EXTENSION);
    // Generate a unique filename to avoid collisions.
    $safeName    = time() . '_' . uniqid() . '.' . $extension;
    $targetPath  = $uploadDir . $safeName;
    // Relative URL that will be stored in the DB and used by the front‑end.
    $relativeUrl = 'uploads/artworks/' . $safeName;

    if (!move_uploaded_file($tmpPath, $targetPath)) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file.']);
        exit();
    }

    // ---------------------------------------------------------------
    // 4. Generate **dummy** AI data.
    //    (You can later replace this block with a real OpenAI call.)
    // ---------------------------------------------------------------
    $baseTitle   = pathinfo($origName, PATHINFO_FILENAME);
    $dummyDescription = "A captivating original painting titled \"$baseTitle\" featuring vibrant colors and expressive brushwork. Perfect for modern interior décor.";
    $dummyTags        = "Abstract, Contemporary, Vibrant, Hand‑Painted";

    // ---------------------------------------------------------------
    // 5. Return JSON in the same shape the front‑end expects.
    // ---------------------------------------------------------------
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
