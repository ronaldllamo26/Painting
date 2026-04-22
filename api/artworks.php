<?php
header('Content-Type: application/json');
require_once '../config/db_config.php';

try {
    $stmt = $pdo->query("SELECT * FROM artworks ORDER BY created_at DESC");
    $artworks = $stmt->fetchAll();
    
    // Extract unique tags for filtering
    $allTags = [];
    foreach ($artworks as $art) {
        if (!empty($art['ai_tags'])) {
            $tags = explode(',', $art['ai_tags']);
            foreach ($tags as $tag) {
                $trimmedTag = trim($tag);
                if (!in_array($trimmedTag, $allTags)) {
                    $allTags[] = $trimmedTag;
                }
            }
        }
    }

    echo json_encode([
        'status' => 'success',
        'data' => $artworks,
        'tags' => $allTags
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
