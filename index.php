<?php 
require_once 'config/db_config.php';
$stmt = $pdo->query("SELECT * FROM settings");
$settings_raw = $stmt->fetchAll();
$settings = [];
foreach ($settings_raw as $s) {
    $settings[$s['setting_key']] = $s['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matthew Rillera's Studio | Original Paintings</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- FsLightbox -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fslightbox/3.0.9/index.min.js"></script>
</head>
<body>

    <!-- Announcement Bar -->
    <div class="announcement-bar py-2 text-center small text-white" style="background: #000; letter-spacing: 2px;">
        AUTHENTIC HAND-PAINTED MASTERPIECES | CERTIFICATE OF AUTHENTICITY INCLUDED | COMMISSIONS OPEN
    </div>

    <!-- Navbar -->
    <nav class="navbar sticky-top">
        <div class="container align-items-center">
            <div class="d-flex align-items-center flex-grow-1">
                <!-- Nav Links -->
                <div class="nav-links d-none d-md-flex gap-4 me-5">
                    <a href="#" class="nav-link-custom">Home</a>
                    <a href="#gallery" class="nav-link-custom">Gallery</a>
                    <a href="javascript:void(0)" class="nav-link-custom" data-bs-toggle="modal" data-bs-target="#trackModal">Track Order</a>
                </div>

                <!-- Search Bar -->
                <div class="search-wrapper flex-grow-1 mx-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0 ps-0"><i class="fas fa-search text-secondary"></i></span>
                        <input type="text" id="navbarSearch" class="form-control border-0 bg-transparent ps-2" placeholder="Search for art, styles, or colors..." aria-label="Search">
                    </div>
                </div>
            </div>

        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section" style="background-image: url('assets/img/hero.png');">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <p class="hero-subtitle">Original Artworks & Fine Prints</p>
            <h1 class="hero-title">Matthew Rillera's Studio</h1>
            <a href="#gallery" class="btn-gallery">Explore Collection</a>
        </div>
    </header>

    <!-- Filter Bar & Gallery -->
    <div class="container mt-5 pt-5" id="gallery">
        <div class="text-center mb-5">
            <h6 class="text-uppercase text-secondary mb-2" style="letter-spacing: 3px;">The Collection</h6>
            <h2 class="display-6" style="font-family: var(--font-serif);">Available Artworks</h2>
        </div>
        <div class="filter-bar" id="tagFilters">
            <button class="filter-btn active" data-filter="all">All Artworks</button>
            <!-- Dynamic tags will be injected here -->
        </div>
    </div>

    <!-- Gallery Grid -->
    <div class="container mb-5">
        <div class="row g-5" id="galleryContainer" style="min-height: 1200px;">
            <!-- Loading Spinner -->
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-dark" role="status"></div>
            </div>
        </div>
        <!-- Pagination UI -->
        <nav class="mt-5">
            <ul class="pagination justify-content-center" id="paginationContainer"></ul>
        </nav>
    </div>

    <!-- About Section -->
    <section class="py-5 bg-white border-bottom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-5 mb-4 mb-md-0">
                    <img src="https://images.unsplash.com/photo-1549490349-8643362247b5?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" class="img-fluid border" alt="Artist at work">
                </div>
                <div class="col-md-7 ps-md-5">
                    <h6 class="text-uppercase text-secondary mb-3" style="letter-spacing: 3px;">The Artist</h6>
                    <h2 class="display-5 mb-4" style="font-family: var(--font-serif);">Matthew Rillera</h2>
                    <p class="lead text-secondary mb-4">A contemporary painter based in the Philippines, Matthew specializes in emotive landscapes and abstract expressions that capture the raw beauty of Filipino life.</p>
                    <p class="text-secondary mb-5">Each piece in this gallery is a result of meticulous layers, blending traditional techniques with modern perspectives. His works have been featured in numerous local exhibitions and private collections worldwide.</p>
                    <a href="#" class="text-dark text-decoration-none fw-bold border-bottom border-dark pb-1">LEARN MORE ABOUT HIS STORY</a>
                </div>
            </div>
        </div>
    </section>



    <!-- Process Section -->
    <section class="py-5 bg-light">
        <div class="container text-center">
            <h6 class="text-uppercase text-secondary mb-4" style="letter-spacing: 3px;">The Studio Process</h6>
            <div class="row g-4">
                <div class="col-md-4">
                    <i class="fas fa-palette fa-2x mb-3"></i>
                    <h5>Original Art</h5>
                    <p class="small text-secondary">Every piece is 100% hand-painted and unique.</p>
                </div>
                <div class="col-md-4">
                    <i class="fas fa-certificate fa-2x mb-3"></i>
                    <h5>Authenticity</h5>
                    <p class="small text-secondary">Includes a signed certificate of authenticity.</p>
                </div>
                <div class="col-md-4">
                    <i class="fas fa-truck-fast fa-2x mb-3"></i>
                    <h5>Delivery Options</h5>
                    <p class="small text-secondary">Lalamove (Metro Manila) or Scheduled Meet-ups available.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-5 bg-white border-top">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="mb-4" style="font-family: var(--font-serif);"><?php echo $settings['studio_name']; ?></h5>
                    <p class="small text-secondary mb-4">Sharing the beauty of the Philippines through contemporary art. Visit our studio in Quezon City.</p>
                    <div class="d-flex gap-3 align-items-center">
                        <a href="<?php echo $settings['facebook_link']; ?>" target="_blank" class="text-dark" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="mailto:<?php echo $settings['email_address']; ?>" class="text-dark" title="Email"><i class="fas fa-envelope"></i></a>
                        <a href="tel:<?php echo str_replace(' ', '', $settings['contact_number']); ?>" class="text-dark" title="Call Us"><i class="fas fa-phone"></i></a>
                        <span class="small text-secondary ms-2"><?php echo $settings['contact_number']; ?></span>
                    </div>
                </div>
                <div class="col-md-2 col-6 mb-4">
                    <h6 class="text-uppercase small fw-bold mb-4">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="#gallery" class="text-decoration-none text-secondary small d-block mb-2" onclick="filterByTag('All')">New Arrivals</a></li>
                        <li><a href="https://m.me/<?php echo $settings['messenger_id']; ?>" target="_blank" class="text-decoration-none text-secondary small d-block mb-2">Commission Art</a></li>
                        <li><a href="#gallery" class="text-decoration-none text-secondary small d-block mb-2" onclick="filterByTag('Prints')">Prints</a></li>
                        <li><a href="#about" class="text-decoration-none text-secondary small d-block mb-2">About</a></li>
                    </ul>
                </div>
                <div class="col-md-2 col-6 mb-4">
                    <h6 class="text-uppercase small fw-bold mb-4">Customer Care</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-decoration-none text-secondary small d-block mb-2" onclick="Swal.fire('Shipping Policy', 'We ship nationwide via Lalamove or J&T. Shipping fee is shouldered by the buyer.', 'info')">Shipping Policy</a></li>
                        <li><a href="#" class="text-decoration-none text-secondary small d-block mb-2" onclick="Swal.fire('Returns', 'All sales are final. We ensure paintings are securely packed before shipping.', 'info')">Returns</a></li>
                        <li><a href="https://m.me/<?php echo $settings['messenger_id']; ?>" target="_blank" class="text-decoration-none text-secondary small d-block mb-2">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h6 class="text-uppercase small fw-bold mb-4">Newsletter</h6>
                    <p class="small text-secondary mb-3">Subscribe to receive updates on new exhibitions and limited print releases.</p>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control border-dark" placeholder="Email address">
                        <button class="btn btn-dark" type="button">JOIN</button>
                    </div>
                </div>
            </div>
            <hr class="my-5">
            <p class="text-center text-secondary small"><a href="admin/index.php" class="text-decoration-none text-secondary">&copy;</a> 2026 Matthew Rillera's Studio. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Buy Now Modal (Light Theme) -->
    <div class="modal fade" id="buyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content glass-modal">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="modalArtTitle" style="font-family: var(--font-serif);">Art Title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-5 mb-4">
                            <a data-fslightbox="modal-gallery" href="">
                                <img id="modalArtImg" src="" class="img-fluid" alt="Painting">
                            </a>
                        </div>
                        <div class="col-md-7">
                            <div class="mb-3 mb-md-4">
                                <h2 id="modalArtTitleInner" class="fw-bold mb-1 d-none d-md-block" style="font-family: var(--font-serif); font-size: 2rem;"></h2>
                                <div class="d-flex align-items-center flex-wrap gap-2 mb-2 mb-md-3">
                                    <h3 class="fw-bold text-accent m-0 modal-price-text" id="modalArtPrice">₱0.00</h3>
                                    <span id="modalArtDetails" class="text-secondary x-small"></span>
                                </div>
                                <div class="bg-light p-2 p-md-3 border-start border-dark border-4">
                                    <p id="modalArtDesc" class="text-secondary m-0 x-small" style="line-height: 1.5;"></p>
                                </div>
                            </div>
                            
                            <hr class="my-3 my-md-4">
                            
                            <form id="checkoutForm">
                                <input type="hidden" name="artwork_id" id="checkoutArtId">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Full Name</label>
                                    <input type="text" class="form-control" name="customer_name" required placeholder="Juan Dela Cruz">
                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label class="form-label small fw-bold">Contact Number</label>
                                        <input type="text" class="form-control" name="contact_number" required placeholder="0912 345 6789">
                                    </div>
                                    <div class="col">
                                        <label class="form-label small fw-bold">Payment & Delivery</label>
                                        <select class="form-select form-control" name="payment_method" id="paymentMethod" required>
                                            <option value="GCash">GCash (Payment First)</option>
                                            <option value="COD">Meet-up (Cash on Delivery)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Delivery / Meet-up Details</label>
                                    <textarea class="form-control" name="address" rows="2" required placeholder="For Lalamove: Complete Address&#10;For Meet-up: Preferred Location (e.g. SM North)"></textarea>
                                    <p class="x-small text-secondary mt-2"><i class="fas fa-info-circle"></i> Note: Lalamove delivery fee is shouldered by the client upon arrival.</p>
                                </div>

                                <!-- GCash Section -->
                                <div id="gcashSection" class="d-none">
                                    <div class="alert bg-light border text-center p-4">
                                        <p class="small mb-2 fw-bold text-danger">PAYMENT FIRST BEFORE DELIVERY</p>
                                        <p class="small mb-3">Scan QR to pay via GCash</p>
                                        <img src="<?php echo $settings['gcash_qr']; ?>" class="img-fluid mb-3 border" style="max-width: 180px;" alt="GCash QR">
                                        <p class="fw-bold mb-0"><?php echo $settings['studio_name']; ?> - <?php echo $settings['contact_number']; ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Upload Receipt Screenshot</label>
                                        <input type="file" class="form-control" name="receipt" id="receiptInput">
                                    </div>
                                </div>

                                <div id="negotiablePriceSection" class="d-none mb-3">
                                    <label class="form-label small fw-bold text-accent">Proposed Offer (PHP)</label>
                                    <input type="number" class="form-control border-accent" name="proposed_price" placeholder="Enter your offer...">
                                    <p class="x-small text-secondary mt-1">Note: This is subject to artist approval.</p>
                                </div>

                                <button type="submit" class="btn btn-gallery w-100 mt-3" id="btnPlaceOrder">Secure Order</button>
                                <a href="https://m.me/<?php echo $settings['messenger_id']; ?>" target="_blank" class="btn btn-outline-dark w-100 mt-2 d-none" id="btnNegotiate">
                                    <i class="fab fa-facebook-messenger me-2"></i> Chat to Negotiate
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Track Order Modal -->
    <div class="modal fade" id="trackModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glass-modal">
                <div class="modal-header border-0">
                    <h5 class="modal-title" style="font-family: var(--font-serif);">Track My Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="trackForm">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-uppercase" style="letter-spacing: 1px;">Contact Number</label>
                            <input type="text" class="form-control" name="contact_number" id="trackContact" placeholder="e.g. 0912 345 6789" required>
                        </div>
                        <button type="submit" class="btn btn-gallery w-100">Find My Order</button>
                    </form>

                    <div id="trackResult" class="mt-4 d-none">
                        <hr>
                        <div id="orderList"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
