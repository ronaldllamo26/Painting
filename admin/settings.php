<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) { header('Location: login.php'); exit; }
require_once '../config/db_config.php';

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
    <title>Settings | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f4f6f9; font-family: 'Inter', sans-serif; }
        .card { border: none; border-radius: 12px; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1); }
        .form-label { font-weight: 600; font-size: 0.75rem; color: #4e73df; text-transform: uppercase; letter-spacing: 0.5px; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <h3 class="fw-bold mb-5">Studio Settings</h3>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card p-4 p-md-5 mb-4">
                    <h5 class="fw-bold mb-4">Studio Information</h5>
                    <form id="settingsForm">
                        <div class="mb-4">
                            <label class="form-label">Studio Name</label>
                            <input type="text" name="studio_name" class="form-control form-control-lg shadow-sm" value="<?php echo htmlspecialchars($settings['studio_name']); ?>">
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Number</label>
                                <input type="text" name="contact_number" class="form-control shadow-sm" value="<?php echo htmlspecialchars($settings['contact_number']); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email_address" class="form-control shadow-sm" value="<?php echo htmlspecialchars($settings['email_address']); ?>">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Facebook Profile Link</label>
                            <input type="text" name="facebook_link" class="form-control shadow-sm" value="<?php echo htmlspecialchars($settings['facebook_link']); ?>">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Messenger ID</label>
                            <input type="text" name="messenger_id" class="form-control shadow-sm" value="<?php echo htmlspecialchars($settings['messenger_id']); ?>">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">GCash QR Code</label>
                            <div class="bg-light p-3 rounded mb-2 border text-center">
                                <img src="../<?php echo $settings['gcash_qr']; ?>" style="max-width: 100px;" class="mb-2">
                                <input type="file" name="gcash_qr_file" class="form-control form-control-sm">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-dark px-5 py-2 shadow w-100 w-md-auto">Update Studio Info</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card p-4 p-md-5">
                    <h5 class="fw-bold mb-4">Security</h5>
                    <form id="passwordForm">
                        <div class="mb-4">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control bg-light" value="<?php echo $_SESSION['admin_user']; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control shadow-sm">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control shadow-sm">
                        </div>
                        <button type="submit" class="btn btn-outline-dark w-100 py-2">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('settingsForm').onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('action', 'update_studio');
            const resp = await fetch('../api/update_settings.php', { method: 'POST', body: formData });
            const res = await resp.json();
            if (res.status === 'success') Swal.fire('Saved!', 'Settings updated.', 'success').then(() => location.reload());
        };
        document.getElementById('passwordForm').onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('action', 'update_password');
            if (formData.get('new_password') !== formData.get('confirm_password')) return Swal.fire('Error', 'Passwords mismatch.', 'error');
            const resp = await fetch('../api/update_settings.php', { method: 'POST', body: formData });
            const res = await resp.json();
            if (res.status === 'success') Swal.fire('Updated!', 'Password changed.', 'success');
        };
    </script>
</body>
</html>
