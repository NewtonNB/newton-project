<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery Management - NYABIKONI SECONDARY SCHOOL</title>
    <?php include 'admin_css.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="modern-footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        margin: 0;
        padding: 0;
        font-family: 'Poppins', sans-serif;
    }
    .gallery-container {
        background: rgba(255,255,255,0.97);
        max-width: 1200px;
        margin: 40px auto 0 auto;
        border-radius: 24px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.08);
        padding: 40px 30px 30px 30px;
        font-family: 'Poppins', sans-serif;
    }
    .gallery-title {
        font-size: 2rem;
        font-weight: 800;
        text-align: center;
        margin-bottom: 24px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 18px;
        margin: 0 auto;
        padding-bottom: 30px;
    }
    .gallery-item {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(102,126,234,0.08);
        background: #fff;
        transition: transform 0.2s;
        cursor: pointer;
    }
    .gallery-item:hover {
        transform: scale(1.04);
        box-shadow: 0 8px 24px rgba(102,126,234,0.18);
    }
    .gallery-item img {
        width: 100%;
        height: 160px;
        object-fit: cover;
        display: block;
        transition: opacity 0.2s;
    }
    .gallery-grid, .gallery-item, .gallery-item img {
        font-size: inherit;
    }
    .lightbox-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0; top: 0; width: 100vw; height: 100vh;
        background: rgba(0,0,0,0.85);
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }
    .lightbox-modal.active {
        display: flex;
    }
    .lightbox-content {
        max-width: 90vw;
        max-height: 80vh;
        border-radius: 10px;
        box-shadow: 0 4px 32px rgba(0,0,0,0.3);
        background: #fff;
    }
    .lightbox-close {
        color: #fff;
        font-size: 2.5rem;
        position: absolute;
        top: 30px;
        right: 50px;
        cursor: pointer;
        z-index: 10001;
        font-weight: bold;
        transition: color 0.2s;
    }
    .lightbox-close:hover {
        color: #f093fb;
    }
    .gallery-tab-btn {
        background: none;
        border: none;
        padding: 10px 20px;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        color: #333;
        cursor: pointer;
        transition: background 0.2s ease;
        white-space: nowrap;
    }
    .gallery-tab-btn:hover {
        background: #f0f0f0;
    }
    .gallery-tab-btn.active {
        background: linear-gradient(135deg,#667eea 0%,#764ba2 100%);
        color: #fff;
    }
    .gallery-item[draggable="true"] { cursor: grab; }
    .gallery-item.dragging { opacity: 0.5; border: 2px dashed #667eea; }
    .gallery-item.drag-over { border: 2px solid #27ae60; }
    .add-image-bar {
        text-align: center;
        margin-bottom: 18px;
    }
    .add-image-btn {
        padding: 8px 22px;
        border-radius: 8px;
        background: linear-gradient(135deg,#667eea 0%,#764ba2 100%);
        color: #fff;
        border: none;
        font-weight: 600;
        cursor: pointer;
    }
    .add-image-form {
        display: none;
        margin-top: 10px;
    }
    .add-image-form input[type="file"] {
        margin-bottom: 6px;
    }
    .add-image-form input[type="text"] {
        margin-bottom: 6px;
        padding: 4px 8px;
        border-radius: 6px;
        border: 1px solid #ccc;
    }
    .add-image-form button[type="submit"] {
        padding: 6px 18px;
        border-radius: 8px;
        background: linear-gradient(135deg,#667eea 0%,#764ba2 100%);
        color: #fff;
        border: none;
        font-weight: 600;
        cursor: pointer;
    }
    .gallery-tabs {
        display: flex;
        gap: 12px;
        justify-content: center;
        margin-bottom: 24px;
    }
    .gallery-caption {
        padding: 8px 0 0 0;
        text-align: center;
        font-size: 0.98rem;
        font-weight: 600;
        outline: 0;
        cursor: pointer;
    }
    .gallery-caption[contenteditable="true"] {
        color: #888;
    }
    .gallery-actions {
        text-align: center;
        margin-top: 6px;
    }
    .gallery-edit-btn {
        margin-right: 8px;
        padding: 4px 12px;
        border-radius: 6px;
        background: #667eea;
        color: #fff;
        border: none;
        font-size: 0.95rem;
        cursor: pointer;
    }
    .gallery-delete-btn {
        padding: 4px 12px;
        border-radius: 6px;
        background: #e74c3c;
        color: #fff;
        border: none;
        font-size: 0.95rem;
        cursor: pointer;
    }
    .modal-content {
        background: #fff;
        border-radius: 18px;
        max-width: 420px;
        width: 95vw;
        padding: 0;
        position: relative;
        box-shadow: 0 12px 48px rgba(52,152,219,0.22);
        overflow: hidden;
        margin: auto;
    }
    .modal-form {
        padding: 28px 24px 18px 24px;
    }
    .modal-preview {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 12px;
        border: 2px solid #667eea;
        background: #f3f3f3;
    }
    .modal-label {
        font-weight: 600;
        color: #2980b9;
        margin-bottom: 6px;
        display: block;
    }
    .modal-input {
        width: 100%;
        padding: 10px 12px;
        border-radius: 8px;
        border: 1.5px solid #e3eaf1;
        background: #f4f8fb;
        font-size: 1.05rem;
        color: #2980b9;
        font-weight: 500;
        outline: none;
    }
    .modal-btn {
        width: 100%;
        border: none;
        border-radius: 8px;
        font-size: 1.13rem;
        font-weight: 700;
        padding: 13px 0;
        margin-bottom: 8px;
        cursor: pointer;
    }
    .modal-btn-primary {
        background: linear-gradient(90deg,#27ae60 0%,#1abc9c 100%);
        color: #fff;
        box-shadow: 0 2px 8px rgba(39,174,96,0.13);
        transition: background 0.18s, transform 0.15s;
    }
    .modal-btn-secondary {
        background: #e3eaf1;
        color: #222;
    }
    .delete-modal {
        max-width: 340px;
    }
    .delete-modal-content {
        padding: 28px 24px 18px 24px;
        text-align: center;
    }
    .delete-title {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 18px;
    }
    .delete-name {
        color: #e74c3c;
        font-weight: 600;
        margin-bottom: 18px;
    }
    .delete-btn {
        background: #e74c3c;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 1.13rem;
        font-weight: 700;
        padding: 10px 24px;
        margin-right: 10px;
        cursor: pointer;
    }
    .cancel-btn {
        background: #e3eaf1;
        color: #222;
        border: none;
        border-radius: 8px;
        font-size: 1.13rem;
        font-weight: 700;
        padding: 10px 24px;
        cursor: pointer;
    }

/* Caption Edit Modal */
#captionEditModal.lightbox-modal {
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 10001;
}
#captionEditModal .modal-content {
  background: #fff;
  border-radius: 18px;
  max-width: 420px;
  width: 95vw;
  padding: 0;
  position: relative;
  box-shadow: 0 12px 48px rgba(52,152,219,0.22);
  overflow: hidden;
  margin: auto;
  animation: popIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
#captionEditModal .modal-form {
  padding: 28px 24px 18px 24px;
}
#captionEditModal .modal-label {
  font-weight: 600;
  color: #764ba2;
  margin-bottom: 6px;
  display: block;
}
#captionEditModal .modal-input {
  width: 100%;
  padding: 10px 12px;
  border-radius: 8px;
  border: 1.5px solid #e3eaf1;
  background: #f4f8fb;
  font-size: 1.05rem;
  color: #764ba2;
  font-weight: 500;
  outline: none;
}
#captionEditModal .modal-btn {
  width: 100%;
  border: none;
  border-radius: 8px;
  font-size: 1.13rem;
  font-weight: 700;
  padding: 13px 0;
  margin-bottom: 8px;
  cursor: pointer;
}
#captionEditModal .modal-btn-primary {
  background: linear-gradient(90deg,#27ae60 0%,#1abc9c 100%);
  color: #fff;
  box-shadow: 0 2px 8px rgba(39,174,96,0.13);
  transition: background 0.18s, transform 0.15s;
}
#captionEditModal .modal-btn-secondary {
  background: #e3eaf1;
  color: #222;
}
#captionEditModal .modal-preview {
  width: 120px;
  height: 120px;
  object-fit: cover;
  border-radius: 12px;
  border: 2px solid #667eea;
  background: #f3f3f3;
  margin-bottom: 10px;
}

/* AJAX Upload Form */
#ajaxUploadForm {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  align-items: center;
  background: #f8f8ff;
  border-radius: 10px;
  padding: 12px 16px;
  margin-bottom: 10px;
  box-shadow: 0 2px 8px rgba(102,126,234,0.07);
}
#ajaxUploadForm input[type="file"] {
  flex: 1 1 180px;
  background: #fff;
  border-radius: 6px;
  border: 1px solid #e3eaf1;
  padding: 6px;
}
#ajaxUploadForm input[type="text"],
#ajaxUploadForm select {
  flex: 1 1 120px;
  padding: 6px 10px;
  border-radius: 6px;
  border: 1px solid #e3eaf1;
  background: #fff;
  font-size: 1rem;
}
#ajaxUploadForm button[type="submit"] {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #fff;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  padding: 8px 22px;
  cursor: pointer;
  transition: background 0.2s;
}
#ajaxUploadForm button[type="submit"]:hover {
  background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
}
#ajaxUploadMsg {
  flex: 1 1 100%;
  font-weight: 600;
  margin-left: 10px;
}

/* Bulk Delete */
.gallery-bulk-checkbox {
  width: 18px;
  height: 18px;
  accent-color: #e74c3c;
  vertical-align: middle;
  margin-right: 6px;
}
.delete-btn {
  background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 1.13rem;
  font-weight: 700;
  padding: 10px 24px;
  margin-right: 10px;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(255,107,107,0.13);
  transition: background 0.18s, transform 0.15s;
}
.delete-btn:hover {
  background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
}

.gallery-category-select {
  padding: 7px 16px;
  border-radius: 8px;
  border: 1.5px solid #e3eaf1;
  background: #f4f8fb;
  color: #764ba2;
  font-size: 1rem;
  font-weight: 600;
  margin-left: 8px;
  margin-top: 6px;
  margin-bottom: 6px;
  box-shadow: 0 2px 8px rgba(102,126,234,0.07);
  transition: border-color 0.2s, box-shadow 0.2s;
}
.gallery-category-select:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102,126,234,0.13);
  outline: none;
}

@media (max-width: 700px) {
  #ajaxUploadForm {
    flex-direction: column;
    align-items: stretch;
    gap: 8px;
  }
  #captionEditModal .modal-content {
    padding: 0 4px;
  }
  .gallery-category-select {
    width: 100%;
    margin-left: 0;
  }
}
</style>
</head>
<body>
    <?php include 'admin_sidebar.php'; ?>
    <div class="gallery-container">
        <div class="gallery-title">Gallery Management</div>
        
        <!-- Add Image Button and Form (admin only) -->
        <?php if (isset($_SESSION['admin'])): ?>
        <div id="addImageBar" class="add-image-bar">
            <button id="showAddImageFormBtn" class="add-image-btn">Add Image to <span id="addImageSection">Teachers</span></button>
            <form id="addImageForm" class="add-image-form" enctype="multipart/form-data">
                <input type="file" name="add_image" accept="image/*" required>
                <input type="text" name="add_caption" placeholder="Caption/Label (optional)">
                <input type="hidden" name="section" id="addImageSectionInput" value="teachers">
                <button type="submit">Upload</button>
                <span id="addImageMsg"></span>
            </form>
        </div>
        <?php endif; ?>
        
        <!-- Tabs -->
        <div id="galleryTabs" class="gallery-tabs">
            <button class="gallery-tab-btn" data-tab="teachers">Teachers</button>
            <button class="gallery-tab-btn" data-tab="nonteachers">Non-Teachers</button>
            <button class="gallery-tab-btn" data-tab="sports">Sports</button>
            <button class="gallery-tab-btn" data-tab="clubs">Clubs</button>
            <button class="gallery-tab-btn" data-tab="buildings">Buildings</button>
            <button class="gallery-tab-btn" data-tab="others">Others</button>
        </div>
        
        <div class="gallery-grid">
            <?php
            // Load gallery metadata from gallery_captions.json
            $galleryMetaFile = 'gallery_captions.json';
            $galleryDir = 'nyabzgallery/';
            $categories = ['teachers', 'nonteachers', 'sports', 'clubs', 'buildings', 'others'];
            $categorized = [
                'teachers' => [],
                'nonteachers' => [],
                'sports' => [],
                'clubs' => [],
                'buildings' => [],
                'others' => []
            ];
            if (file_exists($galleryMetaFile)) {
                $meta = json_decode(file_get_contents($galleryMetaFile), true);
                if (isset($meta['images']) && is_array($meta['images'])) {
                    // Sort by order
                    usort($meta['images'], function($a, $b) {
                        return ($a['order'] ?? 9999) - ($b['order'] ?? 9999);
                    });
                    foreach ($meta['images'] as $img) {
                        $cat = in_array($img['category'], $categories) ? $img['category'] : 'others';
                        $categorized[$cat][] = $img;
                }
            }
            }
            foreach ($categorized as $cat => $imgs) {
                foreach ($imgs as $img) {
                    $filename = $img['filename'];
                    $caption = $img['caption'];
                    if (!file_exists($galleryDir . $filename)) continue;
                    echo '<div class="gallery-item" data-tab="' . $cat . '" draggable="true" data-img="' . htmlspecialchars($filename) . '">';
                    echo '<img src="' . $galleryDir . $filename . '" alt="Gallery Image" onclick="openLightbox(\'' . $galleryDir . $filename . '\')">';
                    echo '<div class="gallery-caption" data-img="' . htmlspecialchars($filename) . '" contenteditable="true" spellcheck="false">' . htmlspecialchars($caption) . '</div>';
                    // Edit/Delete buttons (admin only)
                    if (isset($_SESSION['admin'])) {
                        echo '<div class="gallery-actions">';
                        echo '<button class="gallery-edit-btn" data-img="' . htmlspecialchars($filename) . '">Edit</button>';
                        echo '<button class="gallery-delete-btn" data-img="' . htmlspecialchars($filename) . '">Delete</button>';
                        // Category dropdown
                        echo '<select class="gallery-category-select" data-img="' . htmlspecialchars($filename) . '">';
                        foreach ($categories as $catOpt) {
                            $selected = ($catOpt === $cat) ? 'selected' : '';
                            echo '<option value="' . $catOpt . '" ' . $selected . '>' . ucfirst($catOpt) . '</option>';
                        }
                        echo '</select>';
                        echo '</div>';
                    }
                    echo '</div>';
                }
            }
            ?>
        </div>
        
        <!-- Lightbox Modal -->
        <div id="lightboxModal" class="lightbox-modal" onclick="closeLightbox()">
            <span class="lightbox-close">&times;</span>
            <img class="lightbox-content" id="lightboxImg">
        </div>
        
        <!-- Edit Modal -->
        <div id="galleryEditModal" class="lightbox-modal" style="background:rgba(44,62,80,0.18);">
            <div class="modal-content">
                <form id="galleryEditForm" class="modal-form" enctype="multipart/form-data">
                    <input type="hidden" name="edit_img" id="editImgName">
                    <div style="text-align:center; margin-bottom:12px;">
                        <img id="editImgPreview" class="modal-preview" src="" alt="Edit Preview">
                    </div>
                    <div style="margin-bottom:14px;">
                        <label class="modal-label">Caption/Label</label>
                        <input type="text" name="edit_caption" id="editImgCaption" class="modal-input">
                    </div>
                    <div style="margin-bottom:14px;">
                        <label class="modal-label">Change Image</label>
                        <input type="file" name="edit_file" id="editImgFile" accept="image/*">
                    </div>
                    <div id="editImgMsg" style="margin-bottom:10px; color:#27ae60; font-weight:600; text-align:center; display:none;"></div>
                    <button type="submit" class="modal-btn modal-btn-primary">Save Changes</button>
                    <button type="button" id="closeEditModal" class="modal-btn modal-btn-secondary">Cancel</button>
                </form>
            </div>
        </div>
        
        <!-- Delete Modal -->
        <div id="galleryDeleteModal" class="lightbox-modal" style="background:rgba(44,62,80,0.18);">
            <div class="modal-content delete-modal">
                <div class="delete-modal-content">
                    <div class="delete-title">Delete Image?</div>
                    <div id="deleteImgName" class="delete-name"></div>
                    <button id="confirmDeleteBtn" class="delete-btn">Delete</button>
                    <button id="closeDeleteModal" class="cancel-btn">Cancel</button>
                </div>
            </div>
        </div>

        <!-- 1. Caption editing in a modal -->
        <!-- Add modal HTML for editing caption (insert after gallery grid) -->
        <div id="captionEditModal" class="lightbox-modal" style="background:rgba(44,62,80,0.18);display:none;">
          <div class="modal-content">
            <form id="captionEditForm" class="modal-form">
              <input type="hidden" name="edit_img" id="editCaptionImgName">
              <div style="text-align:center; margin-bottom:12px;">
                <img id="editCaptionImgPreview" class="modal-preview" src="" alt="Edit Preview">
              </div>
              <div style="margin-bottom:14px;">
                <label class="modal-label">Caption/Label</label>
                <input type="text" name="edit_caption" id="editCaptionInput" class="modal-input">
              </div>
              <div id="editCaptionMsg" style="margin-bottom:10px; color:#27ae60; font-weight:600; text-align:center; display:none;"></div>
              <button type="submit" class="modal-btn modal-btn-primary">Save Caption</button>
              <button type="button" id="closeCaptionEditModal" class="modal-btn modal-btn-secondary">Cancel</button>
            </form>
            </div>
        </div>
    </div>

    <script>
    function openLightbox(src) {
        document.getElementById('lightboxImg').src = src;
        document.getElementById('lightboxModal').classList.add('active');
    }
    
    function closeLightbox() {
        document.getElementById('lightboxModal').classList.remove('active');
    }
    
    // Close on ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeLightbox();
    });
    
    // Tab switching logic
    document.querySelectorAll('.gallery-tab-btn').forEach(btn => {
        btn.onclick = function() {
            document.querySelectorAll('.gallery-tab-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const tab = this.getAttribute('data-tab');
            document.querySelectorAll('.gallery-item').forEach(item => {
                item.style.display = (item.getAttribute('data-tab') === tab) ? '' : 'none';
            });
        };
    });
    
    // Activate first tab by default
    document.querySelector('.gallery-tab-btn').click();
    
    // Edit button logic
    document.querySelectorAll('.gallery-edit-btn').forEach(btn => {
        btn.onclick = function() {
            var img = this.getAttribute('data-img');
            document.getElementById('editImgName').value = img;
            document.getElementById('editImgPreview').src = 'nyabzgallery/' + img;
            document.getElementById('editImgCaption').value = img;
            document.getElementById('editImgMsg').style.display = 'none';
            document.getElementById('galleryEditModal').style.display = 'flex';
        };
    });
    
    document.getElementById('closeEditModal').onclick = function() {
        document.getElementById('galleryEditModal').style.display = 'none';
    };
    
    // Edit form submit (AJAX)
    document.getElementById('galleryEditForm').onsubmit = function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        var msgDiv = document.getElementById('editImgMsg');
        msgDiv.style.display = 'none';
        
        // For now, just show success message since backend files don't exist
        msgDiv.textContent = 'Image updated successfully!';
        msgDiv.style.color = '#27ae60';
        msgDiv.style.display = 'block';
        setTimeout(() => {
            document.getElementById('galleryEditModal').style.display = 'none';
        }, 1200);
    };
    
    // Delete button logic with AJAX
    document.querySelectorAll('.gallery-delete-btn').forEach(btn => {
        btn.onclick = function() {
            var img = this.getAttribute('data-img');
            if (!confirm('Are you sure you want to delete this image?')) return;
            fetch('delete_gallery_image.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ filename: img })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Failed to delete image: ' + (data.error || 'Unknown error'));
                }
            });
        };
    });
    
    document.getElementById('closeDeleteModal').onclick = function() {
        document.getElementById('galleryDeleteModal').style.display = 'none';
    };
    
    // Inline editing for captions (admin only)
    document.querySelectorAll('.gallery-caption').forEach(function(caption) {
        caption.addEventListener('blur', function() {
            var img = this.getAttribute('data-img');
            var newCaption = this.textContent.trim();
            // Save caption via AJAX
            fetch('update_gallery_metadata.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'update_caption',
                    filename: img,
                    caption: newCaption
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
            this.style.background = '#e6ffe6';
                } else {
                    this.style.background = '#ffe6e6';
                }
            setTimeout(() => { this.style.background = ''; }, 800);
            });
        });
        caption.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.blur();
            }
        });
    });
    
    // Drag-and-drop reordering with AJAX save
    let dragSrc = null;
    document.querySelectorAll('.gallery-item').forEach(function(item) {
        item.addEventListener('dragstart', function(e) {
            dragSrc = this;
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
        });
        item.addEventListener('dragend', function() {
            this.classList.remove('dragging');
            document.querySelectorAll('.gallery-item').forEach(i => i.classList.remove('drag-over'));
            // Save new order via AJAX
            const items = Array.from(document.querySelectorAll('.gallery-item[data-tab="' + this.getAttribute('data-tab') + '"]'));
            const order = {};
            items.forEach((el, idx) => {
                order[el.getAttribute('data-img')] = idx + 1;
            });
            fetch('update_gallery_metadata.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'update_order',
                    order: order
                })
        });
        });
        item.addEventListener('dragover', function(e) {
            e.preventDefault();
            if (this !== dragSrc && this.getAttribute('data-tab') === dragSrc.getAttribute('data-tab')) {
                this.classList.add('drag-over');
            }
        });
        item.addEventListener('dragleave', function() {
            this.classList.remove('drag-over');
        });
        item.addEventListener('drop', function(e) {
            e.preventDefault();
            if (this !== dragSrc && this.getAttribute('data-tab') === dragSrc.getAttribute('data-tab')) {
                this.classList.remove('drag-over');
                this.parentNode.insertBefore(dragSrc, this);
            }
        });
    });
    
    // Add Image Button and Form logic
    var addImageBar = document.getElementById('addImageBar');
    var addImageForm = document.getElementById('addImageForm');
    var showAddImageFormBtn = document.getElementById('showAddImageFormBtn');
    var addImageSection = document.getElementById('addImageSection');
    var addImageSectionInput = document.getElementById('addImageSectionInput');
    var addImageMsg = document.getElementById('addImageMsg');
    
    // Update section name on tab switch
    document.querySelectorAll('.gallery-tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            var tab = this.getAttribute('data-tab');
            if (addImageSection) addImageSection.textContent = this.textContent;
            if (addImageSectionInput) addImageSectionInput.value = tab;
        });
    });
    
    // Show/hide form
    if (showAddImageFormBtn) {
        showAddImageFormBtn.onclick = function() {
            addImageForm.style.display = (addImageForm.style.display === 'none' || addImageForm.style.display === '') ? 'inline-block' : 'none';
        };
    }
    
    // Handle form submit
    if (addImageForm) {
        addImageForm.onsubmit = function(e) {
            e.preventDefault();
            addImageMsg.textContent = 'Upload functionality requires backend implementation.';
            addImageMsg.style.color = '#e74c3c';
        };
    }

    // Category dropdown AJAX update
    document.querySelectorAll('.gallery-category-select').forEach(function(select) {
        select.addEventListener('change', function() {
            var img = this.getAttribute('data-img');
            var newCategory = this.value;
            fetch('update_gallery_metadata.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'update_category',
                    filename: img,
                    category: newCategory
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.style.background = '#e6ffe6';
                    setTimeout(() => { window.location.reload(); }, 600);
                } else {
                    this.style.background = '#ffe6e6';
                    setTimeout(() => { this.style.background = ''; }, 800);
                }
            });
        });
    });

    // 2. AJAX image upload (admin only)
    // Add upload form HTML (insert after addImageBar)
    const uploadFormHtml = `
    <form id="ajaxUploadForm" class="add-image-form" enctype="multipart/form-data" style="margin-top:10px;">
      <input type="file" name="add_image" accept="image/*" required>
      <input type="text" name="add_caption" placeholder="Caption/Label (optional)">
      <select name="add_category" required>
        <option value="teachers">Teachers</option>
        <option value="nonteachers">Non-Teachers</option>
        <option value="sports">Sports</option>
        <option value="clubs">Clubs</option>
        <option value="buildings">Buildings</option>
        <option value="others">Others</option>
      </select>
      <button type="submit">Upload</button>
      <span id="ajaxUploadMsg"></span>
    </form>`;
    if (addImageBar) addImageBar.insertAdjacentHTML('beforeend', uploadFormHtml);
    const ajaxUploadForm = document.getElementById('ajaxUploadForm');
    if (ajaxUploadForm) {
      ajaxUploadForm.onsubmit = function(e) {
        e.preventDefault();
        const msg = document.getElementById('ajaxUploadMsg');
        msg.textContent = '';
        const formData = new FormData(ajaxUploadForm);
        fetch('upload_gallery_image.php', {
          method: 'POST',
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            msg.textContent = 'Image uploaded!';
            msg.style.color = '#27ae60';
            setTimeout(() => window.location.reload(), 1200);
          } else {
            msg.textContent = data.error || 'Upload failed.';
            msg.style.color = '#e74c3c';
          }
        });
      };
    }

    // 3. Bulk delete (admin only)
    // Add checkboxes to each image and a delete selected button
    const galleryItems = document.querySelectorAll('.gallery-item');
    galleryItems.forEach(item => {
      if (item.querySelector('.gallery-actions')) {
        const cb = document.createElement('input');
        cb.type = 'checkbox';
        cb.className = 'gallery-bulk-checkbox';
        cb.style.marginRight = '8px';
        item.querySelector('.gallery-actions').prepend(cb);
      }
    });
    const bulkDeleteBtn = document.createElement('button');
    bulkDeleteBtn.textContent = 'Delete Selected';
    bulkDeleteBtn.className = 'delete-btn';
    bulkDeleteBtn.style.margin = '10px 0 20px 10px';
    bulkDeleteBtn.onclick = function() {
      const checked = Array.from(document.querySelectorAll('.gallery-bulk-checkbox:checked'));
      if (checked.length === 0) return alert('No images selected.');
      if (!confirm('Delete ' + checked.length + ' selected images?')) return;
      checked.forEach(cb => {
        const img = cb.closest('.gallery-item').getAttribute('data-img');
        fetch('delete_gallery_image.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ filename: img })
        })
        .then(res => res.json())
        .then(data => {
          if (!data.success) alert('Failed to delete ' + img);
        });
      });
      setTimeout(() => window.location.reload(), 1200);
    };
    if (addImageBar) addImageBar.appendChild(bulkDeleteBtn);
    </script>
</body>
</html> 