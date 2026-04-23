<?php
session_start();
require_once 'config/db_config.php';

$stmt = $pdo->query("SELECT * FROM settings");
$settings_raw = $stmt->fetchAll();
$settings = [];
foreach ($settings_raw as $s) { $settings[$s['setting_key']] = $s['setting_value']; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Collection Bag | <?php echo $settings['studio_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #fbfbfb; }
        .cart-item { background: #fff; border: 1px solid #eee; border-radius: 15px; padding: 20px; margin-bottom: 20px; transition: all 0.3s ease; }
        .cart-item:hover { box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .summary-card { background: #fff; border: 1px solid #000; border-radius: 0; padding: 30px; position: sticky; top: 100px; }
        .btn-checkout { background: #000; color: #fff; border-radius: 0; padding: 15px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; border: none; width: 100%; transition: all 0.3s ease; }
        .btn-checkout:hover { background: #333; }
        .empty-cart { padding: 100px 0; text-align: center; }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-light bg-white border-bottom sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php" style="font-family: 'Playfair Display', serif;">
                <i class="fas fa-chevron-left me-2 small"></i> Back to Gallery
            </a>
            <span class="fw-bold">SHOPPING BAG</span>
        </div>
    </nav>

    <div class="container mt-5 pt-3">
        <div class="row g-5">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <h3 class="fw-bold mb-4" style="font-family: 'Playfair Display', serif;">Your Selection</h3>
                <div id="cartItemsContainer">
                    <!-- Loaded via JS -->
                </div>
                
                <div id="emptyCartMessage" class="empty-cart d-none">
                    <i class="fas fa-shopping-basket fa-4x mb-4 opacity-25"></i>
                    <h4 class="fw-bold">Your bag is empty</h4>
                    <p class="text-secondary">Explore the gallery and find a masterpiece that speaks to you.</p>
                    <a href="index.php#gallery" class="btn btn-dark px-5 py-3 mt-3">GO TO GALLERY</a>
                </div>
            </div>

            <!-- Summary -->
            <div class="col-lg-4">
                <div class="summary-card">
                    <h5 class="fw-bold mb-4 text-uppercase" style="letter-spacing: 2px;">Order Summary</h5>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-secondary">Subtotal</span>
                        <span id="cartSubtotal" class="fw-bold">₱0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4 pb-4 border-bottom">
                        <span class="text-secondary">Shipping</span>
                        <span class="text-success fw-bold small">CALCULATED AT CHECKOUT</span>
                    </div>
                    <div class="d-flex justify-content-between mb-5">
                        <h4 class="fw-bold">Total</h4>
                        <h4 id="cartTotal" class="fw-bold">₱0.00</h4>
                    </div>
                    <button class="btn-checkout mb-3" onclick="proceedToCheckout()">Secure Checkout</button>
                    <p class="x-small text-secondary text-center">By clicking Secure Checkout, you agree to our <a href="#" class="text-dark fw-bold">Terms of Service</a>.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let cart = JSON.parse(localStorage.getItem('art_cart')) || [];

        function renderCart() {
            const container = document.getElementById('cartItemsContainer');
            const emptyMsg = document.getElementById('emptyCartMessage');
            container.innerHTML = '';

            if (cart.length === 0) {
                emptyMsg.classList.remove('d-none');
                document.querySelector('.col-lg-4').classList.add('d-none');
                return;
            }

            emptyMsg.classList.add('d-none');
            document.querySelector('.col-lg-4').classList.remove('d-none');

            let total = 0;
            cart.forEach((item, index) => {
                total += parseFloat(item.price);
                const div = document.createElement('div');
                div.className = 'cart-item d-flex align-items-center animate__animated animate__fadeIn';
                div.innerHTML = `
                    <img src="${item.image_url}" class="rounded-3" style="width: 120px; height: 120px; object-fit: cover;">
                    <div class="ms-4 flex-grow-1">
                        <h5 class="fw-bold mb-1">${item.title}</h5>
                        <p class="text-secondary small mb-0">${item.size} | ${item.medium}</p>
                        <h6 class="fw-bold mt-2">₱${parseFloat(item.price).toLocaleString('en-US', {minimumFractionDigits: 2})}</h6>
                    </div>
                    <button class="btn btn-link text-danger p-0" onclick="removeFromCart(${index})">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                `;
                container.appendChild(div);
            });

            document.getElementById('cartSubtotal').textContent = `₱${total.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
            document.getElementById('cartTotal').textContent = `₱${total.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        }

        function removeFromCart(index) {
            cart.splice(index, 1);
            localStorage.setItem('art_cart', JSON.stringify(cart));
            renderCart();
            // Update the main cart count if shared via localStorage
        }

        function proceedToCheckout() {
            Swal.fire({
                title: 'Checkout Info',
                text: 'Multi-item checkout is coming soon! For now, please process your orders individually or contact us on Messenger for a bulk deal.',
                icon: 'info',
                confirmButtonColor: '#000'
            });
        }

        renderCart();
    </script>
</body>
</html>
