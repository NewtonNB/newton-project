<?php
require_once 'config.php';
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}
// Fetch announcements from the database
$sql = "SELECT * FROM announcements ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements & Events</title>
    <?php include 'admin_css.php'; ?>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        .content {
            margin-left: 0;
            margin-top: 90px;
            padding: 20px 30px;
            min-height: calc(100vh - 70px);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }
        .announcement-list-container {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(102, 126, 234, 0.10);
            padding: 40px 24px;
            max-width: 800px;
            width: 100%;
            margin: 40px auto;
            font-size: 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .announcements-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 28px;
            width: 100%;
        }
        .announcement-card {
            background: linear-gradient(135deg, #f9faff 60%, #e3eafc 100%);
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(102, 126, 234, 0.13);
            padding: 28px 22px 20px 22px;
            transition: box-shadow 0.2s, transform 0.2s, border-left 0.3s;
            position: relative;
            border-left: 5px solid transparent; /* Default border */
        }

        /* Style for event category */
        .announcement-card.category-event {
            border-left: 5px solid #3498db;
        }

        .announcement-card:hover {
            box-shadow: 0 8px 32px rgba(102, 126, 234, 0.18);
            transform: translateY(-2px) scale(1.01);
        }
        .announcement-card h3 {
            margin-top: 0;
            color: #1976d2;
            font-size: 1.25rem;
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            letter-spacing: -0.5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .announcement-meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            margin: 16px 0;
        }
        .announcement-meta {
            color: #555;
            font-size: 0.9em;
            margin-bottom: 4px;
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .announcement-meta i {
            color: #667eea;
            font-size: 1.1em;
        }
        .announcement-content {
            margin: 14px 0 0 0;
            color: #222;
            font-size: 1.05rem;
            font-family: 'Poppins', sans-serif;
        }
        .announcement-gallery {
            margin-top: 10px;
            margin-bottom: 6px;
        }
        .announcement-gallery img {
            margin-right: 6px;
            margin-bottom: 6px;
            border: 1.5px solid #e3eafc;
            border-radius: 8px;
            width: 80px;
            height: 60px;
            object-fit: cover;
            box-shadow: 0 2px 8px rgba(102,126,234,0.07);
            transition: box-shadow 0.2s;
        }
        .announcement-gallery img:hover {
            box-shadow: 0 4px 16px rgba(102,126,234,0.18);
        }
        .event-category-badge {
            background-color: #667eea;
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .announcement-list-title {
            font-size: 2.1rem;
            font-weight: 900;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
            text-align: center;
            letter-spacing: -1px;
            line-height: 1.1;
            font-family: 'Poppins', sans-serif;
        }
        .announcement-card .btn {
            display: inline-block;
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 0.98rem;
            font-weight: 600;
            text-decoration: none;
            margin-right: 8px;
            margin-top: 8px;
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px rgba(102,126,234,0.07);
            font-family: 'Poppins', sans-serif;
        }
        .announcement-card .btn-warning {
            background: linear-gradient(135deg, #fbc531 0%, #f5d06f 100%);
            color: #222;
            border: none;
        }
        .announcement-card .btn-warning:hover {
            background: linear-gradient(135deg, #f7b731 0%, #fbc531 100%);
            color: #fff;
        }
        .announcement-card .btn-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color: #fff;
            border: none;
        }
        .announcement-card .btn-danger:hover {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: #fff;
        }
        /* Add Announcement Button */
        .add-announcement-btn {
            position: fixed;
            bottom: 36px;
            right: 36px;
            z-index: 1001;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 64px;
            height: 64px;
            font-size: 2.2rem;
            box-shadow: 0 8px 32px rgba(102,126,234,0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s, transform 0.2s;
        }
        .add-announcement-btn:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            transform: scale(1.07);
        }
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0; top: 0; width: 100vw; height: 100vh;
            background: rgba(30, 41, 59, 0.55);
            align-items: center;
            justify-content: center;
        }
        .modal.active { display: flex; }
        .modal-card {
            background: #fff;
            border-radius: 18px;
            max-width: 480px;
            width: 95vw;
            margin: auto;
            padding: 40px 32px 32px 32px;
            position: relative;
            box-shadow: 0 25px 50px rgba(0,0,0,0.18), 0 10px 20px rgba(0,0,0,0.08);
            border: 1px solid rgba(255,255,255,0.2);
            animation: popIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            max-height: 90vh;
            overflow-y: auto;
        }
        .modal h2 {
            margin-bottom: 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 1.8rem;
            font-weight: 700;
            text-align: center;
            letter-spacing: -0.5px;
        }
        .modal .close-x {
            position: absolute;
            top: 20px; right: 24px;
            font-size: 1.8rem;
            color: #a0aec0;
            background: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 2;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal .close-x:hover {
            color: #e74c3c;
            background: rgba(231, 76, 60, 0.1);
            transform: rotate(90deg);
        }
        .modal label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
            display: block;
            font-size: 0.95rem;
            letter-spacing: 0.3px;
        }
        .modal input, .modal select, .modal textarea {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid rgba(102, 126, 234, 0.2);
            border-radius: 12px;
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.8);
            color: #2d3748;
            outline: none;
            margin-bottom: 18px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        .modal input:focus, .modal select:focus, .modal textarea:focus {
            border-color: #667eea;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }
        .modal .submit-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 16px 0;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            margin-bottom: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
        }
        .modal .submit-btn:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
        }
        .modal .cancel-btn {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color: #fff;
            border: none;
            padding: 16px 0;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            margin-bottom: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
        }
        .modal .cancel-btn:hover {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(255, 107, 107, 0.4);
        }
        .modal .img-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 12px;
        }
        .modal .img-preview img {
            width: 70px;
            height: 55px;
            object-fit: cover;
            border-radius: 8px;
            border: 1.5px solid #e3eafc;
        }
        .announcement-search-bar {
          width: 100%;
          max-width: 420px;
          margin: 0 auto 24px auto;
          display: flex;
          align-items: center;
          gap: 10px;
        }
        .announcement-search-bar input {
          flex: 1 1 0;
          padding: 12px 16px;
          border-radius: 10px;
          border: 1.5px solid #e3eaf1;
          font-size: 1.05rem;
          background: #f8f8ff;
          color: #333;
          transition: border-color 0.2s;
        }
        .announcement-search-bar input:focus {
          border-color: #667eea;
          outline: none;
        }
        @media (max-width: 900px) {
            .content { padding: 10px 2vw; }
            .announcement-list-container { max-width: 100vw; }
            .announcement-card { max-width: 98vw; }
        }
        @media (max-width: 600px) {
            .modal-card { padding: 18px 6px 18px 6px; max-height: 98vh; }
            .announcement-card { padding: 16px 6px 12px 6px; }
        }
    </style>
</head>
<body>
<?php include 'admin_sidebar.php'; ?>
<!-- Add Announcement Button -->
<button class="add-announcement-btn" id="addAnnouncementBtn" title="Add Announcement"><i class="fa fa-plus"></i></button>
<!-- Add Announcement Modal -->
<div class="modal" id="addAnnouncementModal">
  <div class="modal-card">
    <button class="close-x" id="closeAddModalX" title="Close">&times;</button>
    <h2>Add Announcement</h2>
    <form id="addAnnouncementForm" enctype="multipart/form-data">
      <label>Title</label>
      <input type="text" name="title" required placeholder="Enter title">
      <label>Date</label>
      <input type="date" name="date">
      <label>Time</label>
      <input type="time" name="time">
      <label>Location</label>
      <input type="text" name="location" placeholder="Enter location">
      <label>Speakers (comma separated)</label>
      <input type="text" name="speakers" placeholder="e.g. John Doe, Jane Smith">
      <label>Category</label>
      <select name="category" required>
        <option value="General">General</option>
        <option value="Event">Event</option>
        <option value="Holiday">Holiday</option>
        <option value="Exam">Exam</option>
        <option value="News">News</option>
        <option value="Other">Other</option>
      </select>
      <label>Content</label>
      <textarea name="content" rows="4" required placeholder="Enter announcement details"></textarea>
      <label>Gallery Images</label>
      <input type="file" name="gallery[]" id="galleryInput" accept="image/*" multiple>
      <div class="img-preview" id="galleryPreview"></div>
      <div id="addAnnouncementMsg"></div>
      <button type="submit" class="submit-btn"><i class="fa fa-plus"></i> Add Announcement</button>
      <button type="button" id="closeAddModal" class="cancel-btn"><i class="fa fa-times"></i> Cancel</button>
    </form>
  </div>
</div>

<!-- Edit Announcement Modal -->
<div class="modal" id="editAnnouncementModal">
  <div class="modal-card">
    <button class="close-x" id="closeEditModalX" title="Close">&times;</button>
    <h2>Edit Announcement</h2>
    <form id="editAnnouncementForm" enctype="multipart/form-data">
      <input type="hidden" name="id" id="editAnnouncementId">
      <label>Title</label>
      <input type="text" name="title" id="editTitle" required placeholder="Enter title">
      <label>Date</label>
      <input type="date" name="date" id="editDate">
      <label>Time</label>
      <input type="time" name="time" id="editTime">
      <label>Location</label>
      <input type="text" name="location" id="editLocation" placeholder="Enter location">
      <label>Speakers (comma separated)</label>
      <input type="text" name="speakers" id="editSpeakers" placeholder="e.g. John Doe, Jane Smith">
      <label>Category</label>
      <select name="category" id="editCategory" required>
        <option value="General">General</option>
        <option value="Event">Event</option>
        <option value="Holiday">Holiday</option>
        <option value="Exam">Exam</option>
        <option value="News">News</option>
        <option value="Other">Other</option>
      </select>
      <label>Content</label>
      <textarea name="content" id="editContent" rows="4" required placeholder="Enter announcement details"></textarea>
      
      <label>Current Images</label>
      <div class="img-preview" id="currentGalleryPreview"></div>
      
      <label>Upload New Images (optional, will be added to existing)</label>
      <input type="file" name="gallery[]" id="editGalleryInput" accept="image/*" multiple>
      <div class="img-preview" id="editGalleryPreview"></div>

      <div id="editAnnouncementMsg"></div>
      <button type="submit" class="submit-btn"><i class="fa fa-save"></i> Save Changes</button>
      <button type="button" id="closeEditModal" class="cancel-btn"><i class="fa fa-times"></i> Cancel</button>
    </form>
  </div>
</div>

<!-- Search/Filter Bar -->
<div class="announcement-search-bar">
  <input type="text" id="announcementSearchInput" placeholder="Search announcements by title, content, date...">
  <i class="fa fa-search" style="color:#667eea;font-size:1.2rem;"></i>
</div>
<div class="content">
<div class="announcement-list-container">
    <div class="announcement-list-title"><i class="fa-solid fa-bullhorn"></i> Announcements & Events</div>
    <div class="announcements-list">
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($a = $result->fetch_assoc()): ?>
            <?php
                $card_class = 'announcement-card';
                if (isset($a['category']) && $a['category'] === 'Event') {
                    $card_class .= ' category-event';
                }
            ?>
            <div class="<?php echo $card_class; ?>">
                <h3>
                    <?php echo htmlspecialchars($a['title']); ?>
                    <?php if (!empty($a['category'])): ?>
                        <span class="event-category-badge"><?php echo htmlspecialchars($a['category']); ?></span>
                    <?php endif; ?>
                </h3>
                
                <div class="announcement-meta-grid">
                    <?php if (!empty($a['date'])): ?>
                        <div class="announcement-meta"><i class="fa fa-calendar-alt"></i> <?php echo htmlspecialchars(date('d M Y', strtotime($a['date']))); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($a['time'])): ?>
                        <div class="announcement-meta"><i class="fa fa-clock"></i> <?php echo htmlspecialchars(date('h:i A', strtotime($a['time']))); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($a['location'])): ?>
                        <div class="announcement-meta"><i class="fa fa-map-marker-alt"></i> <?php echo htmlspecialchars($a['location']); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($a['speakers'])): ?>
                        <?php 
                            $speakers_list = json_decode($a['speakers'], true);
                            $speakers_display = is_array($speakers_list) ? implode(', ', $speakers_list) : '';
                        ?>
                        <?php if(!empty($speakers_display)): ?>
                        <div class="announcement-meta"><i class="fa fa-users"></i> <?php echo htmlspecialchars($speakers_display); ?></div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="announcement-content"><?php echo nl2br(htmlspecialchars($a['content'])); ?></div>
                
                <?php if (!empty($a['gallery'])): ?>
                    <div class="announcement-gallery">
                        <?php 
                        $gallery = json_decode($a['gallery'], true);
                        if (is_array($gallery)) {
                            foreach ($gallery as $img) {
                                echo '<img src="' . htmlspecialchars($img) . '" alt="Gallery image">';
                            }
                        }
                        ?>
                    </div>
                <?php endif; ?>
                <div style="margin-top: 12px;">
                    <button type="button" class="btn btn-sm btn-warning btn-edit-announcement" data-id="<?php echo $a['id']; ?>"><i class="fa fa-edit"></i> Edit</button>
                    <a href="delete_announcement.php?id=<?php echo $a['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this announcement?');" style="margin-left:8px;"><i class="fa fa-trash"></i> Delete</a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="announcement-card">No announcements or events found.</div>
    <?php endif; ?>
    </div>
</div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal open/close logic
    const addAnnouncementModal = document.getElementById('addAnnouncementModal');
    const addAnnouncementBtn = document.getElementById('addAnnouncementBtn');
    const closeAddModalX = document.getElementById('closeAddModalX');
    const closeAddModal = document.getElementById('closeAddModal');

    if (addAnnouncementBtn) {
        addAnnouncementBtn.onclick = () => {
            addAnnouncementModal.classList.add('active');
            addAnnouncementModal.style.display = 'flex';
        };
    }
    if (closeAddModalX) {
        closeAddModalX.onclick = () => {
            addAnnouncementModal.classList.remove('active');
            addAnnouncementModal.style.display = 'none';
        };
    }
    if (closeAddModal) {
        closeAddModal.onclick = () => {
            addAnnouncementModal.classList.remove('active');
            addAnnouncementModal.style.display = 'none';
        };
    }

    // Edit Modal Logic
    const editAnnouncementModal = document.getElementById('editAnnouncementModal');
    if (editAnnouncementModal) {
        const closeEditModalX = document.getElementById('closeEditModalX');
        const closeEditModal = document.getElementById('closeEditModal');
        const editAnnouncementForm = document.getElementById('editAnnouncementForm');
        const editAnnouncementMsg = document.getElementById('editAnnouncementMsg');

        document.querySelectorAll('.btn-edit-announcement').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                fetch(`get_announcement.php?id=${id}`)
                    .then(res => {
                        if (!res.ok) throw new Error(`Network response was not ok: ${res.statusText}`);
                        return res.json();
                    })
                    .then(res => {
                        if (res.success) {
                            const data = res.data;
                            document.getElementById('editAnnouncementId').value = data.id;
                            document.getElementById('editTitle').value = data.title;
                            document.getElementById('editDate').value = data.date;
                            document.getElementById('editTime').value = data.time;
                            document.getElementById('editLocation').value = data.location;
                            document.getElementById('editSpeakers').value = data.speakers;
                            document.getElementById('editCategory').value = data.category;
                            document.getElementById('editContent').value = data.content;
                            
                            const currentGallery = document.getElementById('currentGalleryPreview');
                            currentGallery.innerHTML = '';
                            if(data.gallery && data.gallery.length > 0){
                                data.gallery.forEach(imgUrl => {
                                    const imgContainer = document.createElement('div');
                                    imgContainer.style.position = 'relative';
                                    imgContainer.style.display = 'inline-block';
                                    imgContainer.style.marginRight = '10px';
                                    const img = document.createElement('img');
                                    img.src = imgUrl;
                                    const delBtn = document.createElement('button');
                                    delBtn.innerHTML = '&times;';
                                    delBtn.type = 'button';
                                    delBtn.setAttribute('style', 'position:absolute; top:0; right:0; background:rgba(255,0,0,0.7); color:white; border:none; cursor:pointer; width:20px; height:20px; line-height:20px; text-align:center; border-radius:50%;');
                                    delBtn.onclick = function() {
                                        imgContainer.remove();
                                        const input = document.createElement('input');
                                        input.type = 'hidden';
                                        input.name = 'delete_images[]';
                                        input.value = imgUrl;
                                        editAnnouncementForm.appendChild(input);
                                    };
                                    imgContainer.appendChild(img);
                                    imgContainer.appendChild(delBtn);
                                    currentGallery.appendChild(imgContainer);
                                });
                            }

                            document.getElementById('editGalleryPreview').innerHTML = '';
                            document.getElementById('editGalleryInput').value = '';
                            editAnnouncementMsg.textContent = '';
                            editAnnouncementModal.classList.add('active');
                            editAnnouncementModal.style.display = 'flex';
                        } else {
                            alert(res.error || 'Failed to fetch announcement details.');
                        }
                    })
                    .catch(err => {
                        console.error('Fetch Error:', err);
                        alert('A network error occurred. Please check the console for details.');
                    });
            });
        });

        if (closeEditModalX) closeEditModalX.onclick = () => {
            editAnnouncementModal.classList.remove('active');
            editAnnouncementModal.style.display = 'none';
        };
        if (closeEditModal) closeEditModal.onclick = () => {
            editAnnouncementModal.classList.remove('active');
            editAnnouncementModal.style.display = 'none';
        };
        
        const editGalleryInput = document.getElementById('editGalleryInput');
        const editGalleryPreview = document.getElementById('editGalleryPreview');
        if (editGalleryInput) {
            editGalleryInput.onchange = function() {
                editGalleryPreview.innerHTML = '';
                Array.from(this.files).forEach(file => {
                    if (!file.type.startsWith('image/')) return;
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        editGalleryPreview.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                });
            };
        }

        if (editAnnouncementForm) {
            editAnnouncementForm.onsubmit = function(e) {
                e.preventDefault();
                const formData = new FormData(editAnnouncementForm);
                fetch('edit_announcement_ajax.php', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success){
                            editAnnouncementMsg.textContent = "Updated successfully! Reloading...";
                            editAnnouncementMsg.style.color = 'green';
                            setTimeout(() => window.location.reload(), 1200);
                        } else {
                            editAnnouncementMsg.textContent = data.error || "Update failed.";
                            editAnnouncementMsg.style.color = 'red';
                        }
                    })
                    .catch(err => {
                        console.error('Submit Error:', err);
                        editAnnouncementMsg.textContent = "A network error occurred.";
                        editAnnouncementMsg.style.color = 'red';
                    });
            };
        }
    }

    // Close modals on background click
    window.onclick = function(event) {
        if (event.target === addAnnouncementModal) {
            addAnnouncementModal.classList.remove('active');
            addAnnouncementModal.style.display = 'none';
        }
        if (event.target === editAnnouncementModal) {
            editAnnouncementModal.classList.remove('active');
            editAnnouncementModal.style.display = 'none';
        }
    };
    
    // Image preview for Add Modal
    const galleryInput = document.getElementById('galleryInput');
    const galleryPreview = document.getElementById('galleryPreview');
    if(galleryInput) {
        galleryInput.onchange = function() {
            galleryPreview.innerHTML = '';
            Array.from(this.files).forEach(file => {
                if (!file.type.startsWith('image/')) return;
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    galleryPreview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        };
    }

    // Add form submission
    const addAnnouncementForm = document.getElementById('addAnnouncementForm');
    const addAnnouncementMsg = document.getElementById('addAnnouncementMsg');
    if(addAnnouncementForm) {
        addAnnouncementForm.onsubmit = function(e) {
            e.preventDefault();
            addAnnouncementMsg.textContent = '';
            const submitBtn = addAnnouncementForm.querySelector('.submit-btn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Adding...';
            const formData = new FormData(addAnnouncementForm);
            fetch('add_announcement_ajax.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        addAnnouncementMsg.textContent = 'Announcement added! Reloading...';
                        addAnnouncementMsg.style.color = '#27ae60';
                        setTimeout(() => window.location.reload(), 1200);
                    } else {
                        addAnnouncementMsg.textContent = data.error || 'Failed to add announcement.';
                        addAnnouncementMsg.style.color = '#e74c3c';
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Add Announcement';
                    }
                })
                .catch((err) => {
                    console.error('Submit Error:', err);
                    addAnnouncementMsg.textContent = 'Network error.';
                    addAnnouncementMsg.style.color = '#e74c3c';
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Add Announcement';
                });
        };
    }

    // Search Logic
    const searchInput = document.getElementById('announcementSearchInput');
    if(searchInput) {
        searchInput.addEventListener('input', function() {
            const val = this.value.toLowerCase();
            document.querySelectorAll('.announcement-card').forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(val) ? '' : 'none';
            });
        });
    }
});
</script>
</body>
</html> 