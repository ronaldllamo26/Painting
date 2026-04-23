<?php
// sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);
require_once '../config/db_config.php';
$stmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'studio_name'");
$studioName = $stmt->fetchColumn() ?: "Artist Studio";
?>
<style>
    :root { 
        --sidebar-width: 280px; 
        --primary-accent: #000;
        --glass-bg: rgba(255, 255, 255, 0.95);
    }
    body { background: #f8fafc; }
    .sidebar { 
        background: var(--glass-bg); 
        backdrop-filter: blur(10px);
        min-height: 100vh; 
        border-right: 1px solid rgba(0,0,0,0.05); 
        position: fixed;
        width: var(--sidebar-width);
        z-index: 1050;
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 10px 0 30px rgba(0,0,0,0.02);
    }
    .main-content { 
        margin-left: var(--sidebar-width); 
        padding: 40px; 
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        min-height: 100vh;
    }
    
    /* Mobile Sidebar Toggle */
    @media (max-width: 991.98px) {
        .sidebar { 
            transform: translateX(calc(-1 * var(--sidebar-width))); 
            box-shadow: none;
        }
        .sidebar.active { 
            transform: translateX(0); 
            box-shadow: 20px 0 50px rgba(0,0,0,0.1);
        }
        .main-content { 
            margin-left: 0 !important; 
            padding: 20px 15px !important; 
            width: 100%;
        }
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.4);
            backdrop-filter: blur(4px);
            z-index: 1040;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .sidebar-overlay.active { 
            display: block; 
            opacity: 1;
        }
    }

    .nav-link { 
        color: #64748b; 
        padding: 1.1rem 1.75rem; 
        font-weight: 600;
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
        border-radius: 12px;
        margin: 4px 15px;
    }
    .nav-link:hover { color: var(--primary-accent); background: #f1f5f9; transform: translateX(5px); }
    .nav-link.active { 
        color: #fff; 
        background: var(--primary-accent); 
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    .nav-link i { width: 28px; font-size: 1.2rem; margin-right: 12px; }
    
    .mobile-nav {
        display: none;
        background: #fff;
        padding: 12px 20px;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        position: sticky;
        top: 0;
        z-index: 1001;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    @media (max-width: 991.98px) {
        .mobile-nav { display: flex; align-items: center; justify-content: space-between; }
    }
</style>

<div class="mobile-nav px-4 py-3">
    <div class="d-flex align-items-center">
        <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">
            <?php echo substr($studioName, 0, 1); ?>
        </div>
        <h6 class="m-0 fw-bold" style="letter-spacing: -0.5px;"><?php echo htmlspecialchars($studioName); ?></h6>
    </div>
    <button class="btn btn-dark btn-sm rounded-3 shadow-sm border-0 d-flex align-items-center justify-content-center" id="sidebarToggle" style="width: 40px; height: 40px;">
        <i class="fas fa-bars"></i>
    </button>
</div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="sidebar" id="sidebar">
    <div class="p-4 border-bottom mb-4 text-center d-none d-lg-block">
        <h6 class="fw-bold mb-0 text-uppercase letter-spacing-1"><?php echo htmlspecialchars($studioName); ?></h6>
        <small class="text-secondary">Admin Access</small>
    </div>
    <nav class="nav flex-column">
        <a class="nav-link" href="../index.php"><i class="fas fa-external-link-alt"></i> View Gallery</a>
        <a class="nav-link <?php echo ($current_page == 'index.php' || $current_page == 'edit_artwork.php') ? 'active' : ''; ?>" href="index.php"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a class="nav-link <?php echo $current_page == 'upload.php' ? 'active' : ''; ?>" href="upload.php"><i class="fas fa-plus-circle"></i> Upload Art</a>
        <a class="nav-link <?php echo $current_page == 'commissions.php' ? 'active' : ''; ?>" href="commissions.php"><i class="fas fa-palette"></i> Commissions</a>
        <a class="nav-link <?php echo $current_page == 'settings.php' ? 'active' : ''; ?>" href="settings.php"><i class="fas fa-cog"></i> Settings</a>
        <a class="nav-link text-danger mt-5" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</div>

<script>
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if(toggle) {
        toggle.onclick = () => {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        };
    }
    overlay.onclick = () => {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
    };
</script>
