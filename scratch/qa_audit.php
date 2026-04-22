<?php
require_once 'config/db_config.php';
echo "--- DATABASE AUDIT ---\n";
try {
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables found: " . implode(', ', $tables) . "\n";
    
    $required = ['admin', 'artworks', 'orders', 'settings'];
    foreach($required as $table) {
        if(!in_array($table, $tables)) {
            echo "MISSING TABLE: $table\n";
        } else {
            $cols = $pdo->query("DESCRIBE $table")->fetchAll(PDO::FETCH_COLUMN);
            echo "Table '$table' structure OK. Columns: " . implode(', ', $cols) . "\n";
        }
    }
} catch(Exception $e) {
    echo "DB ERROR: " . $e->getMessage() . "\n";
}

echo "\n--- FILE INTEGRITY ---\n";
$critical = [
    'index.php', 'admin/index.php', 'admin/login.php', 'admin/settings.php', 
    'api/update_settings.php', 'api/update_order.php', 'config/db_config.php',
    'assets/css/style.css', 'assets/js/main.js'
];
foreach($critical as $file) {
    if(!file_exists($file)) {
        echo "MISSING FILE: $file\n";
    } else {
        echo "File '$file' exists.\n";
    }
}
?>
