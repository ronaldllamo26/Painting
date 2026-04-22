<?php
session_start();
require_once '../config/db_config.php';
if (!isset($_SESSION['admin_logged_in'])) { header("Location: index.php"); exit(); }

if (!isset($_GET['id'])) { header("Location: index.php"); exit(); }

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM artworks WHERE id = ?");
$stmt->execute([$id]);
$art = $stmt->fetch();

if (!$art) { header("Location: index.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Painting | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f4f6f9; font-family: 'Inter', sans-serif; }
        .glass-card { background: #fff; border-radius: 12px; border: none; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1); }
        .form-label { font-weight: 600; font-size: 0.75rem; color: #4e73df; text-transform: uppercase; letter-spacing: 0.5px; }
        .tag-sug { cursor: pointer; transition: all 0.2s; font-size: 0.8rem; }
        .tag-sug:hover { background: #000 !important; color: #fff !important; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid py-4">
            <div class="d-flex align-items-center mb-5">
                <a href="index.php" class="btn btn-sm btn-light border me-3 shadow-sm"><i class="fas fa-arrow-left"></i></a>
                <h3 class="fw-bold m-0">Edit Painting</h3>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="glass-card p-4 p-md-5">
                        <form id="editForm">
                            <input type="hidden" name="id" value="<?php echo $art['id']; ?>">
                            
                            <div class="row g-4 mb-4">
                                <div class="col-md-7">
                                    <label class="form-label">Painting Title</label>
                                    <input type="text" name="title" class="form-control form-control-lg border-light bg-light" value="<?php echo htmlspecialchars($art['title']); ?>" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Availability Status</label>
                                    <select name="status" class="form-select form-select-lg border-light bg-light">
                                        <option value="Available" <?php echo $art['status'] === 'Available' ? 'selected' : ''; ?>>Available</option>
                                        <option value="Pending" <?php echo $art['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Sold" <?php echo $art['status'] === 'Sold' ? 'selected' : ''; ?>>Sold</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Price (PHP)</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text border-light bg-light">₱</span>
                                        <input type="number" name="price" class="form-control border-light bg-light" value="<?php echo $art['price']; ?>" required style="max-width: 150px;">
                                        <div class="input-group-text border-light bg-white flex-grow-1">
                                            <div class="form-check m-0">
                                                <input class="form-check-input" type="checkbox" name="is_negotiable" id="isNegotiable" <?php echo $art['is_negotiable'] ? 'checked' : ''; ?>>
                                                <label class="form-check-label x-small fw-bold text-dark" for="isNegotiable">Negotiable</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="form-label">Dimensions</label>
                                    <input type="text" name="size" class="form-control border-light bg-light" value="<?php echo htmlspecialchars($art['size']); ?>" required>
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="form-label">Medium</label>
                                    <input type="text" name="medium" class="form-control border-light bg-light" value="<?php echo htmlspecialchars($art['medium']); ?>" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Description</label>
                                <textarea name="ai_description" class="form-control border-light bg-light" rows="4"><?php echo htmlspecialchars($art['ai_description']); ?></textarea>
                            </div>

                            <div class="mb-5">
                                <label class="form-label">Tags & Keywords</label>
                                <input type="text" name="ai_tags" id="aiTags" class="form-control border-light bg-light mb-2" value="<?php echo htmlspecialchars($art['ai_tags']); ?>">
                                <div id="quickTags" class="d-flex flex-wrap gap-2">
                                    <?php foreach(['Abstract','Modern','Vibrant','Portrait','Landscape','Minimalist'] as $t): ?>
                                        <span class="badge bg-white text-dark border p-2 tag-sug" onclick="addTag('<?php echo $t; ?>')"><?php echo $t; ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-dark btn-lg py-3 shadow-sm"><i class="fas fa-save me-2"></i> Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function addTag(tag) {
            const input = document.getElementById('aiTags');
            let currentTags = input.value.split(',').map(t => t.trim()).filter(t => t !== "");
            if (!currentTags.includes(tag)) {
                currentTags.push(tag);
                input.value = currentTags.join(', ');
            }
        }

        document.getElementById('editForm').onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            
            try {
                const response = await fetch('../api/update_artwork.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.status === 'success') {
                    Swal.fire('Updated!', 'Artwork details have been saved.', 'success')
                        .then(() => window.location.href = 'index.php');
                } else {
                    Swal.fire('Error', result.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error', 'Failed to update artwork.', 'error');
            }
        };
    </script>
</body>
</html>
