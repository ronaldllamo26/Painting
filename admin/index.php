<?php
session_start();
require_once '../config/db_config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Stats
$selectedYear = isset($_GET['year']) ? $_GET['year'] : date('Y');
$artworksCount = $pdo->query("SELECT COUNT(*) FROM artworks")->fetchColumn();
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'Pending'")->fetchColumn();

// Revenue for selected year
$stmtRev = $pdo->prepare("SELECT SUM(a.price) FROM orders o JOIN artworks a ON o.artwork_id = a.id WHERE o.order_status = 'Approved' AND YEAR(o.order_date) = ?");
$stmtRev->execute([$selectedYear]);
$yearlyRevenue = $stmtRev->fetchColumn() ?: 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        body { background: #f4f6f9; font-family: 'Inter', sans-serif; }
        .card-stat {
            background: #fff;
            border: none;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            height: 100%;
        }
        .stat-icon {
            width: 45px; height: 45px;
            background: #f8f9fc;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; color: #000;
        }
        .glass-card { background: #fff; border-radius: 12px; border: none; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1); }
        .table thead th { background: #f8f9fc; border-bottom: none; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; padding: 15px; }
        .table td { padding: 15px; vertical-align: middle; }
        .x-small { font-size: 0.7rem; }
        .img-thumbnail-zoom { transition: transform 0.2s; cursor: pointer; }
        .img-thumbnail-zoom:hover { transform: scale(2.5); z-index: 100; position: relative; box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
        .custom-scroll::-webkit-scrollbar { width: 5px; }
        .custom-scroll::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #888; }
    </style>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token']; ?>">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
            <h3 class="fw-bold m-0">Overview</h3>
            <a href="upload.php" class="btn btn-dark px-4 py-2" style="border-radius: 8px;"><i class="fas fa-plus me-2"></i> New Artwork</a>
        </div>

        <!-- Stats -->
        <div class="row g-4 mb-5 animate__animated animate__fadeInUp">
            <div class="col-6 col-md-4">
                <div class="card-stat d-flex flex-column justify-content-between p-4 border-0 shadow-sm" style="background: #fff; border-radius: 20px;">
                    <div class="stat-icon mb-3" style="background: rgba(0,0,0,0.05); color: #000; border-radius: 12px; width: 50px; height: 50px;"><i class="fas fa-palette"></i></div>
                    <div>
                        <h6 class="text-secondary small fw-bold text-uppercase mb-1" style="font-size: 0.65rem; letter-spacing: 1px;">Collection Size</h6>
                        <h2 class="fw-800 m-0" style="letter-spacing: -1px;"><?php echo $artworksCount; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="card-stat d-flex flex-column justify-content-between p-4 border-0 shadow-sm" style="background: #fff; border-radius: 20px;">
                    <div class="stat-icon mb-3" style="background: rgba(255, 193, 7, 0.1); color: #ffc107; border-radius: 12px; width: 50px; height: 50px;"><i class="fas fa-clock"></i></div>
                    <div>
                        <h6 class="text-secondary small fw-bold text-uppercase mb-1" style="font-size: 0.65rem; letter-spacing: 1px;">Pending Inquiries</h6>
                        <h2 class="fw-800 m-0 text-warning" style="letter-spacing: -1px;"><?php echo $pendingOrders; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card-stat d-flex flex-column justify-content-between p-4 border-0 shadow-sm" style="background: #000; color: #fff; border-radius: 20px;">
                    <div class="stat-icon mb-3" style="background: rgba(255,255,255,0.1); color: #fff; border-radius: 12px; width: 50px; height: 50px;"><i class="fas fa-wallet"></i></div>
                    <div>
                        <h6 class="text-white-50 small fw-bold text-uppercase mb-1" style="font-size: 0.65rem; letter-spacing: 1px;">Revenue <?php echo $selectedYear; ?></h6>
                        <h2 class="fw-800 m-0" style="letter-spacing: -1px;">₱<?php echo number_format($yearlyRevenue, 0); ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics Row -->
        <div class="row g-4 mb-5">
            <div class="col-lg-8">
                <div class="glass-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Revenue Analytics</h5>
                        <div class="dropdown">
                            <?php
                            $selectedYear = isset($_GET['year']) ? $_GET['year'] : date('Y');
                            // Get available years from DB
                            $years = $pdo->query("SELECT DISTINCT YEAR(order_date) as year FROM orders WHERE order_status = 'Approved' ORDER BY year DESC")->fetchAll();
                            ?>
                            <button class="btn btn-sm btn-light border dropdown-toggle px-3" type="button" data-bs-toggle="dropdown">
                                <?php echo $selectedYear; ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <?php if(empty($years)): ?>
                                    <li><a class="dropdown-item small" href="#">No data yet</a></li>
                                <?php else: ?>
                                    <?php foreach($years as $y): ?>
                                        <li><a class="dropdown-item small <?php echo $selectedYear == $y['year'] ? 'active' : ''; ?>" href="?year=<?php echo $y['year']; ?>"><?php echo $y['year']; ?></a></li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                    <div style="height: 320px; position: relative;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="glass-card p-4 h-100">
                    <h5 class="fw-bold mb-4">Inventory Summary</h5>
                    <div class="custom-scroll" style="max-height: 280px; overflow-y: auto; overflow-x: hidden;">
                        <?php
                        $recentArts = $pdo->query("SELECT * FROM artworks ORDER BY created_at DESC")->fetchAll();
                        foreach ($recentArts as $art) {
                            $statusBadge = $art['status'] === 'Available' ? 'bg-info' : ($art['status'] === 'Sold' ? 'bg-secondary' : 'bg-warning');
                            ?>
                            <div class="d-flex align-items-center mb-3 pb-3 border-bottom pe-3">
                                <img src="../<?php echo $art['image_url']; ?>" class="rounded-3 me-3 img-thumbnail-zoom" style="width: 50px; height: 50px; object-fit: cover;">
                                <div class="flex-grow-1 overflow-hidden" style="min-width: 0;">
                                    <h6 class="mb-0 small fw-bold text-truncate"><?php echo $art['title']; ?></h6>
                                    <span class="badge <?php echo $statusBadge; ?> x-small opacity-75 mt-1"><?php echo $art['status']; ?></span>
                                </div>
                                <div class="ms-2 d-flex gap-1 flex-shrink-0">
                                    <?php if($art['status'] === 'Pending'): ?>
                                        <button class="btn btn-sm btn-outline-warning p-1" onclick="resetArtStatus(<?php echo $art['id']; ?>, this)" title="Reset to Available">
                                            <i class="fas fa-undo x-small"></i>
                                        </button>
                                    <?php endif; ?>
                                    <a href="edit_artwork.php?id=<?php echo $art['id']; ?>" class="btn btn-sm btn-light border p-1" title="Edit"><i class="fas fa-edit x-small"></i></a>
                                    <button class="btn btn-sm btn-light border p-1 text-danger" onclick="deleteArtwork(<?php echo $art['id']; ?>, this)" title="Delete"><i class="fas fa-trash-alt x-small"></i></button>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="mt-4">
                        <a href="upload.php" class="btn btn-dark w-100 py-2"><i class="fas fa-plus me-2"></i> Add New Piece</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Recent Orders -->
            <div class="col-12">
                <div class="glass-card p-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                        <h5 class="mb-0 fw-bold">Recent Inquiries & Orders</h5>
                        <div class="d-flex align-items-center gap-2">
                            <button onclick="exportToCSV()" class="btn btn-sm btn-outline-dark me-2"><i class="fas fa-file-export me-1"></i> Export to CSV</button>
                            <div class="input-group input-group-sm" style="max-width: 300px;">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-secondary"></i></span>
                                <input type="text" id="orderSearch" class="form-control bg-light border-start-0" placeholder="Search customer or art...">
                            </div>
                            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill"><?php echo $pendingOrders; ?> Action Required</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle" id="ordersTable">
                            <thead>
                                <tr>
                                    <th>Artwork</th>
                                    <th>Customer</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $orders = $pdo->query("SELECT o.*, a.title as art_title, a.image_url as art_img FROM orders o JOIN artworks a ON o.artwork_id = a.id ORDER BY o.order_date DESC LIMIT 10")->fetchAll();
                                foreach ($orders as $order) {
                                    $statusClass = ['Pending' => 'bg-warning text-dark', 'Approved' => 'bg-success text-white', 'Cancelled' => 'bg-danger text-white'][$order['order_status']];
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="../<?php echo $order['art_img']; ?>" class="rounded me-3 img-thumbnail-zoom" style="width: 45px; height: 45px; object-fit: cover;">
                                                <div>
                                                    <div class="small fw-bold"><?php echo $order['art_title']; ?></div>
                                                    <div class="x-small text-secondary"><?php echo date('M d, Y', strtotime($order['order_date'])); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small fw-bold"><?php echo $order['customer_name']; ?></div>
                                            <div class="x-small text-secondary"><?php echo $order['contact_number']; ?></div>
                                        </td>
                                        <td>
                                            <div class="small"><?php echo $order['payment_method']; ?></div>
                                            <?php if($order['proposed_price']): ?>
                                                <div class="text-success fw-bold x-small">Offer: ₱<?php echo number_format($order['proposed_price'], 2); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td><span class="badge <?php echo $statusClass; ?> x-small p-2 rounded-pill"><?php echo $order['order_status']; ?></span></td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-light border rounded me-1" onclick="copyOrderInfo(this)" 
                                                    data-name="<?php echo htmlspecialchars($order['customer_name']); ?>"
                                                    data-contact="<?php echo htmlspecialchars($order['contact_number']); ?>"
                                                    data-address="<?php echo htmlspecialchars($order['address']); ?>"
                                                    data-art="<?php echo htmlspecialchars($order['art_title']); ?>"
                                                    title="Copy Delivery Info">
                                                    <i class="fas fa-copy text-primary"></i>
                                                </button>
                                                <?php if($order['order_status'] === 'Pending'): ?>
                                                    <button class="btn btn-sm btn-dark me-1 rounded" title="Approve" onclick="updateOrder(<?php echo $order['id']; ?>, 'Approved', this)"><i class="fas fa-check"></i></button>
                                                    <button class="btn btn-sm btn-outline-danger me-1 rounded" title="Cancel" onclick="updateOrder(<?php echo $order['id']; ?>, 'Cancelled', this)"><i class="fas fa-times"></i></button>
                                                <?php endif; ?>
                                                <?php if($order['receipt_url']): ?>
                                                    <a href="../<?php echo $order['receipt_url']; ?>" target="_blank" class="btn btn-sm btn-light border rounded" title="View Receipt"><i class="fas fa-receipt"></i></a>
                                                <?php endif; ?>
                                                <button class="btn btn-sm btn-light border rounded ms-1" onclick="deleteOrder(<?php echo $order['id']; ?>, this)" title="Delete Record"><i class="fas fa-trash-alt small text-danger"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Initialize Revenue Chart
        const ctx = document.getElementById('revenueChart').getContext('2d');
        <?php
        // Fetch monthly revenue for selected year
        $stmt = $pdo->prepare("
            SELECT 
                DATE_FORMAT(o.order_date, '%b') as month,
                SUM(a.price) as total 
            FROM orders o 
            JOIN artworks a ON o.artwork_id = a.id 
            WHERE o.order_status = 'Approved' 
            AND YEAR(o.order_date) = ?
            GROUP BY MONTH(o.order_date)
            ORDER BY MONTH(o.order_date)
        ");
        $stmt->execute([$selectedYear]);
        $monthlyRevenue = $stmt->fetchAll();
        
        $labels = []; $data = [];
        foreach($monthlyRevenue as $row) {
            $labels[] = $row['month'];
            $data[] = $row['total'];
        }
        ?>

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($labels ?: ['No Data']); ?>,
                datasets: [{
                    label: 'Revenue (₱)',
                    data: <?php echo json_encode($data ?: [0]); ?>,
                    borderColor: '#000',
                    backgroundColor: 'rgba(0,0,0,0.05)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#000',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { display: false } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Order Table Search
        document.getElementById('orderSearch').addEventListener('keyup', function() {
            const term = this.value.toLowerCase();
            const rows = document.querySelectorAll('#ordersTable tbody tr');
            
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            });
        });

        function copyOrderInfo(btn) {
            const name = btn.getAttribute('data-name');
            const contact = btn.getAttribute('data-contact');
            const address = btn.getAttribute('data-address');
            const art = btn.getAttribute('data-art');
            
            const text = `🎨 ARTWORK: ${art}\n👤 CUSTOMER: ${name}\n📞 CONTACT: ${contact}\n📍 ADDRESS: ${address}`;
            
            navigator.clipboard.writeText(text).then(() => {
                const icon = btn.querySelector('i');
                icon.classList.replace('fa-copy', 'fa-check');
                icon.classList.replace('text-primary', 'text-success');
                setTimeout(() => {
                    icon.classList.replace('fa-check', 'fa-copy');
                    icon.classList.replace('text-success', 'text-primary');
                }, 2000);
            });
        }

        async function updateOrder(orderId, status, btn) {
            const confirmText = status === 'Approved' ? 'approve this order and mark art as SOLD' : 'cancel this order and mark art as AVAILABLE';
            const { isConfirmed } = await Swal.fire({ 
                title: 'Process Order?', 
                text: `Are you sure you want to ${confirmText}?`, 
                icon: 'question', 
                showCancelButton: true, 
                confirmButtonColor: status === 'Approved' ? '#000' : '#d33' 
            });
            if (isConfirmed) {
                try {
                    const formData = new FormData();
                    formData.append('order_id', orderId);
                    formData.append('status', status);
                    formData.append('csrf_token', csrfToken);

                    const response = await fetch('../api/update_order.php', { 
                        method: 'POST', 
                        body: formData 
                    });
                    const result = await response.json();
                    if (result.status === 'success') {
                        Swal.fire({ title: 'Success', text: `Order ${status.toLowerCase()}!`, icon: 'success', timer: 1000, showConfirmButton: false });
                        location.reload();
                    } else {
                        Swal.fire('Error', result.message, 'error');
                    }
                } catch (e) {
                    console.error(e);
                    location.reload();
                }
            }
        }

        async function deleteOrder(id, btn) {
            const { isConfirmed } = await Swal.fire({ title: 'Delete?', text: "Remove this order from history?", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33' });
            if (isConfirmed) {
                try {
                    const formData = new FormData();
                    formData.append('id', id);
                    formData.append('csrf_token', csrfToken);

                    const response = await fetch('../api/delete_order.php', { 
                        method: 'POST', 
                        body: formData 
                    });
                    const result = await response.json();
                    if (result.status === 'success') {
                        location.reload();
                    }
                } catch (e) { location.reload(); }
            }
        }

        async function deleteArtwork(id, btn) {
            const { isConfirmed } = await Swal.fire({ 
                title: 'Delete Artwork?', 
                text: "This will permanently remove the painting from your gallery and server. Proceed?", 
                icon: 'warning', 
                showCancelButton: true, 
                confirmButtonColor: '#000',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Delete'
            });
            
            if (isConfirmed) {
                try {
                    const formData = new FormData();
                    formData.append('id', id);
                    formData.append('csrf_token', csrfToken);

                    const response = await fetch('../api/delete_artwork.php', { 
                        method: 'POST', 
                        body: formData 
                    });
                    const result = await response.json();
                    if (result.status === 'success') {
                        location.reload();
                    } else {
                        Swal.fire('Error', result.message || 'Failed to delete.', 'error');
                    }
                } catch (e) { 
                    console.error("Delete Error:", e);
                    location.reload(); 
                }
            }
        }

        async function resetArtStatus(id, btn) {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('csrf_token', csrfToken);
            
            try {
                const resp = await fetch('../api/reset_art.php', { method: 'POST', body: formData });
                const res = await resp.json();
                if (res.status === 'success') {
                    Swal.fire({ title: 'Success', text: 'Status reset to Available!', icon: 'success', timer: 1000, showConfirmButton: false });
                    location.reload();
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            } catch (e) { location.reload(); }
        }

        function exportToCSV() {
            const table = document.getElementById('ordersTable');
            let csv = [];
            const rows = table.querySelectorAll('tr');
            
            for (let i = 0; i < rows.length; i++) {
                let row = [], cols = rows[i].querySelectorAll('td, th');
                for (let j = 0; j < cols.length - 1; j++) { // Skip action column
                    let text = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, " ").replace(/,/g, ";");
                    row.push(text);
                }
                csv.push(row.join(","));
            }

            const csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
            const downloadLink = document.createElement("a");
            downloadLink.download = `orders_export_${new Date().toLocaleDateString()}.csv`;
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = "none";
            document.body.appendChild(downloadLink);
            downloadLink.click();
        }
    </script>
</body>
</html>
