CREATE DATABASE IF NOT EXISTS art_gallery;
USE art_gallery;

-- Artworks Table
CREATE TABLE IF NOT EXISTS artworks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    size VARCHAR(100),
    medium VARCHAR(100),
    image_url VARCHAR(500) NOT NULL,
    cloudinary_id VARCHAR(255),
    ai_description TEXT,
    ai_tags TEXT, -- Comma-separated tags
    is_negotiable BOOLEAN DEFAULT FALSE,
    status ENUM('Available', 'Pending', 'Sold') DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    artwork_id INT NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    contact_number VARCHAR(50) NOT NULL,
    address TEXT NOT NULL,
    payment_method ENUM('GCash', 'COD') NOT NULL,
    receipt_url VARCHAR(500), -- For GCash screenshots
    coa_number VARCHAR(50) UNIQUE,
    order_status ENUM('Pending', 'Approved', 'Cancelled') DEFAULT 'Pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (artwork_id) REFERENCES artworks(id) ON DELETE CASCADE
);

-- Commissions Table
CREATE TABLE IF NOT EXISTS commissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(255) NOT NULL,
    contact_number VARCHAR(50) NOT NULL,
    email VARCHAR(255),
    subject VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    budget DECIMAL(10, 2),
    status ENUM('Pending', 'Accepted', 'Rejected', 'Completed') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Settings Table
CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT
);

-- Admin Table (For secure login)
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL -- Hashed
);

-- Initial Data
INSERT INTO admin (username, password) VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- password: admin123

INSERT INTO settings (setting_key, setting_value) VALUES 
('studio_name', "Matthew Rillera's Studio"),
('contact_number', '0956 993 2911'),
('email_address', 'johnmatthewrillera@gmail.com'),
('facebook_link', 'https://www.facebook.com/profile.php?id=100068728255359'),
('messenger_id', '100068728255359'),
('gcash_qr', 'assets/img/gcash_qr.png'),
('openai_api_key', '');
