<?php
require_once '../config/db_config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Fetch commissions
$commissions = $pdo->query("SELECT * FROM commissions ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commissions | Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css"> <!-- Assuming common admin styles -->
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.05);
        }
        .table { vertical-align: middle; }
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-contacted { background: #d1ecf1; color: #0c5460; }
        .badge-completed { background: #d4edda; color: #155724; }
    </style>
</head>
<body class="bg-light">

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold mb-1">Commission Requests</h2>
                <p class="text-secondary">Manage custom art inquiries from potential clients.</p>
            </div>
        </div>

        <div class="glass-card p-4">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Subject</th>
                            <th>Description</th>
                            <th>Budget</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commissions as $comm): ?>
                        <tr>
                            <td class="small text-secondary"><?php echo date('M d, Y', strtotime($comm['created_at'])); ?></td>
                            <td>
                                <div class="fw-bold"><?php echo htmlspecialchars($comm['customer_name']); ?></div>
                                <div class="small text-secondary"><?php echo htmlspecialchars($comm['contact_number']); ?></div>
                                <div class="small text-secondary"><?php echo htmlspecialchars($comm['email']); ?></div>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($comm['subject']); ?></span></td>
                            <td class="small" style="max-width: 250px;"><?php echo htmlspecialchars($comm['description']); ?></td>
                            <td class="fw-bold text-success">₱<?php echo number_format($comm['budget'], 2); ?></td>
                            <td>
                                <span class="badge badge-pending rounded-pill px-3">Pending</span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="https://tel:<?php echo $comm['contact_number']; ?>" class="btn btn-sm btn-dark" title="Call"><i class="fas fa-phone"></i></a>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteCommission(<?php echo $comm['id']; ?>, this)" title="Delete"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($commissions)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-secondary">No commission requests yet.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        async function deleteCommission(id, btn) {
            const { isConfirmed } = await Swal.fire({
                title: 'Delete Request?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33'
            });

            if (isConfirmed) {
                try {
                    const response = await fetch('../api/delete_commission.php', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-Token': csrfToken 
                        },
                        body: `id=${id}`
                    });
                    const result = await response.json();
                    if (result.status === 'success') {
                        btn.closest('tr').classList.add('animate__animated', 'animate__fadeOut');
                        setTimeout(() => btn.closest('tr').remove(), 500);
                        Swal.fire('Deleted!', '', 'success');
                    }
                } catch (e) {
                    Swal.fire('Error', 'Failed to delete.', 'error');
                }
            }
        }
    </script>
</body>
</html>
