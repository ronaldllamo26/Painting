# 🎨 Matthew Rillera's Studio - Art Marketplace

https://github.com/user-attachments/assets/18167ea8-ac33-4606-9741-8ee22ccc5490

A premium, modern, and fully responsive Art Marketplace and Gallery system designed for contemporary artists. This platform allows artists to showcase their hand-painted masterpieces, manage inventory, and process orders through a professional administrative dashboard.


## ✨ Features

### 🖼️ Public Gallery
- **Modern Minimalist UI**: Built with a "less is more" aesthetic using Playfair Display and Inter typography.
- **Dynamic Filtering**: Quickly sort artworks by category (Abstract, Prints, Portrait, etc.) without page reloads.
- **Responsive Modal**: View artwork details, high-res images, and secure order forms optimized for any device POV.
- **Smart Search**: Real-time search for art titles, styles, or colors.

### 🔐 Admin Dashboard
- **Comprehensive Overview**: Monitor total revenue, pending orders, and inventory at a glance.
- **AI-Assisted Uploads**: integrated AI suggestion logic for descriptions and tagging (Ready for OpenAI/Vision integration).
- **Settings Management**: Update studio name, contact info, social links, and GCash QR codes directly from the UI.
- **Order Processing**: Manage customer inquiries and verify payment receipts with ease.
- **Secure Access**: Hashed password protection and session-based authentication.

## 🛠️ Tech Stack
- **Frontend**: HTML5, Vanilla CSS3, Javascript (ES6), Bootstrap 5.
- **Backend**: PHP 8.x (PDO for secure database interactions).
- **Database**: MySQL.
- **Libraries**: SweetAlert2 (Popups), FontAwesome 6 (Icons), Animate.css.

## 🚀 Installation & Setup

1. **Clone the repository**:
   ```bash

   git clone https://github.com/ronaldllamo26/Painting.git
   ```

2. **Database Setup**:

https://github.com/user-attachments/assets/63375138-b7ae-44d8-9881-96f15cfcecd0


   - Create a database named `art_gallery`.
   - Import the provided SQL file (if applicable) or use the following tables: `admin`, `artworks`, `orders`, `settings`.

3. **Configuration**:
   - Update `config/db_config.php` with your local database credentials.

4. **Run via XAMPP**:
   - Move the folder to `C:/xampp/htdocs/`.
   - Access via `http://localhost/Painting/`.

## 📱 Mobile POV
The entire platform is fully optimized for mobile and tablet views, ensuring a seamless experience for both the artist and the collectors.

## 📜 License
© 2026 Matthew Rillera's Studio. All Rights Reserved.

---
*Developed with ❤️ for Filipino Artists.*
