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
    <style>
        body { background: #f4f6f9; font-family: 'Inter', sans-serif; }
        .glass-card { background: #fff; border-radius: 12px; border: none; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1); }
        .preview-img { max-height: 300px; border-radius: 8px; display: none; margin: 20px auto; border: 1px solid #eee; width: 100%; object-fit: contain; }
        .loading-overlay { 
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
            background: rgba(0,0,0,0.8); display: none; flex-direction: column;
            align-items: center; justify-content: center; z-index: 9999;
        }
        .tag-sug { cursor: pointer; transition: all 0.2s; font-size: 0.8rem; }
        .tag-sug:hover { background: #000 !important; color: #fff !important; }
        .form-label { font-weight: 600; font-size: 0.75rem; color: #4e73df; text-transform: uppercase; letter-spacing: 0.5px; }
    </style>
</head>
<body>
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner-border text-light mb-3" style="width: 3rem; height: 3rem;"></div>
        <h4 id="loadingText" class="text-white">Analyzing with AI...</h4>
    </div>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="d-flex align-items-center mb-5">
            <a href="index.php" class="btn btn-sm btn-outline-secondary me-3"><i class="fas fa-arrow-left"></i></a>
            <h3 class="fw-bold m-0">New Painting</h3>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="glass-card p-4 p-md-5">
                    <form id="uploadForm">
                        <div class="row g-3 g-md-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control form-control-lg shadow-sm" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Price (PHP)</label>
                                <div class="input-group input-group-lg shadow-sm">
                                    <input type="number" name="price" class="form-control" required>
                                    <div class="input-group-text bg-white">
                                        <input class="form-check-input mt-0 me-1" type="checkbox" name="is_negotiable" id="isNegotiable">
                                        <label class="x-small mb-0" for="isNegotiable">Negot.</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Size</label>
                                <input type="text" name="size" class="form-control shadow-sm" required placeholder="24x36\"">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Medium</label>
                                <input type="text" name="medium" class="form-control shadow-sm" required placeholder="Acrylic">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Artwork Image</label>
                            <div class="p-3 border-dashed rounded text-center bg-light">
                                <input type="file" name="image" id="imageInput" class="form-control form-control-sm" accept="image/*" required>
                                <img id="imagePreview" src="#" alt="Preview" class="preview-img shadow-sm">
                            </div>
                        </div>

                        <div id="aiSection" class="animate__animated animate__fadeIn d-none bg-light p-3 p-md-4 rounded mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-dark text-white p-2 rounded me-3"><i class="fas fa-robot"></i></div>
                                <div><h6 class="fw-bold mb-0">AI Suggestions</h6></div>
                            </div>
                            <textarea name="ai_description" id="aiDesc" class="form-control shadow-sm mb-3" rows="4"></textarea>
                            <label class="form-label">Tags</label>
                            <input type="text" name="ai_tags" id="aiTags" class="form-control shadow-sm mb-2">
                            <div id="quickTags" class="d-flex flex-wrap gap-1">
                                <?php foreach(['Abstract','Contemporary','Vibrant','Minimalist','Landscape','Portrait'] as $t): ?>
                                    <span class="badge bg-white text-dark border p-2 tag-sug" onclick="addTag('<?php echo $t; ?>')"><?php echo $t; ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="button" id="btnAnalyze" class="btn btn-dark btn-lg py-3 shadow"><i class="fas fa-magic me-2"></i> Analyze with AI</button>
                            <button type="submit" id="btnSave" class="btn btn-dark btn-lg py-3 shadow d-none"><i class="fas fa-save me-2"></i> Save Painting</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const imageInput = document.getElementById('imageInput');
        const imagePreview = document.getElementById('imagePreview');
        const btnAnalyze = document.getElementById('btnAnalyze');
        const btnSave = document.getElementById('btnSave');
        const aiSection = document.getElementById('aiSection');
        const loadingOverlay = document.getElementById('loadingOverlay');

        function addTag(tag) {
            const input = document.getElementById('aiTags');
            let currentTags = input.value.split(',').map(t => t.trim()).filter(t => t !== "");
            if (!currentTags.includes(tag)) { currentTags.push(tag); input.value = currentTags.join(', '); }
        }

        imageInput.onchange = evt => {
            const [file] = imageInput.files;
            if (file) { imagePreview.src = URL.createObjectURL(file); imagePreview.style.display = 'block'; }
        }

        btnAnalyze.onclick = async () => {
            if(!document.getElementById('uploadForm').checkValidity()) { document.getElementById('uploadForm').reportValidity(); return; }
            loadingOverlay.style.display = 'flex';
            const formData = new FormData(document.getElementById('uploadForm'));
            try {
                const response = await fetch('process_ai.php', { method: 'POST', body: formData });
                const result = await response.json();
                if (result.status === 'success') {
                    document.getElementById('aiDesc').value = result.description;
                    document.getElementById('aiTags').value = result.tags;
                    const cUrl = document.createElement('input'); cUrl.type = 'hidden'; cUrl.name = 'cloudinary_url'; cUrl.value = result.image_url;
                    const cId = document.createElement('input'); cId.type = 'hidden'; cId.name = 'cloudinary_id'; cId.value = result.cloudinary_id;
                    document.getElementById('uploadForm').appendChild(cUrl); document.getElementById('uploadForm').appendChild(cId);
                    aiSection.classList.remove('d-none'); btnAnalyze.classList.add('d-none'); btnSave.classList.remove('d-none');
                }
            } finally { loadingOverlay.style.display = 'none'; }
        };

        document.getElementById('uploadForm').onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const response = await fetch('save_artwork.php', { method: 'POST', body: formData });
            const result = await response.json();
            if (result.status === 'success') Swal.fire('Saved!', 'Painting is live.', 'success').then(() => window.location.href = 'index.php');
        };
    </script>
</body>
</html>
