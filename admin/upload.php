<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) { header("Location: index.php"); exit(); }
require_once '../config/db_config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Artwork | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif; color: #1e293b; }
        .premium-card { background: #fff; border-radius: 16px; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
        .form-label { font-weight: 700; font-size: 0.75rem; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
        .form-control { border-radius: 10px; padding: 12px; border: 1px solid #e2e8f0; background: #fff; }
        .image-upload-zone { border: 2px dashed #cbd5e1; border-radius: 12px; padding: 30px; background: #f8fafc; cursor: pointer; transition: 0.3s; }
        .image-upload-zone:hover { border-color: #000; background: #fff; }
        .preview-img { max-height: 250px; border-radius: 12px; display: none; margin: 15px auto; width: 100%; object-fit: contain; }
        .btn-save { background: #000; color: #fff; font-weight: 700; padding: 16px; border-radius: 12px; border: none; width: 100%; transition: 0.3s; }
        .btn-save:hover { background: #333; transform: translateY(-2px); }
        
        /* Mobile Spacing Fix */
        @media (max-width: 991.98px) {
            .main-content { padding: 15px !important; margin-left: 0 !important; }
            .premium-card { padding: 20px !important; }
        }
    </style>
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token']; ?>">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex align-items-center mb-4">
                <a href="index.php" class="btn btn-light border rounded-circle me-3"><i class="fas fa-arrow-left"></i></a>
                <h3 class="fw-bold m-0">Add New Artwork</h3>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="premium-card p-4 p-md-5">
                        <form id="uploadForm" enctype="multipart/form-data">
                            <!-- Security Token -->
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            
                            <div class="row g-4">
                                <div class="col-md-6 border-end-md">
                                    <div class="mb-3">
                                        <label class="form-label">Painting Title</label>
                                        <input type="text" name="title" class="form-control" required placeholder="Masterpiece name...">
                                    </div>
                                    <div class="row g-3 mb-3">
                                        <div class="col-6">
                                            <label class="form-label">Price (PHP)</label>
                                            <input type="number" name="price" class="form-control" required placeholder="0.00">
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" name="is_negotiable" id="isNegotiable" checked>
                                                <label class="form-check-label x-small fw-bold text-dark" for="isNegotiable" style="font-size: 0.7rem;">Negotiable</label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label">Dimensions</label>
                                            <input type="text" name="size" class="form-control" required placeholder="e.g. 24x36">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Medium</label>
                                        <input type="text" name="medium" class="form-control" required placeholder="e.g. Acrylic on Canvas">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Category / Tags</label>
                                        <input type="text" name="tags" id="artTags" class="form-control mb-2" placeholder="Abstract, Portrait, etc.">
                                        <div class="d-flex flex-wrap gap-2">
                                            <?php foreach(['Abstract','Modern','Portrait','Landscape','Minimalist'] as $t): ?>
                                                <span class="badge bg-light text-dark border p-2" style="cursor: pointer; font-size: 0.7rem;" onclick="addTag('<?php echo $t; ?>')"><?php echo $t; ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" class="form-control" rows="4" required placeholder="Tell the story..."></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Artwork Image</label>
                                        <div class="image-upload-zone text-center" onclick="document.getElementById('imageInput').click()">
                                            <input type="file" name="image" id="imageInput" class="d-none" accept="image/*" required>
                                            <div id="uploadPlaceholder">
                                                <i class="fas fa-image fa-2x text-secondary mb-2"></i>
                                                <p class="small text-secondary mb-0">Select Artwork Image</p>
                                            </div>
                                            <img id="imagePreview" src="#" alt="Preview" class="preview-img">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn-save">
                                    <i class="fas fa-save me-2"></i> SAVE & PUBLISH PAINTING
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const getEl = (id) => document.getElementById(id);
        const imageInput = getEl('imageInput');
        const imagePreview = getEl('imagePreview');
        const uploadPlaceholder = getEl('uploadPlaceholder');

        imageInput.onchange = evt => {
            const [file] = imageInput.files;
            if (file) { 
                imagePreview.src = URL.createObjectURL(file); 
                imagePreview.style.display = 'block'; 
                uploadPlaceholder.style.display = 'none';
            }
        }

        function addTag(tag) {
            const input = getEl('artTags');
            let current = input.value.split(',').map(t => t.trim()).filter(t => t !== "");
            if (!current.includes(tag)) { current.push(tag); input.value = current.join(', '); }
        }

        getEl('uploadForm').onsubmit = async (e) => {
            e.preventDefault();
            Swal.fire({ title: 'Publishing...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            
            const formData = new FormData(e.target);
            try {
                const response = await fetch('save_artwork.php', { 
                    method: 'POST', 
                    body: formData 
                });
                const result = await response.json();
                if (result.status === 'success') {
                    Swal.fire('Published!', 'Painting is now live.', 'success').then(() => window.location.href = 'index.php');
                } else {
                    Swal.fire('Error', result.message || 'Failed to save.', 'error');
                }
            } catch (err) {
                Swal.fire('Error', 'Connection error.', 'error');
            }
        };
    </script>
</body>
</html>
