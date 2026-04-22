<?php
// sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);
require_once '../config/db_config.php';
$stmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'studio_name'");
$studioName = $stmt->fetchColumn() ?: "Artist Studio";
?>
<style>
    :root { --sidebar-width: 260px; }
    .sidebar { 
        background: #fff; 
        min-height: 100vh; 
        border-right: 1px solid #e3e6f0; 
        position: fixed;
        width: var(--sidebar-width);
        z-index: 1000;
        transition: all 0.3s;
    }
    .main-content { 
        margin-left: var(--sidebar-width); 
        padding: 40px; 
        transition: all 0.3s;
    }
    
    /* Mobile Sidebar Toggle */
    @media (max-width: 991.98px) {
        .sidebar { margin-left: calc(-1 * var(--sidebar-width)); }
        .sidebar.active { margin-left: 0; }
        .main-content { margin-left: 0; padding: 20px; }
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
        .sidebar-overlay.active { display: block; }
    }

    .nav-link { 
        color: #6e707e; 
        padding: 1rem 1.5rem; 
        font-weight: 600;
        display: flex;
        align-items: center;
        transition: all 0.2s;
    }
    .nav-link:hover, .nav-link.active { color: #000; background: #f8f9fc; }
    .nav-link.active { border-right: 4px solid #000; }
    .nav-link i { width: 25px; font-size: 1.1rem; margin-right: 10px; }
    
    .mobile-nav {
        display: none;
        background: #fff;
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        position: sticky;
        top: 0;
        z-index: 1001;
    }
    @media (max-width: 991.98px) {
        .mobile-nav { display: flex; align-items: center; justify-content: space-between; }
    }
</style>

<div class="mobile-nav">
    <h6 class="m-0 fw-bold"><?php echo htmlspecialchars($studioName); ?></h6>
    <button class="btn btn-dark btn-sm" id="sidebarToggle"><i class="fas fa-bars"></i></button>
</div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="sidebar" id="sidebar">
    <div class="p-4 border-bottom mb-4 text-center d-none d-lg-block">
        <h6 class="fw-bold mb-0 text-uppercase letter-spacing-1"><?php echo htmlspecialchars($studioName); ?></h6>
        <small class="text-secondary">Admin Access</small>
    </div>
    <nav class="nav flex-column">
        <a class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>" href="index.php"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a class="nav-link <?php echo $current_page == 'upload.php' ? 'active' : ''; ?>" href="upload.php"><i class="fas fa-plus-circle"></i> Upload Art</a>
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
