<?php 
require_once 'config/db_config.php';

$stmt = $pdo->query("SELECT * FROM settings");
$settings_raw = $stmt->fetchAll();
$settings = [];
foreach ($settings_raw as $s) {
    $settings[$s['setting_key']] = $s['setting_value'];
}

// Logout handling
if (isset($_GET['logout'])) {
    unset($_SESSION['collector_phone']);
    header("Location: portal.php");
    exit();
}

$is_logged_in = isset($_SESSION['collector_phone']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collector Portal | <?php echo $settings['studio_name']; ?></title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <style>
        :root {
            --primary-dark: #0f172a;
            --accent-gold: #c2a35d;
            --glass-bg: rgba(255, 255, 255, 0.8);
            --glass-border: rgba(255, 255, 255, 0.2);
            --font-serif: 'Playfair Display', serif;
            --font-sans: 'Inter', sans-serif;
        }

        body {
            background: #f8fafc;
            font-family: var(--font-sans);
            color: var(--primary-dark);
            min-height: 100vh;
        }

        .portal-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            z-index: -1;
        }

        .navbar-brand {
            font-family: var(--font-serif);
            font-weight: 700;
            letter-spacing: 1px;
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        }

        .login-container {
            max-width: 450px;
            margin: 100px auto;
        }

        .btn-gold {
            background: var(--primary-dark);
            color: white;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-gold:hover {
            background: #1e293b;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .artwork-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            border-radius: 15px;
            overflow: hidden;
            background: white;
        }

        .artwork-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .artwork-img-wrapper {
            aspect-ratio: 4/5;
            overflow: hidden;
            background: #f1f5f9;
        }

        .artwork-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .artwork-card:hover .artwork-img {
            transform: scale(1.05);
        }

        .coa-badge {
            background: rgba(194, 163, 93, 0.1);
            color: var(--accent-gold);
            border: 1px solid var(--accent-gold);
            font-size: 0.65rem;
            padding: 4px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .nav-link-custom {
            color: var(--primary-dark);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: color 0.3s;
        }

        .nav-link-custom:hover {
            color: var(--accent-gold);
        }

        .status-pill {
            font-size: 0.7rem;
            padding: 4px 12px;
            border-radius: 50px;
            font-weight: 600;
        }

        .status-paid { background: #dcfce7; color: #166534; }
        .status-pending { background: #fef9c3; color: #854d0e; }

        /* Certificate Styles */
        .coa-preview {
            border: 10px double #c2a35d;
            padding: 40px;
            background: #fff;
            position: relative;
            font-family: var(--font-serif);
        }
    </style>
</head>
<body>
    <div class="portal-bg"></div>

    <!-- Simple Navbar -->
    <nav class="navbar py-4">
        <div class="container">
            <a class="navbar-brand text-dark" href="index.php">
                <i class="fas fa-chevron-left me-2 small"></i> <?php echo $settings['studio_name']; ?>
            </a>
            <?php if($is_logged_in): ?>
                <div class="d-flex align-items-center gap-3">
                    <span class="small text-secondary d-none d-md-block">Collector: <strong><?php echo $_SESSION['collector_phone']; ?></strong></span>
                    <a href="?logout=1" class="btn btn-sm btn-outline-danger rounded-pill px-3">Logout</a>
                </div>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container">
        <?php if(!$is_logged_in): ?>
            <!-- Login Form -->
            <div class="login-container animate__animated animate__fadeInUp">
                <div class="glass-card p-5">
                    <div class="text-center mb-5">
                        <div class="mb-4">
                            <i class="fas fa-user-shield fa-3x text-accent-gold"></i>
                        </div>
                        <h2 class="fw-bold mb-2">Collector Access</h2>
                        <p class="text-secondary small">Enter your contact number used during purchase to access your private collection and certificates.</p>
                    </div>

                    <form id="collectorLoginForm">
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Contact Number</label>
                            <input type="text" class="form-control form-control-lg bg-light border-0" id="phoneInput" placeholder="e.g. 0912 345 6789" required>
                        </div>
                        <button type="submit" class="btn btn-gold w-100 py-3 mb-3">Verify Access</button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="x-small text-secondary mb-0">Need help? <a href="https://m.me/<?php echo $settings['messenger_id']; ?>" target="_blank" class="text-dark fw-bold">Contact Studio Support</a></p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Dashboard -->
            <div class="dashboard-header mb-5 animate__animated animate__fadeIn">
                <h1 class="display-5 fw-bold mb-2" style="font-family: var(--font-serif);">My Collection</h1>
                <p class="text-secondary">Explore your acquired masterpieces and digital certificates of authenticity.</p>
            </div>

            <div id="collectionGrid" class="row g-4 mb-5">
                <!-- Artworks will be loaded here via AJAX -->
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-dark" role="status"></div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- COA Modal -->
    <div class="modal fade" id="coaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 0;">
                <div class="modal-body p-0">
                    <div id="coaContent" class="coa-preview">
                        <!-- COA details will be dynamic -->
                    </div>
                    <div class="p-4 bg-light text-center border-top">
                        <button class="btn btn-dark px-5" onclick="window.print()"><i class="fas fa-print me-2"></i> Print Certificate</button>
                        <button class="btn btn-link text-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('collectorLoginForm');
            const collectionGrid = document.getElementById('collectionGrid');

            if (loginForm) {
                loginForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const phone = document.getElementById('phoneInput').value;
                    
                    try {
                        const response = await fetch('api/portal_login.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ phone: phone })
                        });
                        const data = await response.json();
                        
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Access Verified',
                                text: 'Welcome back, Collector!',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        } else {
                            Swal.fire('Access Denied', data.message, 'error');
                        }
                    } catch (err) {
                        Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                    }
                });
            }

            if (collectionGrid) {
                loadCollection();
            }

            async function loadCollection() {
                try {
                    const response = await fetch('api/get_collection.php');
                    const data = await response.json();
                    
                    if (data.status === 'success' && data.artworks.length > 0) {
                        collectionGrid.innerHTML = data.artworks.map(art => `
                            <div class="col-md-4 col-lg-3 animate__animated animate__fadeInUp">
                                <div class="artwork-card card h-100 shadow-sm">
                                    <div class="artwork-img-wrapper">
                                        <img src="${art.image_url}" class="artwork-img" alt="${art.title}">
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="fw-bold m-0" style="font-family: var(--font-serif);">${art.title}</h5>
                                            <span class="status-pill status-paid">Acquired</span>
                                        </div>
                                        <p class="small text-secondary mb-4">${art.size} | ${art.medium}</p>
                                        <button onclick="viewCOA(${art.id})" class="btn btn-outline-dark w-100 rounded-pill py-2 small">
                                            <i class="fas fa-certificate me-2 text-accent-gold"></i> View COA
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `).join('');
                    } else {
                        collectionGrid.innerHTML = `
                            <div class="col-12 text-center py-5">
                                <i class="fas fa-layer-group fa-3x text-light mb-3"></i>
                                <h4 class="text-secondary">No masterpieces found in your collection yet.</h4>
                                <p class="small text-secondary">Purchased artworks will appear here once approved by the artist.</p>
                                <a href="index.php#gallery" class="btn btn-gold px-5 mt-3">Browse Gallery</a>
                            </div>
                        `;
                    }
                } catch (err) {
                    collectionGrid.innerHTML = '<p class="text-center text-danger">Failed to load collection.</p>';
                }
            }
        });

        async function viewCOA(artworkId) {
            try {
                const response = await fetch(`api/get_coa.php?id=${artworkId}`);
                const data = await response.json();
                
                if (data.status === 'success') {
                    const coaModal = new bootstrap.Modal(document.getElementById('coaModal'));
                    const coa = data.coa;
                    document.getElementById('coaContent').innerHTML = `
                        <div class="text-center">
                            <h1 style="font-size: 3rem; margin-bottom: 20px;">CERTIFICATE</h1>
                            <h3 style="letter-spacing: 5px; margin-bottom: 40px;">OF AUTHENTICITY</h3>
                            
                            <p class="mb-4">This document certifies that the artwork titled</p>
                            <h2 class="fw-bold mb-4" style="font-size: 2.5rem; color: #000;">"${coa.title}"</h2>
                            
                            <div class="row mb-5 text-uppercase small fw-bold" style="letter-spacing: 2px;">
                                <div class="col-6 text-end border-end pe-4">Medium: ${coa.medium}</div>
                                <div class="col-6 text-start ps-4">Dimensions: ${coa.size}</div>
                            </div>
                            
                            <p class="mb-5 px-5" style="font-style: italic;">is an original, one-of-a-kind hand-painted work of art created by</p>
                            <h3 class="fw-bold mb-2">MATTHEW RILLERA</h3>
                            <p class="small text-secondary mb-5">Quezon City, Philippines</p>
                            
                            <div class="mt-5 pt-5 d-flex justify-content-between align-items-end px-5">
                                <div class="text-center">
                                    <div class="border-bottom border-dark mb-2" style="width: 200px;"></div>
                                    <p class="x-small">Date of Issue: ${coa.date}</p>
                                </div>
                                <div class="text-center">
                                    <img src="assets/img/seal.png" style="width: 80px; opacity: 0.8;" onerror="this.style.display='none'">
                                    <p class="x-small mt-2">Official Studio Seal</p>
                                </div>
                                <div class="text-center">
                                    <div class="border-bottom border-dark mb-2" style="width: 200px;">
                                        <span style="font-family: 'Brush Script MT', cursive; font-size: 1.5rem;">M. Rillera</span>
                                    </div>
                                    <p class="x-small">Artist Signature</p>
                                </div>
                            </div>
                            
                            <div class="mt-5 x-small text-secondary" style="font-family: var(--font-sans);">
                                Security ID: ${coa.security_id}
                            </div>
                        </div>
                    `;
                    coaModal.show();
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            } catch (err) {
                Swal.fire('Error', 'Failed to generate certificate.', 'error');
            }
        }
    </script>
</body>
</html>
