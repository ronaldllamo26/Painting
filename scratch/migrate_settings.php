<?php
require_once 'config/db_config.php';
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        id INT PRIMARY KEY AUTO_INCREMENT,
        setting_key VARCHAR(50) UNIQUE,
        setting_value TEXT
    )");
    
    $defaults = [
        ['studio_name', "Matthew Rillera's Studio"],
        ['contact_number', '0956 993 2911'],
        ['email_address', 'johnmatthewrillera@gmail.com'],
        ['facebook_link', 'https://www.facebook.com/profile.php?id=100068728255359'],
        ['messenger_id', '100068728255359'],
        ['gcash_qr', 'assets/img/gcash-qr.png']
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)");
    foreach ($defaults as $row) {
        $stmt->execute($row);
    }
    
    echo "Success: settings table created and defaults inserted.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
