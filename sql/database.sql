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
    order_status ENUM('Pending', 'Approved', 'Cancelled') DEFAULT 'Pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (artwork_id) REFERENCES artworks(id) ON DELETE CASCADE
);

-- Admin Table (For secure login)
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL -- Hashed
);

-- Insert default admin (password: admin123)
INSERT INTO admin (username, password) VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
