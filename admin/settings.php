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
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token']; ?>">
    <style>
        body { background: #f4f6f9; font-family: 'Inter', sans-serif; }
        .main-content { padding: 40px; margin-left: 260px; }
        @media (max-width: 768px) { .main-content { margin-left: 0; padding: 20px; } }
        .card { border-radius: 15px; border: none; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <h3 class="fw-bold mb-5">Studio Settings</h3>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card p-4 p-md-5 mb-4 shadow-sm">
                    <h5 class="fw-bold mb-4">Studio Information</h5>
                    <form id="settingsForm">
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Studio Name</label>
                            <input type="text" name="studio_name" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['studio_name'] ?? ''); ?>">
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Contact Number</label>
                                <input type="text" name="contact_number" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['contact_number'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Email Address</label>
                                <input type="email" name="email_address" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['email_address'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Facebook Profile Link</label>
                            <input type="text" name="facebook_link" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['facebook_link'] ?? ''); ?>">
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Messenger ID</label>
                            <input type="text" name="messenger_id" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['messenger_id'] ?? ''); ?>">
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">GCash QR Code</label>
                            <div class="bg-light p-3 rounded mb-2 border text-center">
                                <img src="../<?php echo $settings['gcash_qr'] ?? ''; ?>" style="max-width: 100px;" class="mb-2 shadow-sm rounded">
                                <input type="file" name="gcash_qr_file" class="form-control form-control-sm mt-3">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-dark px-5 py-3 w-100 fw-bold shadow-sm">Update Studio Info</button>
                    </form>
                </div>

                <div class="card p-4 p-md-5 mb-4 shadow-sm">
                    <h5 class="fw-bold mb-4"><i class="fas fa-robot me-2 text-primary"></i> AI Configuration</h5>
                    <form id="aiConfigForm">
                        <div class="mb-4">
                            <label class="form-label small fw-bold">OpenAI API Key</label>
                            <input type="password" name="openai_api_key" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['openai_api_key'] ?? ''); ?>" placeholder="sk-...">
                        </div>
                        <button type="submit" class="btn btn-outline-dark w-100 py-3 fw-bold">Save AI Config</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card p-4 p-md-5 shadow-sm">
                    <h5 class="fw-bold mb-4"><i class="fas fa-shield-alt me-2 text-success"></i> Security</h5>
                    <form id="passwordForm">
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Username</label>
                            <input type="text" class="form-control bg-light border-0" value="<?php echo $_SESSION['admin_user']; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">New Password</label>
                            <input type="password" name="new_password" class="form-control bg-light border-0">
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control bg-light border-0">
                        </div>
                        <button type="submit" class="btn btn-outline-dark w-100 py-3 fw-bold">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        document.getElementById('settingsForm').onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('action', 'update_studio');
            formData.append('csrf_token', csrfToken);
            
            const resp = await fetch('../api/update_settings.php', { method: 'POST', body: formData });
            const res = await resp.json();
            if (res.status === 'success') Swal.fire('Saved!', 'Settings updated.', 'success').then(() => location.reload());
            else Swal.fire('Error', res.message, 'error');
        };

        document.getElementById('aiConfigForm').onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('action', 'update_ai');
            formData.append('csrf_token', csrfToken);
            
            const resp = await fetch('../api/update_settings.php', { method: 'POST', body: formData });
            const res = await resp.json();
            if (res.status === 'success') Swal.fire('Saved!', 'AI configuration updated.', 'success');
            else Swal.fire('Error', res.message, 'error');
        };

        document.getElementById('passwordForm').onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('action', 'update_password');
            formData.append('csrf_token', csrfToken);
            if (formData.get('new_password') !== formData.get('confirm_password')) return Swal.fire('Error', 'Passwords mismatch.', 'error');
            
            const resp = await fetch('../api/update_settings.php', { method: 'POST', body: formData });
            const res = await resp.json();
            if (res.status === 'success') Swal.fire('Updated!', 'Password changed.', 'success');
            else Swal.fire('Error', res.message, 'error');
        };
    </script>
</body>
</html>
