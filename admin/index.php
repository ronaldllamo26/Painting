<?php
session_start();
require_once '../config/db_config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Stats
$artworksCount = $pdo->query("SELECT COUNT(*) FROM artworks")->fetchColumn();
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'Pending'")->fetchColumn();
$totalRevenue = $pdo->query("SELECT SUM(a.price) FROM orders o JOIN artworks a ON o.artwork_id = a.id WHERE o.order_status = 'Approved'")->fetchColumn() ?: 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
            <h3 class="fw-bold m-0">Overview</h3>
            <a href="upload.php" class="btn btn-dark px-4 py-2" style="border-radius: 8px;"><i class="fas fa-plus me-2"></i> New Artwork</a>
        </div>

        <!-- Stats -->
        <div class="row g-4 mb-5">
            <div class="col-6 col-md-4">
                <div class="card-stat d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-secondary small fw-bold text-uppercase mb-1 d-none d-md-block">Total Artworks</h6>
                        <h3 class="fw-bold m-0"><?php echo $artworksCount; ?></h3>
                    </div>
                    <div class="stat-icon"><i class="fas fa-palette"></i></div>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="card-stat d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-secondary small fw-bold text-uppercase mb-1 d-none d-md-block">Pending Orders</h6>
                        <h3 class="fw-bold m-0 text-warning"><?php echo $pendingOrders; ?></h3>
                    </div>
                    <div class="stat-icon"><i class="fas fa-shopping-bag"></i></div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card-stat d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-secondary small fw-bold text-uppercase mb-1 d-none d-md-block">Total Revenue</h6>
                        <h3 class="fw-bold m-0 text-success">₱<?php echo number_format($totalRevenue, 2); ?></h3>
                    </div>
                    <div class="stat-icon"><i class="fas fa-wallet"></i></div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Recent Orders -->
            <div class="col-lg-8">
                <div class="glass-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                        <h5 class="mb-0 fw-bold">Recent Orders</h5>
                        <span class="badge bg-light text-dark border"><?php echo $pendingOrders; ?> New</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Artwork</th>
                                    <th class="d-none d-sm-table-cell">Customer</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $orders = $pdo->query("SELECT o.*, a.title as art_title, a.image_url as art_img FROM orders o JOIN artworks a ON o.artwork_id = a.id ORDER BY o.order_date DESC LIMIT 5")->fetchAll();
                                foreach ($orders as $order) {
                                    $statusClass = ['Pending' => 'bg-warning text-dark', 'Approved' => 'bg-success text-white', 'Cancelled' => 'bg-danger text-white'][$order['order_status']];
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="../<?php echo $order['art_img']; ?>" class="rounded me-2 me-md-3" style="width: 40px; height: 40px; object-fit: cover;">
                                                <div class="small fw-bold"><?php echo $order['art_title']; ?></div>
                                            </div>
                                        </td>
                                        <td class="small d-none d-sm-table-cell">
                                            <div class="fw-bold"><?php echo $order['customer_name']; ?></div>
                                            <?php if($order['proposed_price']): ?>
                                                <div class="text-accent fw-bold" style="font-size: 0.7rem;">Offer: ₱<?php echo number_format($order['proposed_price'], 2); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td><span class="badge <?php echo $statusClass; ?> x-small p-2"><?php echo $order['order_status']; ?></span></td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <?php if($order['order_status'] === 'Pending'): ?>
                                                    <button class="btn btn-sm btn-dark" onclick="updateOrder(<?php echo $order['id']; ?>, 'Approved')"><i class="fas fa-check"></i></button>
                                                <?php endif; ?>
                                                <?php if($order['receipt_url']): ?>
                                                    <a href="../<?php echo $order['receipt_url']; ?>" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="fas fa-receipt"></i></a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Inventory Summary -->
            <div class="col-lg-4">
                <div class="glass-card p-4 h-100">
                    <h5 class="mb-4 fw-bold px-2">Inventory</h5>
                    <div class="px-2" style="max-height: 400px; overflow-y: auto;">
                        <?php
                        $recentArts = $pdo->query("SELECT * FROM artworks ORDER BY created_at DESC")->fetchAll();
                        foreach ($recentArts as $art) {
                            $statusBadge = $art['status'] === 'Available' ? 'bg-info' : ($art['status'] === 'Sold' ? 'bg-secondary' : 'bg-warning');
                            ?>
                            <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                <img src="../<?php echo $art['image_url']; ?>" class="rounded me-3" style="width: 45px; height: 45px; object-fit: cover;">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 small fw-bold text-truncate" style="max-width: 100px;"><?php echo $art['title']; ?></h6>
                                    <span class="badge <?php echo $statusBadge; ?> x-small opacity-75 mt-1"><?php echo $art['status']; ?></span>
                                </div>
                                <div class="d-flex" style="position: relative; z-index: 10;">
                                    <a href="edit_artwork.php?id=<?php echo $art['id']; ?>" class="btn btn-sm btn-outline-secondary p-1 me-1" style="position: relative; z-index: 11;"><i class="fas fa-edit"></i></a>
                                    <button class="btn btn-sm btn-outline-danger p-1" onclick="deleteArtwork(<?php echo $art['id']; ?>)" style="position: relative; z-index: 11;"><i class="fas fa-trash-alt"></i></button>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        async function updateOrder(orderId, status) {
            const { isConfirmed } = await Swal.fire({ title: 'Process Order?', text: `Update to ${status}?`, icon: 'question', showCancelButton: true, confirmButtonColor: '#000' });
            if (isConfirmed) {
                const response = await fetch('../api/update_order.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ order_id: orderId, status: status }) });
                const result = await response.json();
                if (result.status === 'success') location.reload();
            }
        }
        async function deleteArtwork(id) {
            const { isConfirmed } = await Swal.fire({ title: 'Delete?', text: "This cannot be undone.", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33' });
            if (isConfirmed) {
                const response = await fetch('../api/delete_artwork.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id: id }) });
                const result = await response.json();
                if (result.status === 'success') location.reload();
            }
        }
    </script>
</body>
</html>
