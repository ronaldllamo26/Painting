<?php
session_start();
require_once '../config/db_config.php';
if (!isset($_SESSION['admin_id'])) { header("Location: index.php"); exit(); }

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
    <title>Edit Artwork | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fas fa-arrow-left"></i> EDIT PAINTING</a>
        </div>
    </nav>

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="glass-card p-5">
                    <form id="editForm">
                        <input type="hidden" name="id" value="<?php echo $art['id']; ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-secondary">Title</label>
                                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($art['title']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-secondary">Price (PHP)</label>
                                <div class="input-group">
                                    <input type="number" name="price" class="form-control" value="<?php echo $art['price']; ?>" required>
                                    <div class="input-group-text bg-white">
                                        <input class="form-check-input mt-0 me-2" type="checkbox" name="is_negotiable" id="isNegotiable" <?php echo $art['is_negotiable'] ? 'checked' : ''; ?>>
                                        <label class="small text-secondary mb-0" for="isNegotiable">Negotiable</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-secondary">Size</label>
                                <input type="text" name="size" class="form-control" value="<?php echo htmlspecialchars($art['size']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-secondary">Medium</label>
                                <input type="text" name="medium" class="form-control" value="<?php echo htmlspecialchars($art['medium']); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-secondary">Status</label>
                            <select name="status" class="form-select">
                                <option value="Available" <?php echo $art['status'] === 'Available' ? 'selected' : ''; ?>>Available</option>
                                <option value="Pending" <?php echo $art['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="Sold" <?php echo $art['status'] === 'Sold' ? 'selected' : ''; ?>>Sold</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-secondary">Description</label>
                            <textarea name="ai_description" class="form-control" rows="4"><?php echo htmlspecialchars($art['ai_description']); ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-secondary">Tags (Comma-separated)</label>
                            <input type="text" name="ai_tags" id="aiTags" class="form-control" value="<?php echo htmlspecialchars($art['ai_tags']); ?>">
                            <div id="quickTags" class="mt-2 d-flex flex-wrap gap-2">
                                <span class="badge bg-light text-dark border p-2 cursor-pointer tag-sug" onclick="addTag('Abstract')">Abstract</span>
                                <span class="badge bg-light text-dark border p-2 cursor-pointer tag-sug" onclick="addTag('Contemporary')">Contemporary</span>
                                <span class="badge bg-light text-dark border p-2 cursor-pointer tag-sug" onclick="addTag('Vibrant')">Vibrant</span>
                                <span class="badge bg-light text-dark border p-2 cursor-pointer tag-sug" onclick="addTag('Minimalist')">Minimalist</span>
                                <span class="badge bg-light text-dark border p-2 cursor-pointer tag-sug" onclick="addTag('Landscape')">Landscape</span>
                                <span class="badge bg-light text-dark border p-2 cursor-pointer tag-sug" onclick="addTag('Portrait')">Portrait</span>
                                <span class="badge bg-light text-dark border p-2 cursor-pointer tag-sug" onclick="addTag('Oil on Canvas')">Oil on Canvas</span>
                                <span class="badge bg-light text-dark border p-2 cursor-pointer tag-sug" onclick="addTag('Acrylic')">Acrylic</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-gallery w-100">Update Artwork</button>
                    </form>
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
