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
    <title>Matthew Rillera's Studio | Original Masterpieces</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Playfair+Display:wght@700;900&family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token']; ?>">
</head>
<body>

    <!-- Announcement Bar -->
    <div class="announcement-bar py-2 overflow-hidden text-white" style="background: #000; letter-spacing: 2px; font-family: 'Outfit', sans-serif;">
        <div class="marquee-content">
            <span>AUTHENTIC HAND-PAINTED MASTERPIECES</span>
            <span>CERTIFICATE OF AUTHENTICITY INCLUDED</span>
            <span>FREE SHIPPING NATIONWIDE</span>
            <span>COMMISSIONS OPEN FOR 2026</span>
            <!-- Duplicate for seamless scroll -->
            <span>AUTHENTIC HAND-PAINTED MASTERPIECES</span>
            <span>CERTIFICATE OF AUTHENTICITY INCLUDED</span>
            <span>FREE SHIPPING NATIONWIDE</span>
            <span>COMMISSIONS OPEN FOR 2026</span>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top bg-white py-3 shadow-sm">
        <div class="container">
            <!-- Mobile Brand -->
            <a class="navbar-brand d-lg-none fw-bold" href="#" style="letter-spacing: 2px;">M. RILLERA</a>
            
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <i class="fas fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse justify-content-center" id="mainNavbar">
                <!-- Desktop Brand (Hidden on mobile) -->
                <a class="navbar-brand d-none d-lg-block position-absolute start-0 ms-4 fw-bold" href="#" style="letter-spacing: 2px;">M. RILLERA</a>

                <div class="navbar-nav gap-lg-4 py-3 py-lg-0">
                    <a href="#" class="nav-link-custom active">HOME</a>
                    <a href="#gallery" class="nav-link-custom">GALLERY</a>
                    <a href="#" class="nav-link-custom" data-bs-toggle="modal" data-bs-target="#trackOrderModal">TRACK ORDER</a>
                    <a href="#" class="nav-link-custom" data-bs-toggle="modal" data-bs-target="#verifyCOAModal">VERIFY COA</a>
                    <a href="#" class="nav-link-custom" data-bs-toggle="modal" data-bs-target="#commissionModal">COMMISSION</a>
                    <a href="portal.php" class="nav-link-custom fw-bold"><i class="fas fa-user-circle me-1"></i> COLLECTOR PORTAL</a>
                </div>
            </div>

            <!-- Always Visible Actions -->
            <div class="d-flex align-items-center gap-3 ms-auto">
                <div class="search-wrapper d-none d-md-block">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search text-secondary"></i></span>
                        <input type="text" id="navbarSearch" class="form-control bg-light border-0" placeholder="Search art...">
                    </div>
                </div>
                <div class="position-relative cursor-pointer" onclick="openCart()">
                    <i class="fas fa-shopping-bag fa-lg"></i>
                    <span class="cart-count position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark">0</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section" style="background: url('assets/img/hero.png') center/cover; height: 80vh; position: relative;">
        <div class="hero-overlay" style="position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.3);"></div>
        <div class="container h-100 d-flex flex-column justify-content-center align-items-center text-center text-white position-relative">
            <p class="text-uppercase mb-2 animate__animated animate__fadeInDown" style="letter-spacing: 5px; font-weight: 300;">Original Masterpieces</p>
            <h1 class="display-1 fw-bold mb-4 animate__animated animate__fadeInUp" style="font-family: var(--font-serif);">Matthew Rillera's Studio</h1>
            <a href="#gallery" class="btn btn-outline-light px-5 py-3 rounded-0 animate__animated animate__fadeInUp animate__delay-1s">EXPLORE COLLECTION</a>
        </div>
    </header>

    <main class="container py-5" id="gallery">
        <div class="text-center mb-5 pt-5">
            <p class="text-secondary small text-uppercase mb-2" style="letter-spacing: 4px;">Browse the Archive</p>
            <h2 class="display-4 fw-bold mb-4" style="font-family: var(--font-serif);">Available Artworks</h2>
            <div class="filter-bar d-flex flex-wrap justify-content-center gap-4 mb-4" id="tagFilters">
                <button class="filter-btn active" data-filter="all">All Pieces</button>
            </div>
        </div>

        <!-- Artwork Grid -->
        <div class="row g-4" id="galleryContainer">
            <!-- Artworks injected here -->
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-5">
            <nav id="paginationContainer">
                <!-- Pagination injected here -->
            </nav>
        </div>
    </main>

    <!-- Artist Spotlight Section -->
    <section class="py-5 bg-light overflow-hidden">
        <div class="container py-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 animate__animated animate__fadeInLeft">
                    <div class="position-relative">
                        <img src="assets/img/artist_process.jpg" class="img-fluid shadow-lg" alt="Artist at work" style="border-radius: 4px;">
                    </div>
                </div>
                <div class="col-lg-6 animate__animated animate__fadeInRight">
                    <p class="text-secondary small text-uppercase mb-2" style="letter-spacing: 4px;">The Artist</p>
                    <h2 class="display-5 fw-bold mb-4" style="font-family: var(--font-serif);">Matthew Rillera</h2>
                    <p class="lead text-secondary mb-4">A contemporary painter based in the Philippines, Matthew specializes in emotive landscapes and abstract expressions that capture the raw beauty of Filipino life.</p>
                    <p class="text-secondary mb-5">Each piece in this gallery is a result of meticulous layers, blending traditional techniques with modern perspectives. His works have been featured in numerous local exhibitions and private collections worldwide.</p>
                    <a href="#" class="text-dark fw-bold text-decoration-none border-bottom border-2 border-dark pb-1 text-uppercase small" style="letter-spacing: 2px;">Learn more about his story</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Cart Modal -->
    <div class="modal fade" id="cartModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <div class="modal-body p-4 p-md-5">
                    <h4 class="fw-bold mb-4">Your Collection</h4>
                    <div id="cartItemsList" class="mb-4"></div>
                    <div id="cartEmptyMsg" class="text-center py-4 d-none">
                        <i class="fas fa-shopping-bag fa-3x mb-3 opacity-25"></i>
                        <p class="text-secondary">Your bag is empty.</p>
                    </div>
                    <button class="btn btn-dark w-100 py-3 rounded-pill fw-bold" data-bs-dismiss="modal">CONTINUE SHOPPING</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Buy/Details Modal -->
    <div class="modal fade" id="buyModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 0;">
                <div class="modal-body p-0">
                    <div class="row g-0">
                        <div class="col-lg-7 bg-light">
                            <div class="magnifier-container h-100 d-flex align-items-center justify-content-center p-3 p-md-5" id="magnifierContainer">
                                <img src="" id="modalArtImg" class="img-fluid shadow-lg" style="max-height: 600px;">
                                <div class="magnifier-loupe" id="magnifierLoupe"></div>
                            </div>
                        </div>
                        <div class="col-lg-5 p-4 p-md-5 bg-white d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div>
                                    <h2 id="modalArtTitle" class="fw-bold mb-1" style="font-family: var(--font-serif);"></h2>
                                    <p id="modalArtDetails" class="text-secondary small text-uppercase" style="letter-spacing: 2px;"></p>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            
                            <p id="modalArtDesc" class="text-secondary mb-4" style="line-height: 1.8;"></p>
                            <h3 id="modalArtPrice" class="fw-bold mb-4"></h3>

                            <!-- Social Sharing -->
                            <div class="d-flex gap-3 mb-4 align-items-center">
                                <span class="small fw-bold text-uppercase" style="letter-spacing: 1px;">Share Piece:</span>
                                <a href="#" id="shareFB" target="_blank" class="text-dark hover-scale"><i class="fab fa-facebook fa-lg"></i></a>
                                <a href="#" id="shareMessenger" target="_blank" class="text-dark hover-scale"><i class="fab fa-facebook-messenger fa-lg"></i></a>
                                <a href="javascript:void(0)" onclick="copyLink()" class="text-dark hover-scale"><i class="fas fa-link fa-lg"></i></a>
                            </div>

                            <form id="checkoutForm" class="mt-auto">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <input type="hidden" name="artwork_id" id="checkoutArtId">
                                <div class="mb-3">
                                    <input type="text" class="form-control rounded-0" name="customer_name" placeholder="Full Name" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-7">
                                        <input type="text" class="form-control rounded-0" name="contact_number" id="trackContact" placeholder="Contact Number" required>
                                    </div>
                                    <div class="col-5">
                                        <select class="form-select rounded-0" name="payment_method" id="paymentMethod" required>
                                            <option value="GCash">GCash</option>
                                            <option value="COD">Meet-up</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <textarea class="form-control rounded-0" name="address" rows="2" placeholder="Delivery Address / Meet-up Location" required></textarea>
                                </div>
                                <div id="gcashSection" class="mb-3 p-3 border text-center bg-light rounded shadow-sm">
                                    <p class="small fw-bold mb-2 text-dark">Scan to Pay via GCash</p>
                                    <img src="<?php echo $settings['gcash_qr']; ?>" class="img-fluid mb-3 rounded" style="max-width: 150px;">
                                    <div class="text-start">
                                        <label class="form-label x-small fw-bold text-primary" style="letter-spacing: 1px;">UPLOAD GCASH RECEIPT (SCREENSHOT)</label>
                                        <input type="file" class="form-control form-control-sm rounded-0" name="receipt" id="receiptInput" accept="image/*">
                                        <p class="x-small text-muted mt-1 mb-0">Please upload a clear screenshot of your payment receipt.</p>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-dark w-100 py-3 rounded-0 fw-bold" id="btnPlaceOrder">PLACE ORDER</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Track Order Modal -->
    <div class="modal fade" id="trackOrderModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <div class="modal-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <h4 class="fw-bold">Track My Order</h4>
                        <p class="text-secondary small">Enter your contact number to see your order status.</p>
                    </div>
                    <form id="trackForm">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <div class="mb-3">
                            <input type="text" id="trackContact" class="form-control form-control-lg text-center rounded-pill" placeholder="e.g. 09123456789" required>
                        </div>
                        <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill fw-bold">TRACK NOW</button>
                    </form>
                    <div id="trackResult" class="mt-4 d-none">
                        <hr>
                        <div id="orderList"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Verify COA Modal -->
    <div class="modal fade" id="verifyCOAModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <div class="modal-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-shield-alt fa-3x mb-3 text-dark"></i>
                        <h4 class="fw-bold">Verify Authenticity</h4>
                        <p class="text-secondary small">Enter the Certificate of Authenticity (COA) serial number.</p>
                    </div>
                    <form id="verifyForm">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <div class="mb-3">
                            <input type="text" name="coa_number" class="form-control form-control-lg text-center rounded-pill" placeholder="e.g. COA-12345-6789" required>
                        </div>
                        <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill fw-bold">VERIFY PIECE</button>
                    </form>
                    <div id="verifyResult" class="mt-4 d-none"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Commission Modal -->
    <div class="modal fade" id="commissionModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <div class="modal-body p-0 overflow-hidden">
                    <div class="row g-0">
                        <div class="col-md-5 d-none d-md-block" style="background: url('assets/img/hero.png') center/cover;">
                            <div class="h-100 w-100 bg-dark bg-opacity-25 d-flex align-items-end p-4 text-white">
                                <div>
                                    <h3 class="fw-bold">Custom Vision</h3>
                                    <p class="small mb-0">Let's turn your idea into a timeless masterpiece.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7 p-4 p-md-5">
                            <h4 class="fw-bold mb-4">Request a Commission</h4>
                            <form id="commissionForm">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <div class="mb-3">
                                    <input type="text" name="customer_name" class="form-control" placeholder="Your Name" required>
                                </div>
                                <div class="mb-3">
                                    <input type="text" name="contact_number" class="form-control" placeholder="Contact Number" required>
                                </div>
                                <div class="mb-3">
                                    <input type="text" name="subject" class="form-control" placeholder="Project Subject (e.g. Family Portrait)" required>
                                </div>
                                <div class="mb-3">
                                    <textarea name="description" class="form-control" rows="4" placeholder="Describe your vision (size, medium, additional details...)" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill fw-bold">SEND REQUEST</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white pt-5 pb-4 mt-5">
        <div class="container">
            <div class="row g-4 mb-5">
                <div class="col-lg-4">
                    <h5 class="fw-bold mb-4" style="font-family: var(--font-serif);">Matthew Rillera</h5>
                    <p class="small text-secondary" style="line-height: 1.8;">Original hand-painted artworks for the modern collector. Every piece comes with a signed Certificate of Authenticity.</p>
                    <div class="d-flex gap-3 mt-4">
                        <a href="https://www.facebook.com/profile.php?id=100068728255359" target="_blank" class="text-secondary hover-white"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="mailto:johnmatthewrillera@gmail.com" class="text-secondary hover-white"><i class="fas fa-envelope fa-lg"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h6 class="fw-bold mb-4 small text-uppercase" style="letter-spacing: 2px;">Quick Links</h6>
                    <ul class="list-unstyled small text-secondary">
                        <li class="mb-2"><a href="#gallery" class="text-decoration-none text-secondary hover-white">Gallery</a></li>
                        <li class="mb-2"><a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#commissionModal" class="text-decoration-none text-secondary hover-white">Commissions</a></li>
                        <li class="mb-2"><a href="portal.php" class="text-decoration-none text-secondary hover-white">Collector Access</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h6 class="fw-bold mb-4 small text-uppercase" style="letter-spacing: 2px;">Information</h6>
                    <ul class="list-unstyled small text-secondary">
                        <li class="mb-2">Shipping & Delivery</li>
                        <li class="mb-2"><a href="terms.php" class="text-decoration-none text-secondary hover-white">Terms of Service</a></li>
                        <li class="mb-2"><a href="privacy.php" class="text-decoration-none text-secondary hover-white">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-4">
                    <h6 class="fw-bold mb-4 small text-uppercase" style="letter-spacing: 2px;">Studio Location</h6>
                    <p class="small text-secondary mb-2">Quezon City / Metro Manila, Philippines</p>
                    <p class="small text-secondary mb-0">Contact: 0956 993 2911</p>
                </div>
            </div>
            <hr class="border-secondary opacity-25">
            <div class="text-center pt-3">
                <p class="x-small text-secondary mb-0"><a href="admin/login.php" class="text-decoration-none text-secondary">&copy;</a> 2026 Matthew Rillera's Studio. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Floating Messenger FAB -->
    <a href="https://www.messenger.com/t/100068728255359" target="_blank" class="messenger-fab">
        <i class="fab fa-facebook-messenger"></i>
    </a>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
