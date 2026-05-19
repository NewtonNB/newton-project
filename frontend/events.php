<?php
require_once '../shared/config.php';
// Pagination logic
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 6;
$offset = ($page - 1) * $perPage;
$total = $conn->query("SELECT COUNT(*) FROM announcements")->fetch_row()[0];
$totalPages = ceil($total / $perPage);
$sql = "SELECT * FROM announcements ORDER BY date DESC, created_at DESC LIMIT $perPage OFFSET $offset";
$result = $conn->query($sql);
// Fetch registration counts for all events
$counts = [];
$res = $conn->query("SELECT event_id, COUNT(*) as cnt FROM event_registrations GROUP BY event_id");
if ($res) {
  while ($row = $res->fetch_assoc()) {
    $counts[$row['event_id']] = $row['cnt'];
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Section</title>
    <!-- Preconnects for Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Poppins and Bungee Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Bungee&family=Poppins:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3.2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 (modern) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="modern-footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
:root {
  --primary: #667eea;
  --secondary: #764ba2;
  --accent: #42a5f5;
  --card-bg: #fff;
  --card-radius: 22px;
  --card-shadow: 0 8px 32px rgba(102,126,234,0.13);
  --card-hover-shadow: 0 16px 40px rgba(102,126,234,0.22);
  --btn-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  --btn-hover-bg: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
  --btn-color: #fff;
  --spacing: 2.7rem;
  --font: 'Poppins', 'Segoe UI', Arial, sans-serif;
}
body {
  background: linear-gradient(120deg, #e3eafc 0%, #cfd8ff 100%);
  font-family: var(--font);
  margin: 0;
  padding: 0;
  min-height: 100vh;
}
.header {
  min-height: 54vh;
  width: 100%;
  background: linear-gradient(120deg, rgba(102,126,234,0.90) 0%, rgba(118,75,162,0.60) 100%), url('nyabzgallery/current.jpg') center/cover no-repeat;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: var(--spacing);
  box-shadow: 0 8px 32px rgba(102,126,234,0.10);
  position: relative;
  overflow: hidden;
}
.header::before {
  content: '';
  position: absolute;
  top: -80px; left: -80px;
  width: 220px; height: 220px;
  background: radial-gradient(circle, #cfd8ff 0%, transparent 80%);
  opacity: 0.25;
  z-index: 1;
}
.header::after {
  content: '';
  position: absolute;
  bottom: -60px; right: -60px;
  width: 180px; height: 180px;
  background: radial-gradient(circle, #42a5f5 0%, transparent 80%);
  opacity: 0.18;
  z-index: 1;
}
.text-box {
  text-align: center;
  padding: 120px 24px 50px 24px;
  color: #fff;
  position: relative;
  z-index: 2;
  max-width: 600px;
  margin: 0 auto;
}
.text-box h1 {
  font-family: 'Bungee', cursive;
  font-size: 3.5rem;
  color: #fff;
  margin-bottom: 18px;
  text-shadow: 0 4px 24px rgba(102,126,234,0.25);
  letter-spacing: 2px;
  animation: fadeInDown 1.1s cubic-bezier(.77,0,.18,1) both;
}
.text-box p {
  font-size: 1.25rem;
  color: #e3f2fd;
  max-width: 520px;
  margin: 0 auto;
  animation: fadeInUp 1.3s cubic-bezier(.77,0,.18,1) both;
  font-weight: 500;
  letter-spacing: 0.5px;
}
@keyframes fadeInDown {
  0% { opacity: 0; transform: translateY(-40px); }
  100% { opacity: 1; transform: translateY(0); }
}
@keyframes fadeInUp {
  0% { opacity: 0; transform: translateY(40px); }
  100% { opacity: 1; transform: translateY(0); }
}
.events-section {
  max-width: 96%;
  width: 96%;
  margin: 0 auto;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
  gap: var(--spacing);
  padding: 0 1.2rem;
  background: #fff;
  border-radius: 24px;
  box-shadow: var(--card-shadow);
}
.events-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 28px;
  width: 100%;
  margin: 0 auto;
}
.event-card {
  background: var(--card-bg);
  border-radius: var(--card-radius);
  box-shadow: var(--card-shadow);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  transition: box-shadow 0.35s, transform 0.35s, border 0.25s;
  opacity: 0;
  animation: fadeInCard 0.9s cubic-bezier(.77,0,.18,1) forwards;
  border: 1.5px solid #e3eafc;
  position: relative;
}
@keyframes fadeInCard {
  0% { opacity: 0; transform: translateY(40px) scale(0.97); }
  100% { opacity: 1; transform: translateY(0) scale(1); }
}
.event-card:hover {
  box-shadow: var(--card-hover-shadow);
  transform: translateY(-12px) scale(1.045);
  border: 1.5px solid var(--primary);
}
.event-card img {
  width: 100%;
  height: 210px;
  object-fit: cover;
  border-bottom: 4px solid var(--primary);
  transition: transform 0.35s cubic-bezier(.77,0,.18,1), border-bottom 0.25s;
}
.event-card:hover img {
  transform: scale(1.08);
  border-bottom: 4px solid var(--secondary);
}
.event-date {
  position: absolute;
  top: 18px;
  left: 18px;
  background: linear-gradient(135deg, var(--primary), var(--secondary));
  color: #fff;
  text-align: center;
  padding: 0.7rem 0.9rem 0.5rem 0.9rem;
  font-size: 1.1rem;
  border-radius: 50%;
  font-weight: 700;
  min-width: 62px;
  min-height: 62px;
  box-shadow: 0 4px 16px rgba(102,126,234,0.18);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  border: 3px solid #fff;
  z-index: 2;
  transition: box-shadow 0.25s, background 0.25s;
}
.event-card:hover .event-date {
  box-shadow: 0 8px 32px rgba(102,126,234,0.22);
  background: linear-gradient(135deg, var(--secondary), var(--primary));
}
.event-date .day {
  font-size: 1.5rem;
  font-weight: bold;
  color: #fff;
  display: block;
  line-height: 1.1;
}
.event-date .month {
  font-size: 0.85rem;
  color: #e3eafc;
  font-weight: 600;
  letter-spacing: 1px;
  margin-top: 2px;
}
.card-body {
  padding: 1.7rem 1.1rem 1.7rem 1.1rem;
  flex: 1 1 auto;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  justify-content: space-between;
  position: relative;
  z-index: 1;
}
.card-title {
  color: var(--primary);
  font-size: 1.3rem;
  margin-bottom: 1.1rem;
  font-weight: 700;
  text-align: left;
  letter-spacing: 1px;
}
.card-text {
  color: #444;
  font-size: 1.08rem;
  margin-bottom: 0.5rem;
  font-weight: 500;
}
.event-btn {
  margin-top: 1.2rem;
  background: var(--btn-bg);
  color: var(--btn-color);
  border: none;
  padding: 0.7rem 2.1rem;
  border-radius: 8px;
  font-size: 1.08rem;
  font-weight: 600;
  cursor: pointer;
  box-shadow: 0 2px 12px rgba(102,126,234,0.10);
  position: relative;
  overflow: hidden;
  transition: background 0.22s, box-shadow 0.22s, transform 0.18s;
}
.event-btn:hover {
  background: var(--btn-hover-bg);
  box-shadow: 0 6px 24px rgba(102,126,234,0.18);
  transform: scale(1.04);
}
.event-btn:active {
  background: var(--primary);
}
.event-meta {
  font-size: 0.98em;
  color: #1976d2;
}
.event-time {
  margin-right: 8px;
}
.event-location {
  color: #1976d2;
}
.event-speakers-preview {
  font-size: 0.97em;
  color: #333;
}
.event-speakers-preview i {
  color: #1976d2;
  margin-right: 4px;
}
.event-category {
  display: inline-block;
  background: linear-gradient(135deg, #ffd700 0%, #fbc531 100%);
  color: #222;
  font-size: 0.92rem;
  font-weight: 700;
  border-radius: 999px;
  padding: 6px 18px;
  margin-bottom: 10px;
  margin-right: 8px;
  letter-spacing: 0.7px;
  box-shadow: 0 2px 8px rgba(255,215,0,0.08);
  border: 1.5px solid #ffe066;
  text-transform: uppercase;
}
.event-empty-state {
  text-align: center;
  color: #888;
  font-size: 1.2rem;
  margin: 60px 0 40px 0;
  padding: 40px 0;
}
.events-pagination {
  display: flex;
  justify-content: center;
  gap: 8px;
  margin: 32px 0 0 0;
}
.events-pagination button {
  background: #fff;
  border: 1.5px solid #667eea;
  color: #667eea;
  border-radius: 8px;
  font-size: 1.05rem;
  font-weight: 600;
  padding: 6px 16px;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
}
.events-pagination button.active, .events-pagination button:hover {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #fff;
}
#eventRegisterModal .modal-card {
  background: linear-gradient(120deg, #fff 80%, #e3eafc 100%);
  border-radius: 28px;
  max-width: 420px;
  width: 95vw;
  margin: auto;
  padding: 44px 36px 36px 36px;
  position: relative;
  box-shadow: 0 25px 50px rgba(102,126,234,0.13), 0 10px 20px rgba(102,126,234,0.08);
  border: 1px solid rgba(102,126,234,0.10);
  animation: popIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
  max-height: 90vh;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  align-items: center;
}
#eventRegisterModal h2 {
  font-size: 1.7rem;
  font-weight: 800;
  color: #667eea;
  margin-bottom: 18px;
  letter-spacing: 1px;
  text-align: center;
}
#eventRegisterModal label {
  font-weight: 600;
  color: #764ba2;
  margin-bottom: 8px;
  display: block;
  font-size: 1.05rem;
  letter-spacing: 0.3px;
}
#eventRegisterModal input[type="text"],
#eventRegisterModal input[type="email"] {
  width: 100%;
  padding: 15px 20px;
  border: 2px solid #e3eafc;
  border-radius: 14px;
  font-size: 1.08rem;
  background: #f8f8ff;
  color: #2d3748;
  outline: none;
  margin-bottom: 20px;
  transition: border-color 0.2s, box-shadow 0.2s;
  font-weight: 500;
  box-shadow: 0 2px 8px rgba(102,126,234,0.07);
}
#eventRegisterModal input:focus {
  border-color: #667eea;
  background: #fff;
  box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.13);
  transform: translateY(-2px);
}
#eventRegisterModal .modal-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  padding: 16px 0;
  border-radius: 14px;
  font-size: 1.13rem;
  font-weight: 700;
  cursor: pointer;
  width: 100%;
  margin-bottom: 12px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 8px 25px rgba(102, 126, 234, 0.13);
  position: relative;
  overflow: hidden;
  letter-spacing: 0.5px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
}
#eventRegisterModal .modal-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}
#eventRegisterModal .modal-btn .spinner {
  width: 22px;
  height: 22px;
  border: 3px solid #fff;
  border-top: 3px solid #667eea;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  display: inline-block;
}
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
#eventRegisterMsg {
  font-weight: 600;
  margin-bottom: 10px;
  text-align: center;
  color: #e74c3c;
  min-height: 24px;
}
#eventRegisterMsg.success {
  color: #27ae60;
}
#eventRegisterModal .close-x {
  position: absolute;
  top: 20px; right: 24px;
  font-size: 2.1rem;
  color: #a0aec0;
  background: none;
  border: none;
  cursor: pointer;
  transition: color 0.2s, background 0.2s, transform 0.2s;
  z-index: 2;
  width: 44px;
  height: 44px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}
#eventRegisterModal .close-x:hover {
  color: #e74c3c;
  background: rgba(231, 76, 60, 0.13);
  transform: rotate(90deg) scale(1.08);
}
@media (max-width: 700px) {
  #eventRegisterModal .modal-card {
    padding: 18px 6px 18px 6px;
    max-width: 99vw;
  }
  #eventRegisterModal h2 {
    font-size: 1.3rem;
  }
}
#eventDetailsModal {
  display: none;
  position: fixed;
  z-index: 2000;
  left: 0; top: 0; width: 100vw; height: 100vh;
  background: rgba(30, 41, 59, 0.65);
  backdrop-filter: blur(2.5px);
  align-items: center;
  justify-content: center;
  animation: fadeInOverlay 0.4s cubic-bezier(.77,0,.18,1);
}
#eventDetailsModal.active {
  display: flex;
}
@keyframes fadeInOverlay {
  from { opacity: 0; }
  to { opacity: 1; }
}
#eventDetailsModal .modal-card {
  background: linear-gradient(120deg, #fff 80%, #e3eafc 100%);
  border-radius: 28px;
  max-width: 540px;
  width: 97vw;
  margin: auto;
  padding: 44px 36px 36px 36px;
  position: relative;
  box-shadow: 0 25px 50px rgba(102,126,234,0.18), 0 10px 20px rgba(102,126,234,0.10);
  border: 1px solid rgba(102,126,234,0.10);
  animation: popInModal 0.45s cubic-bezier(0.34, 1.56, 0.64, 1);
  max-height: 92vh;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  align-items: center;
}
@keyframes popInModal {
  from { opacity: 0; transform: scale(0.85) translateY(60px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}
#eventDetailsModal .close-x {
  position: absolute;
  top: 20px; right: 24px;
  font-size: 2.1rem;
  color: #a0aec0;
  background: none;
  border: none;
  cursor: pointer;
  transition: color 0.2s, background 0.2s, transform 0.2s;
  z-index: 2;
  width: 44px;
  height: 44px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}
#eventDetailsModal .close-x:hover {
  color: #e74c3c;
  background: rgba(231, 76, 60, 0.13);
  transform: rotate(90deg) scale(1.08);
}
#eventDetailsModal h2 {
  font-size: 2rem;
  font-weight: 800;
  color: #667eea;
  margin-bottom: 12px;
  letter-spacing: 1px;
  text-align: center;
}
#eventDetailsModal .modal-gallery {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  margin-bottom: 12px;
  justify-content: center;
}
#eventDetailsModal .modal-gallery img {
  width: 120px;
  height: 90px;
  object-fit: cover;
  border-radius: 12px;
  border: 2px solid #e3eafc;
  box-shadow: 0 2px 8px rgba(102,126,234,0.07);
  transition: transform 0.2s, box-shadow 0.2s;
  cursor: pointer;
}
#eventDetailsModal .modal-gallery img:hover {
  transform: scale(1.08);
  box-shadow: 0 6px 24px rgba(102,126,234,0.18);
}
#eventDetailsModal .modal-meta, #eventDetailsModal .modal-speakers {
  font-size: 1.08rem;
  color: #764ba2;
  margin-bottom: 8px;
  text-align: center;
  font-weight: 600;
}
#eventDetailsModal .modal-content {
  font-size: 1.13rem;
  color: #333;
  margin-bottom: 18px;
  text-align: center;
  font-weight: 500;
  line-height: 1.6;
}
#eventDetailsModal .modal-gallery-nav {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 18px;
  margin-bottom: 12px;
}
#eventDetailsModal .modal-gallery-nav-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #fff;
  border: none;
  border-radius: 50%;
  width: 38px;
  height: 38px;
  font-size: 1.3rem;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.2s, transform 0.2s;
  box-shadow: 0 2px 8px rgba(102,126,234,0.07);
}
#eventDetailsModal .modal-gallery-nav-btn:disabled {
  background: #e3eafc;
  color: #aaa;
  cursor: not-allowed;
}
#eventDetailsModal .modal-gallery-nav-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
  transform: scale(1.08);
}
#eventDetailsModal .modal-gallery-main {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 10px;
}
#eventDetailsModal .modal-gallery-main-img {
  width: 320px;
  height: 220px;
  object-fit: cover;
  border-radius: 16px;
  border: 2px solid #e3eafc;
  box-shadow: 0 4px 16px rgba(102,126,234,0.10);
  transition: box-shadow 0.2s, transform 0.2s;
  background: #f8f8ff;
}
@media (max-width: 700px) {
  #eventDetailsModal .modal-gallery-main-img {
    width: 98vw;
    height: 160px;
  }
}
#eventDetailsModal .modal-meta, #eventDetailsModal .modal-speakers {
  font-size: 1.08rem;
  color: #764ba2;
  margin-bottom: 8px;
  text-align: center;
  font-weight: 600;
}
#eventDetailsModal .modal-category-badge {
  display: inline-block;
  background: linear-gradient(135deg, #ffd700 0%, #fbc531 100%);
  color: #222;
  font-size: 0.92rem;
  font-weight: 700;
  border-radius: 999px;
  padding: 6px 18px;
  margin-bottom: 10px;
  margin-right: 8px;
  letter-spacing: 0.7px;
  box-shadow: 0 2px 8px rgba(255,215,0,0.08);
  border: 1.5px solid #ffe066;
  text-transform: uppercase;
}
#eventDetailsModal .modal-gallery-caption {
  text-align: center;
  color: #444;
  font-size: 1.08rem;
  font-weight: 500;
  margin: 8px 0 0 0;
  min-height: 24px;
  letter-spacing: 0.2px;
}
#eventDetailsModal .modal-moreinfo-toggle {
  display: block;
  background: none;
  border: none;
  color: #667eea;
  font-weight: 700;
  font-size: 1.08rem;
  margin: 10px auto 0 auto;
  cursor: pointer;
  text-align: center;
  transition: color 0.2s;
}
#eventDetailsModal .modal-moreinfo-toggle:hover {
  color: #764ba2;
}
#eventDetailsModal .modal-moreinfo-content {
  display: none;
  background: #f8f8ff;
  border-radius: 12px;
  padding: 16px 18px;
  margin: 10px 0 0 0;
  color: #333;
  font-size: 1.05rem;
  box-shadow: 0 2px 8px rgba(102,126,234,0.07);
  text-align: left;
}
#eventDetailsModal .modal-moreinfo-content.active {
  display: block;
  animation: fadeInUp 0.4s cubic-bezier(.77,0,.18,1);
}
.events-search-bar {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 18px;
  margin: 0 auto 32px auto;
  padding: 18px 24px;
  background: linear-gradient(120deg, #fff 80%, #e3eafc 100%);
  border-radius: 18px;
  box-shadow: 0 4px 18px rgba(102,126,234,0.10);
  max-width: 700px;
  width: 98%;
  position: relative;
  top: -18px;
  /* Fix for dropdown menu being clipped on large screens */
  overflow: visible !important;
  z-index: 1001;
}
.container {
  overflow: visible !important;
}
@media (min-width: 992px) {
  .events-search-bar {
    overflow: visible !important;
  }
  .container {
    overflow: visible !important;
  }
}
.events-search-bar select {
  padding: 13px 22px;
  border-radius: 12px;
  border: 2px solid #667eea;
  font-size: 1.08rem;
  background: #f8f8ff;
  color: #333;
  font-weight: 600;
  box-shadow: 0 2px 8px rgba(102,126,234,0.07);
  transition: border-color 0.2s, box-shadow 0.2s;
  outline: none;
}
.events-search-bar select:focus {
  border-color: #764ba2;
  box-shadow: 0 0 0 4px rgba(118,75,162,0.13);
}
.events-search-bar input[type="text"] {
  padding: 13px 22px;
  border-radius: 12px;
  border: 2px solid #667eea;
  font-size: 1.08rem;
  background: #f8f8ff;
  color: #333;
  font-weight: 500;
  box-shadow: 0 2px 8px rgba(102,126,234,0.07);
  transition: border-color 0.2s, box-shadow 0.2s;
  outline: none;
  width: 220px;
}
.events-search-bar input[type="text"]:focus {
  border-color: #764ba2;
  box-shadow: 0 0 0 4px rgba(118,75,162,0.13);
}
.events-search-bar .search-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #fff;
  border: none;
  border-radius: 10px;
  padding: 13px 28px;
  font-size: 1.08rem;
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 2px 12px rgba(102,126,234,0.10);
  transition: background 0.22s, box-shadow 0.22s, transform 0.18s;
  margin-left: 6px;
  display: flex;
  align-items: center;
  gap: 8px;
}
.events-search-bar .search-btn:hover {
  background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
  transform: scale(1.04);
  box-shadow: 0 6px 24px rgba(102,126,234,0.18);
}
.events-search-bar .fa-search {
  color: #fff;
  font-size: 1.2rem;
  margin-left: 0;
}
.custom-category-dropdown {
  position: relative;
  min-width: 190px;
  font-size: 1.08rem;
  font-family: inherit;
  z-index: 1002;
}
.custom-category-dropdown .dropdown-toggle {
  width: 100%;
  background: #f8f8ff;
  border: 2px solid #667eea;
  border-radius: 12px;
  padding: 13px 22px;
  color: #333;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(102,126,234,0.07);
  transition: border-color 0.2s, box-shadow 0.2s;
  outline: none;
  z-index: 1003;
}
.custom-category-dropdown.open .dropdown-toggle {
  border-color: #764ba2;
  box-shadow: 0 0 0 4px rgba(118,75,162,0.13);
}
.custom-category-dropdown .dropdown-menu {
  display: none;
  position: absolute;
  left: 0; right: 0;
  top: 110%;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 18px rgba(102,126,234,0.18);
  z-index: 1004;
  margin-top: 4px;
  padding: 6px 0;
  border: 2px solid #764ba2;
}
.custom-category-dropdown.open .dropdown-menu {
  display: block;
}
.custom-category-dropdown .dropdown-item {
  padding: 12px 22px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 500;
  color: #333;
  transition: background 0.18s, color 0.18s;
}
.custom-category-dropdown .dropdown-item:hover, .custom-category-dropdown .dropdown-item.selected {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #fff;
}
.custom-category-dropdown .dropdown-menu {
  /* Debug: strong background and border for visibility */
  background: #fffbe6 !important;
  border: 3px solid #e74c3c !important;
  color: #222;
}
</style>

</head>
<body>
<!-- Ensure hero section is always visible -->
<?php include 'navbar.php'; ?>
<!-- Enhanced Hero Section -->
<section class="header">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-10 text-center text-box" data-aos="fade-up" data-aos-duration="1000">
        <h1 style="font-family: Bungee; margin-bottom: 10px; font-size: 3.2rem; letter-spacing: 2px;">NEWS & EVENTS</h1>
        <p style="padding: 5px; margin-bottom: 40px; font-size: 1.35rem; color: #e3f2fd; font-weight: 500;">
          Stay up to date with the latest news, events, and celebrations at <span style="color:#ffd700;font-weight:700;">Nyabikoni Secondary School</span>.<br>
          <span style="font-size:1.1rem; color:#fff;">From academic milestones to vibrant community gatherings, discover what makes our school special.</span>
        </p>
        <a href="#events" class="event-btn" style="font-size:1.15rem; padding: 1rem 2.5rem; margin-top: 10px; box-shadow: 0 4px 24px rgba(102,126,234,0.18);">See Upcoming Events <i class="fa fa-arrow-down"></i></a>
      </div>
    </div>
  </div>
</section>
<!-- Featured Event (if any upcoming) -->
<?php
$featured = $conn->query("SELECT * FROM announcements WHERE date >= date('now') ORDER BY date ASC LIMIT 1");
if ($featured && $featured->num_rows > 0):
  $f = $featured->fetch_assoc();
  $gallery = !empty($f['gallery']) ? json_decode($f['gallery'], true) : [];
  $imgSrc = (is_array($gallery) && count($gallery) > 0) ? htmlspecialchars($gallery[0]) : 'nyabzgallery/default.png';
?>
<section class="featured-event" style="background: linear-gradient(120deg, #667eea 0%, #764ba2 100%); border-radius: 24px; box-shadow: 0 8px 32px rgba(102,126,234,0.13); margin: 0 auto 40px auto; max-width: 1100px; overflow: hidden; position: relative;">
  <div style="display: flex; flex-wrap: wrap; align-items: stretch;">
    <div style="flex:1 1 340px; min-width: 320px; background:#fff; display:flex; align-items:center; justify-content:center;">
      <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($f['title']); ?>" style="width:100%; max-width:420px; height:320px; object-fit:cover; border-radius: 0 0 24px 0; box-shadow: 0 4px 24px rgba(102,126,234,0.13);">
    </div>
    <div style="flex:2 1 400px; padding: 38px 36px 38px 36px; color:#fff; display:flex; flex-direction:column; justify-content:center;">
      <div style="font-size:1.1rem; margin-bottom:10px; opacity:0.85;"><i class="fa fa-star"></i> Featured Event</div>
      <h2 style="font-size:2.1rem; font-weight:800; margin-bottom:10px; letter-spacing:1px; color:#ffd700; text-shadow:0 2px 12px rgba(0,0,0,0.08);">
        <?php echo htmlspecialchars($f['title']); ?>
      </h2>
      <div style="font-size:1.08rem; margin-bottom:12px; color:#fff;">
        <?php if (!empty($f['date'])): ?>
          <span style="background:rgba(255,255,255,0.13); border-radius:8px; padding:6px 16px; margin-right:10px;"><i class="fa fa-calendar"></i> <?php echo date('l, d M Y', strtotime($f['date'])); ?></span>
        <?php endif; ?>
        <?php if (!empty($f['time'])): ?>
          <span style="background:rgba(255,255,255,0.13); border-radius:8px; padding:6px 16px; margin-right:10px;"><i class="fa fa-clock"></i> <?php echo htmlspecialchars($f['time']); ?></span>
        <?php endif; ?>
        <?php if (!empty($f['location'])): ?>
          <span style="background:rgba(255,255,255,0.13); border-radius:8px; padding:6px 16px;"><i class="fa fa-map-marker-alt"></i> <?php echo htmlspecialchars($f['location']); ?></span>
        <?php endif; ?>
      </div>
      <div style="font-size:1.13rem; color:#fff; margin-bottom:18px; opacity:0.95;">
        <?php echo nl2br(htmlspecialchars(mb_strimwidth($f['content'], 0, 180, '...'))); ?>
      </div>
      <a href="#events" class="event-btn" style="font-size:1.08rem; background:linear-gradient(135deg,#ffd700 0%,#fbc531 100%); color:#222; font-weight:700;">Learn More</a>
    </div>
  </div>
</section>
<?php endif; ?>
<!-- Main Events Section Anchor -->
<a id="events"></a>
<!-- Search/Filter Bar and toggles always visible -->
<div class="events-search-bar">
  <div class="custom-category-dropdown" id="customCategoryDropdown" tabindex="0">
    <button type="button" class="dropdown-toggle" id="categoryDropdownBtn">
      <i class="bi bi-ui-checks-grid"></i> <span id="categoryDropdownLabel">All Categories</span> <i class="bi bi-caret-down-fill"></i>
    </button>
    <div class="dropdown-menu" id="categoryDropdownMenu">
      <div class="dropdown-item" data-value=""><i class="bi bi-ui-checks-grid"></i> All Categories</div>
      <div class="dropdown-item" data-value="General"><i class="bi bi-megaphone"></i> General</div>
      <div class="dropdown-item" data-value="Event"><i class="bi bi-stars"></i> Event</div>
      <div class="dropdown-item" data-value="Holiday"><i class="bi bi-umbrella"></i> Holiday</div>
      <div class="dropdown-item" data-value="Exam"><i class="bi bi-journal-text"></i> Exam</div>
      <div class="dropdown-item" data-value="News"><i class="bi bi-newspaper"></i> News</div>
      <div class="dropdown-item" data-value="Other"><i class="bi bi-bookmark"></i> Other</div>
    </div>
    <input type="hidden" id="eventsCategoryFilter" value="">
  </div>
  <input type="text" id="eventsSearchInput" placeholder="Search events by title, content, date...">
  <button class="search-btn" type="button" id="eventsSearchBtn"><i class="fa fa-search"></i> Search</button>
</div>
<!-- Events Section (default: visible) -->
<section class="events-section container py-4" style="display: flex;">
  <div class="events-grid">
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($a = $result->fetch_assoc()): ?>
        <div class="event-card animate-fade-slide">
          <div class="card h-100 shadow-sm event-pro" tabindex="0" aria-label="Event: <?php echo htmlspecialchars($a['title']); ?>">
            <?php if (!empty($a['date'])): 
              $dateObj = new DateTime($a['date']);
              $day = $dateObj->format('d');
              $month = strtoupper($dateObj->format('M Y'));
            ?>
              <div class="event-date"><span class="day"><?php echo $day; ?></span><span class="month"><?php echo $month; ?></span></div>
            <?php endif; ?>
            <?php 
            $gallery = !empty($a['gallery']) ? json_decode($a['gallery'], true) : [];
            $imgSrc = (is_array($gallery) && count($gallery) > 0) ? htmlspecialchars($gallery[0]) : 'nyabzgallery/default.png';
            ?>
            <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($a['title']); ?>" loading="lazy">
            <div class="card-body">
                <?php if (!empty($a['category'])): ?>
                  <span class="event-category"><?php echo htmlspecialchars($a['category']); ?></span>
                <?php endif; ?>
                <h5 class="card-title mb-0"><?php echo htmlspecialchars($a['title']); ?></h5>
                <div class="event-meta mb-2">
                  <?php if (!empty($a['time'])): ?>
                    <span class="event-time"><i class="fa fa-clock"></i> <?php echo htmlspecialchars($a['time']); ?></span><br>
                  <?php endif; ?>
                  <?php if (!empty($a['location'])): ?>
                    <span class="event-location"><i class="fa fa-map-marker-alt"></i> <?php echo htmlspecialchars($a['location']); ?></span>
                  <?php endif; ?>
                </div>
                <p class="card-text"><?php echo nl2br(htmlspecialchars($a['content'])); ?></p>
                <?php if (!empty($a['speakers'])): ?>
                  <div class="event-speakers-preview mb-2"><i class="fa fa-user"></i> <?php echo htmlspecialchars(str_replace(["[", "]", '"'], '', $a['speakers'])); ?></div>
                <?php endif; ?>
                <?php if (is_array($gallery) && count($gallery) > 1): ?>
                  <div class="event-gallery mt-2">
                    <?php foreach (array_slice($gallery, 1) as $img): ?>
                      <img src="<?php echo htmlspecialchars($img); ?>" alt="Gallery image" style="width:48px;height:36px;border-radius:6px;margin-right:4px;object-fit:cover;" loading="lazy">
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
                <div style="margin-bottom:8px;">
                  <span class="badge bg-success" style="font-size:0.98rem;"> <?php echo isset($counts[$a['id']]) ? $counts[$a['id']] : 0; ?> Registered</span>
                </div>
                <a class="event-btn register-btn" href="#" onclick="openRegisterModal('<?php echo $a['id']; ?>'); return false;" style="margin-top:10px; background:linear-gradient(135deg,#42a5f5 0%,#667eea 100%); font-weight:700;">Register</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="event-empty-state col-12">No events or announcements found.<br><i class="fa fa-calendar fa-2x mt-3" style="color:#667eea;"></i></div>
    <?php endif; ?>
  </div>
  <?php if ($totalPages > 1): ?>
  <div class="events-pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <form method="get" style="display:inline;">
        <button type="submit" name="page" value="<?php echo $i; ?>" class="<?php if ($i == $page) echo 'active'; ?>" aria-label="Go to page <?php echo $i; ?>"><?php echo $i; ?></button>
      </form>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
</section>
<!-- Event Details Modal -->
<div id="eventDetailsModal" tabindex="-1" aria-modal="true" role="dialog">
  <div class="modal-card">
    <button class="close-x" id="closeEventModalX" title="Close" aria-label="Close">&times;</button>
    <h2 id="eventModalTitle"></h2>
    <div id="eventModalMeta" style="color:#1976d2;font-size:1.05rem;margin-bottom:8px;"></div>
    <div id="eventModalSpeakers" style="margin-bottom:8px;"></div>
    <div id="eventModalContent" style="margin-bottom:12px;"></div>
    <div class="modal-gallery" id="eventModalGallery"></div>
  </div>
</div>
<!-- Event Registration Modal -->
<div id="eventRegisterModal" aria-modal="true" role="dialog" tabindex="-1">
  <div class="modal-card">
    <button class="close-x" id="closeRegisterModalX" title="Close" aria-label="Close">&times;</button>
    <h2>Event Registration</h2>
    <form id="eventRegisterForm" autocomplete="off" novalidate>
      <input type="hidden" name="event_id" id="registerEventId">
      <label for="registerName">Name</label>
      <input type="text" name="name" id="registerName" required placeholder="Your Name" aria-required="true">
      <label for="registerEmail">Email</label>
      <input type="email" name="email" id="registerEmail" required placeholder="Your Email" aria-required="true">
      <label for="registerPhone">Phone</label>
      <input type="text" name="phone" id="registerPhone" required placeholder="Your Phone Number" aria-required="true">
      <!-- Honeypot for spam prevention -->
      <input type="text" name="website" id="registerWebsite" style="display:none;" tabindex="-1" autocomplete="off">
      <div id="eventRegisterMsg"></div>
      <button type="submit" class="modal-btn" id="eventRegisterSubmitBtn"><span>Register</span></button>
    </form>
  </div>
</div>
<script>
// Search/filter logic
const eventsSearchInput = document.getElementById('eventsSearchInput');
const eventsCategoryFilter = document.getElementById('eventsCategoryFilter');
function filterEvents() {
  const val = eventsSearchInput.value.toLowerCase();
  const cat = eventsCategoryFilter.value;
  document.querySelectorAll('.event-card').forEach(card => {
    const text = card.textContent.toLowerCase();
    const badge = card.querySelector('.event-category');
    const matchesCat = !cat || (badge && badge.textContent.trim().toLowerCase().includes(cat.toLowerCase()));
    card.style.display = (text.includes(val) && matchesCat) ? '' : 'none';
  });
}
// Custom dropdown logic
const customDropdown = document.getElementById('customCategoryDropdown');
const dropdownBtn = document.getElementById('categoryDropdownBtn');
const dropdownMenu = document.getElementById('categoryDropdownMenu');
const dropdownLabel = document.getElementById('categoryDropdownLabel');
const dropdownInput = document.getElementById('eventsCategoryFilter');
let dropdownOpen = false;
function closeDropdown() {
  customDropdown.classList.remove('open');
  dropdownOpen = false;
}
dropdownBtn.onclick = function(e) {
  console.log('Dropdown button clicked (desktop test)');
  e.stopPropagation();
  dropdownOpen = !dropdownOpen;
  if (dropdownOpen) {
    customDropdown.classList.add('open');
    setTimeout(() => {
      dropdownMenu.querySelector('.dropdown-item.selected')?.scrollIntoView({block:'nearest'});
    }, 10);
  } else {
    closeDropdown();
  }
};
dropdownMenu.onclick = function(e) {
  console.log('Dropdown menu clicked, stopPropagation');
  e.stopPropagation();
};
dropdownMenu.querySelectorAll('.dropdown-item').forEach(item => {
  item.onclick = function(e) {
    e.stopPropagation();
    dropdownMenu.querySelectorAll('.dropdown-item').forEach(i => i.classList.remove('selected'));
    this.classList.add('selected');
    dropdownLabel.innerHTML = this.innerHTML;
    dropdownInput.value = this.dataset.value;
    closeDropdown();
    filterEvents();
  };
});
document.addEventListener('click', function(e) {
  if (!customDropdown.contains(e.target)) closeDropdown();
});
customDropdown.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeDropdown();
});
// Make search button trigger filter
const eventsSearchBtn = document.getElementById('eventsSearchBtn');
eventsSearchBtn.addEventListener('click', filterEvents);
// Also allow Enter key in search input to trigger filter
 eventsSearchInput.addEventListener('keydown', function(e) {
   if (e.key === 'Enter') {
     filterEvents();
   }
 });
// Enhanced modal: auto-play, captions, more info
const eventDetailsModal = document.getElementById('eventDetailsModal');
const closeEventModalX = document.getElementById('closeEventModalX');
const eventModalTitle = document.getElementById('eventModalTitle');
const eventModalMeta = document.getElementById('eventModalMeta');
const eventModalSpeakers = document.getElementById('eventModalSpeakers');
const eventModalContent = document.getElementById('eventModalContent');
const eventModalGallery = document.getElementById('eventModalGallery');
let lastFocusedElement = null;
let galleryImgs = [];
let galleryIdx = 0;
let galleryCaptions = [];
let galleryAutoPlayTimer = null;
let galleryPaused = false;
let moreInfoHtml = '';
function openEventModal(title, meta, speakers, content, imgs, category, date, time, location, captions = [], moreInfo = '') {
  eventModalTitle.textContent = title;
  // Show category badge, date, time, location
  let metaHtml = '';
  if (category) metaHtml += `<span class='modal-category-badge'>${category}</span>`;
  if (date) metaHtml += `<span style='margin-right:10px;'><i class='fa fa-calendar'></i> ${date}</span>`;
  if (time) metaHtml += `<span style='margin-right:10px;'><i class='fa fa-clock'></i> ${time}</span>`;
  if (location) metaHtml += `<span><i class='fa fa-map-marker-alt'></i> ${location}</span>`;
  eventModalMeta.innerHTML = metaHtml;
  eventModalSpeakers.innerHTML = speakers ? '<i class="fa fa-user"></i> ' + speakers : '';
  eventModalContent.innerHTML = content;
  // Gallery navigation
  galleryImgs = imgs;
  galleryIdx = 0;
  galleryCaptions = captions.length === imgs.length ? captions : imgs.map(() => title);
  moreInfoHtml = moreInfo;
  renderGallery();
  renderMoreInfo();
  lastFocusedElement = document.activeElement;
  eventDetailsModal.classList.add('active');
  eventDetailsModal.style.display = 'flex';
  eventDetailsModal.focus();
  setTimeout(() => { closeEventModalX.focus(); }, 100);
  startGalleryAutoPlay();
}
function renderGallery() {
  eventModalGallery.innerHTML = '';
  if (galleryImgs.length === 0) return;
  // Main image with nav
  const nav = document.createElement('div');
  nav.className = 'modal-gallery-nav';
  const prevBtn = document.createElement('button');
  prevBtn.className = 'modal-gallery-nav-btn';
  prevBtn.innerHTML = '<i class="fa fa-chevron-left"></i>';
  prevBtn.disabled = galleryIdx === 0;
  prevBtn.onclick = (e) => { e.stopPropagation(); galleryIdx--; renderGallery(); resetGalleryAutoPlay(); };
  const nextBtn = document.createElement('button');
  nextBtn.className = 'modal-gallery-nav-btn';
  nextBtn.innerHTML = '<i class="fa fa-chevron-right"></i>';
  nextBtn.disabled = galleryIdx === galleryImgs.length-1;
  nextBtn.onclick = (e) => { e.stopPropagation(); galleryIdx++; renderGallery(); resetGalleryAutoPlay(); };
  nav.appendChild(prevBtn);
  // Main image
  const mainImg = document.createElement('img');
  mainImg.src = galleryImgs[galleryIdx];
  mainImg.className = 'modal-gallery-main-img';
  mainImg.alt = 'Event image ' + (galleryIdx+1);
  mainImg.tabIndex = 0;
  mainImg.onmouseenter = mainImg.onfocus = () => { galleryPaused = true; };
  mainImg.onmouseleave = mainImg.onblur = () => { galleryPaused = false; };
  mainImg.onclick = () => {
    // Show as large preview
    const overlay = document.createElement('div');
    overlay.style.position = 'fixed';
    overlay.style.left = 0;
    overlay.style.top = 0;
    overlay.style.width = '100vw';
    overlay.style.height = '100vh';
    overlay.style.background = 'rgba(30,41,59,0.85)';
    overlay.style.display = 'flex';
    overlay.style.alignItems = 'center';
    overlay.style.justifyContent = 'center';
    overlay.style.zIndex = 3000;
    overlay.onclick = () => document.body.removeChild(overlay);
    const bigImg = document.createElement('img');
    bigImg.src = galleryImgs[galleryIdx];
    bigImg.style.maxWidth = '90vw';
    bigImg.style.maxHeight = '85vh';
    bigImg.style.borderRadius = '18px';
    bigImg.style.boxShadow = '0 8px 32px rgba(102,126,234,0.22)';
    overlay.appendChild(bigImg);
    document.body.appendChild(overlay);
  };
  nav.appendChild(mainImg);
  nav.appendChild(nextBtn);
  eventModalGallery.appendChild(nav);
  // Caption
  const caption = document.createElement('div');
  caption.className = 'modal-gallery-caption';
  caption.textContent = galleryCaptions[galleryIdx] || '';
  eventModalGallery.appendChild(caption);
  // Thumbnails
  const thumbs = document.createElement('div');
  thumbs.style.display = 'flex';
  thumbs.style.justifyContent = 'center';
  thumbs.style.gap = '8px';
  thumbs.style.marginTop = '10px';
  galleryImgs.forEach((src, idx) => {
    const thumb = document.createElement('img');
    thumb.src = src;
    thumb.style.width = '48px';
    thumb.style.height = '36px';
    thumb.style.objectFit = 'cover';
    thumb.style.borderRadius = '8px';
    thumb.style.border = idx === galleryIdx ? '2px solid #667eea' : '2px solid #e3eafc';
    thumb.style.cursor = 'pointer';
    thumb.onclick = (e) => { e.stopPropagation(); galleryIdx = idx; renderGallery(); resetGalleryAutoPlay(); };
    thumbs.appendChild(thumb);
  });
  eventModalGallery.appendChild(thumbs);
}
function startGalleryAutoPlay() {
  clearInterval(galleryAutoPlayTimer);
  galleryAutoPlayTimer = setInterval(() => {
    if (!galleryPaused && galleryImgs.length > 1) {
      galleryIdx = (galleryIdx + 1) % galleryImgs.length;
      renderGallery();
    }
  }, 3500);
}
function resetGalleryAutoPlay() {
  clearInterval(galleryAutoPlayTimer);
  startGalleryAutoPlay();
}
// More Info section
function renderMoreInfo() {
  let moreInfoToggle = document.getElementById('modalMoreInfoToggle');
  let moreInfoContent = document.getElementById('modalMoreInfoContent');
  if (!moreInfoToggle) {
    moreInfoToggle = document.createElement('button');
    moreInfoToggle.id = 'modalMoreInfoToggle';
    moreInfoToggle.className = 'modal-moreinfo-toggle';
    moreInfoToggle.type = 'button';
    moreInfoToggle.innerHTML = '<i class="fa fa-info-circle"></i> More Info';
    eventModalContent.parentNode.insertBefore(moreInfoToggle, eventModalGallery.nextSibling);
  }
  if (!moreInfoContent) {
    moreInfoContent = document.createElement('div');
    moreInfoContent.id = 'modalMoreInfoContent';
    moreInfoContent.className = 'modal-moreinfo-content';
    eventModalContent.parentNode.insertBefore(moreInfoContent, moreInfoToggle.nextSibling);
  }
  moreInfoContent.innerHTML = moreInfoHtml || 'No additional information.';
  moreInfoContent.classList.remove('active');
  moreInfoToggle.onclick = function() {
    moreInfoContent.classList.toggle('active');
    if (moreInfoContent.classList.contains('active')) {
      moreInfoToggle.innerHTML = '<i class="fa fa-info-circle"></i> Hide Info';
    } else {
      moreInfoToggle.innerHTML = '<i class="fa fa-info-circle"></i> More Info';
    }
  };
}
document.querySelectorAll('.event-card .card').forEach(card => {
  card.style.cursor = 'pointer';
  card.onclick = function(e) {
    if (e.target.tagName === 'A') return;
    const title = card.querySelector('.card-title')?.textContent || '';
    const meta = card.querySelector('.event-meta')?.innerHTML || '';
    const speakers = card.querySelector('.event-speakers-preview')?.innerHTML || '';
    const content = card.querySelector('.card-text')?.innerHTML || '';
    const imgs = Array.from(card.querySelectorAll('img')).map(img => img.src);
    const category = card.querySelector('.event-category')?.textContent || '';
    const date = card.querySelector('.event-date .day') && card.querySelector('.event-date .month') ? card.querySelector('.event-date .day').textContent + ' ' + card.querySelector('.event-date .month').textContent : '';
    const time = card.querySelector('.event-time')?.textContent.replace(/\s*\bTime\b\s*/i, '') || '';
    const location = card.querySelector('.event-location')?.textContent.replace(/\s*\bLocation\b\s*/i, '') || '';
    // Try to get captions from data-captions attribute (if present in future), else fallback
    let captions = [];
    if (card.dataset.captions) {
      try { captions = JSON.parse(card.dataset.captions); } catch(e) { captions = []; }
    }
    // More info: try to get from data-moreinfo attribute (if present in future), else fallback
    let moreInfo = card.dataset.moreinfo || '';
    openEventModal(title, meta, speakers, content, imgs, category, date, time, location, captions, moreInfo);
  };
});
closeEventModalX.onclick = () => {
  eventDetailsModal.classList.remove('active');
  eventDetailsModal.style.display = 'none';
  clearInterval(galleryAutoPlayTimer);
  if (lastFocusedElement) lastFocusedElement.focus();
};
window.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    eventDetailsModal.classList.remove('active');
    eventDetailsModal.style.display = 'none';
    clearInterval(galleryAutoPlayTimer);
    if (lastFocusedElement) lastFocusedElement.focus();
  }
});
window.onclick = function(event) {
  if (event.target === eventDetailsModal) {
    eventDetailsModal.classList.remove('active');
    eventDetailsModal.style.display = 'none';
    clearInterval(galleryAutoPlayTimer);
    if (lastFocusedElement) lastFocusedElement.focus();
  }
};

// Registration Modal Accessibility: focus trap, ESC to close
const eventRegisterModal = document.getElementById('eventRegisterModal');
const closeRegisterModalX = document.getElementById('closeRegisterModalX');
const eventRegisterForm = document.getElementById('eventRegisterForm');
const eventRegisterMsg = document.getElementById('eventRegisterMsg');
const eventRegisterSubmitBtn = document.getElementById('eventRegisterSubmitBtn');
const registerName = document.getElementById('registerName');
const registerEmail = document.getElementById('registerEmail');
const registerPhone = document.getElementById('registerPhone');
const registerWebsite = document.getElementById('registerWebsite');
let lastRegisterFocused = null;
function openRegisterModal(eventId) {
  document.getElementById('registerEventId').value = eventId || '';
  eventRegisterForm.reset();
  eventRegisterMsg.textContent = '';
  eventRegisterMsg.classList.remove('success');
  eventRegisterModal.style.display = 'flex';
  eventRegisterModal.classList.add('active');
  lastRegisterFocused = document.activeElement;
  setTimeout(() => { registerName.focus(); }, 100);
}
function closeRegisterModal() {
  eventRegisterModal.style.display = 'none';
  eventRegisterModal.classList.remove('active');
  if (lastRegisterFocused) lastRegisterFocused.focus();
}
closeRegisterModalX.onclick = closeRegisterModal;
window.addEventListener('keydown', function(e) {
  if (eventRegisterModal.classList.contains('active') && e.key === 'Escape') closeRegisterModal();
});
eventRegisterModal.onclick = function(e) {
  if (e.target === eventRegisterModal) closeRegisterModal();
};
// Focus trap
const focusableRegisterEls = () => eventRegisterModal.querySelectorAll('input,button,textarea,select,[tabindex]:not([tabindex="-1"])');
eventRegisterModal.addEventListener('keydown', function(e) {
  if (!eventRegisterModal.classList.contains('active')) return;
  if (e.key === 'Tab') {
    const els = Array.from(focusableRegisterEls());
    const first = els[0];
    const last = els[els.length - 1];
    if (e.shiftKey) {
      if (document.activeElement === first) { e.preventDefault(); last.focus(); }
    } else {
      if (document.activeElement === last) { e.preventDefault(); first.focus(); }
    }
  }
});
// Client-side validation
function validateRegisterForm() {
  let valid = true;
  eventRegisterMsg.textContent = '';
  eventRegisterMsg.classList.remove('success');
  if (!registerName.value.trim()) {
    eventRegisterMsg.textContent = 'Please enter your name.';
    registerName.focus();
    valid = false;
  } else if (!registerEmail.value.trim() || !/^\S+@\S+\.\S+$/.test(registerEmail.value)) {
    eventRegisterMsg.textContent = 'Please enter a valid email address.';
    registerEmail.focus();
    valid = false;
  } else if (!registerPhone.value.trim() || !/^\+?[0-9\-\s]{7,}$/.test(registerPhone.value)) {
    eventRegisterMsg.textContent = 'Please enter a valid phone number.';
    registerPhone.focus();
    valid = false;
  } else if (registerWebsite.value) {
    // Honeypot filled: likely spam
    eventRegisterMsg.textContent = 'Spam detected.';
    valid = false;
  }
  return valid;
}
// AJAX submission
if (eventRegisterForm) {
  eventRegisterForm.onsubmit = function(e) {
    e.preventDefault();
    if (!validateRegisterForm()) return;
    eventRegisterMsg.textContent = '';
    eventRegisterMsg.classList.remove('success');
    eventRegisterSubmitBtn.disabled = true;
    eventRegisterSubmitBtn.innerHTML = '<span class="spinner"></span> <span>Registering...</span>';
    const formData = new FormData(eventRegisterForm);
    fetch('../backend/register_event.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        eventRegisterMsg.textContent = 'Thank you for registering! We have received your details.';
        eventRegisterMsg.classList.add('success');
        eventRegisterForm.reset();
        setTimeout(closeRegisterModal, 1800);
      } else {
        eventRegisterMsg.textContent = data.error || 'Registration failed. Please try again.';
      }
    })
    .catch(() => {
      eventRegisterMsg.textContent = 'Network error. Please try again.';
    })
    .finally(() => {
      eventRegisterSubmitBtn.disabled = false;
      eventRegisterSubmitBtn.innerHTML = '<span>Register</span>';
    });
  };
}
</script>
<?php include 'modern-footer.html'; ?>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init();
</script>
<script>
    function scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }
</script>
<script>
    function toggleFab() {
      const fabOptions = document.querySelector('.fab-options');
      fabOptions.classList.toggle('show');
    }
</script>
<!-- Bootstrap Bundle JS (only once, at the end) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<button id="backToTop" aria-label="Back to top" title="Back to top">&#8679;</button>
<script>
// Show/hide back to top button
const backToTop = document.getElementById('backToTop');
window.addEventListener('scroll', function() {
  if (window.scrollY > 300) {
    backToTop.classList.add('show');
        } else {
    backToTop.classList.remove('show');
        }
    });
backToTop.addEventListener('click', function() {
  window.scrollTo({ top: 0, behavior: 'smooth' });
});
</script>
</body>
</html>
