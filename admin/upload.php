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
        <div class="container-fluid py-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <a href="index.php" class="btn btn-sm btn-light border me-3 shadow-sm"><i class="fas fa-arrow-left"></i></a>
                    <h3 class="fw-bold m-0" style="letter-spacing: -0.5px;">New Painting</h3>
                </div>
                <div class="text-secondary small d-none d-md-block">Matthew Rillera's Studio &bull; Admin Access</div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="glass-card shadow-sm overflow-hidden">
                        <div class="row g-0">
                            <!-- Left Side: Form -->
                            <div class="col-md-7 p-4 p-md-5 border-end">
                                <form id="uploadForm">
                                    <div class="mb-4">
                                        <label class="form-label">Painting Title</label>
                                        <input type="text" name="title" class="form-control form-control-lg border-light bg-light" required placeholder="Enter title...">
                                    </div>
                                    
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-7">
                                            <label class="form-label">Price (PHP)</label>
                                            <div class="input-group">
                                                <span class="input-group-text border-light bg-light">₱</span>
                                                <input type="number" name="price" class="form-control border-light bg-light" required placeholder="0.00" style="max-width: 150px;">
                                                <div class="input-group-text border-light bg-white flex-grow-1">
                                                    <div class="form-check m-0">
                                                        <input class="form-check-input" type="checkbox" name="is_negotiable" id="isNegotiable">
                                                        <label class="form-check-label x-small fw-bold text-dark" for="isNegotiable">Negotiable</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label">Dimensions</label>
                                            <input type="text" name="size" class="form-control border-light bg-light" required placeholder="e.g. 24x36">
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label">Medium / Material</label>
                                        <input type="text" name="medium" class="form-control border-light bg-light" required placeholder="e.g. Acrylic on Canvas">
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label">Artwork Image</label>
                                        <div class="image-upload-zone p-4 border-dashed rounded text-center bg-light" style="cursor: pointer;" onclick="document.getElementById('imageInput').click()">
                                            <input type="file" name="image" id="imageInput" class="d-none" accept="image/*" required>
                                            <div id="uploadPlaceholder">
                                                <i class="fas fa-cloud-upload-alt fa-2x text-secondary mb-2"></i>
                                                <p class="small text-secondary mb-0">Click to upload or drag and drop</p>
                                            </div>
                                            <img id="imagePreview" src="#" alt="Preview" class="preview-img mx-auto" style="display: none; max-height: 200px;">
                                        </div>
                                    </div>

                                    <div class="d-grid">
                                        <button type="button" id="btnAnalyze" class="btn btn-dark btn-lg py-3 shadow-sm"><i class="fas fa-magic me-2"></i> Analyze with AI</button>
                                        <button type="submit" id="btnSave" class="btn btn-primary btn-lg py-3 shadow-sm d-none"><i class="fas fa-save me-2"></i> Save Painting</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Right Side: AI Insights -->
                            <div class="col-md-5 bg-light p-4 p-md-5 d-flex flex-column">
                                <div id="aiSection" class="h-100 d-none animate__animated animate__fadeIn">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="bg-primary text-white p-2 rounded me-3"><i class="fas fa-robot"></i></div>
                                        <h5 class="fw-bold m-0">AI Insights</h5>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="form-label text-dark opacity-75">Generated Description</label>
                                        <textarea name="ai_description" id="aiDesc" class="form-control border-0 shadow-sm bg-white" rows="6" style="resize: none;"></textarea>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label text-dark opacity-75">Suggested Tags</label>
                                        <input type="text" name="ai_tags" id="aiTags" class="form-control border-0 shadow-sm bg-white mb-3">
                                        <div id="quickTags" class="d-flex flex-wrap gap-2">
                                            <?php foreach(['Abstract','Modern','Vibrant','Portrait','Landscape','Minimalist'] as $t): ?>
                                                <span class="badge bg-white text-dark border p-2 tag-sug" onclick="addTag('<?php echo $t; ?>')"><?php echo $t; ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-auto p-3 bg-white rounded shadow-sm border-start border-primary border-4">
                                        <p class="x-small text-secondary mb-0"><i class="fas fa-info-circle me-1"></i> These suggestions help collectors find your work through search filters.</p>
                                    </div>
                                </div>
                                
                                <div id="aiEmptyState" class="h-100 d-flex flex-column align-items-center justify-content-center text-center opacity-50 py-5">
                                    <i class="fas fa-sparkles fa-3x mb-3"></i>
                                    <h5>AI Studio Assistant</h5>
                                    <p class="small">Fill in the details and analyze to see AI suggestions.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const imageInput = document.getElementById('imageInput');
        const imagePreview = document.getElementById('imagePreview');
        const uploadPlaceholder = document.getElementById('uploadPlaceholder');
        const btnAnalyze = document.getElementById('btnAnalyze');
        const btnSave = document.getElementById('btnSave');
        const aiSection = document.getElementById('aiSection');
        const aiEmptyState = document.getElementById('aiEmptyState');
        const loadingOverlay = document.getElementById('loadingOverlay');

        function addTag(tag) {
            const input = document.getElementById('aiTags');
            let currentTags = input.value.split(',').map(t => t.trim()).filter(t => t !== "");
            if (!currentTags.includes(tag)) { currentTags.push(tag); input.value = currentTags.join(', '); }
        }

        imageInput.onchange = evt => {
            const [file] = imageInput.files;
            if (file) { 
                imagePreview.src = URL.createObjectURL(file); 
                imagePreview.style.display = 'block'; 
                uploadPlaceholder.style.display = 'none';
            }
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
                    
                    // Add hidden fields for Cloudinary if not already there
                    if(!document.querySelector('input[name="cloudinary_url"]')) {
                        const cUrl = document.createElement('input'); cUrl.type = 'hidden'; cUrl.name = 'cloudinary_url';
                        const cId = document.createElement('input'); cId.type = 'hidden'; cId.name = 'cloudinary_id';
                        document.getElementById('uploadForm').appendChild(cUrl); document.getElementById('uploadForm').appendChild(cId);
                    }
                    
                    document.querySelector('input[name="cloudinary_url"]').value = result.image_url;
                    document.querySelector('input[name="cloudinary_id"]').value = result.cloudinary_id;

                    aiSection.classList.remove('d-none'); 
                    aiEmptyState.classList.add('d-none');
                    btnAnalyze.classList.add('d-none'); 
                    btnSave.classList.remove('d-none');
                }
            } finally { loadingOverlay.style.display = 'none'; }
        };

        document.getElementById('uploadForm').onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            try {
                const response = await fetch('save_artwork.php', { method: 'POST', body: formData });
                const result = await response.json();
                if (result.status === 'success') {
                    Swal.fire('Saved!', 'Painting is live in the gallery.', 'success').then(() => window.location.href = 'index.php');
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
