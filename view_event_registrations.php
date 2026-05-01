<?php
// Disable error reporting to prevent HTML output
error_reporting(0);
ini_set('display_errors', 0);

session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Event Registrations - Admin</title>
  <?php include 'admin_css.php'; ?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; }
    .content { margin-top: 40px; display: flex; justify-content: center; align-items: flex-start; min-height: 80vh; padding: 0 20px; }
    .modern-table-container { background: rgba(255,255,255,0.95); backdrop-filter: blur(20px); border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.1), 0 8px 16px rgba(0,0,0,0.05), inset 0 1px 0 rgba(255,255,255,0.8); padding: 48px 40px 40px 40px; width: 100%; max-width: 1200px; margin-top: 0; border: 1px solid rgba(255,255,255,0.2); position: relative; overflow: hidden; }
    .modern-table-container::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #f5576c); border-radius: 24px 24px 0 0; }
    .header-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; position: relative; }
    .modern-table-container h2 { font-size: 2.2rem; font-weight: 800; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: 0; display: flex; align-items: center; gap: 16px; letter-spacing: -0.5px; }
    .table-icon { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 2.6rem; filter: drop-shadow(0 4px 8px rgba(102, 126, 234, 0.3)); }
    .stats-section { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 32px; }
    .stat-card { background: rgba(255,255,255,0.9); backdrop-filter: blur(10px); border-radius: 16px; padding: 24px; text-align: center; border: 1px solid rgba(255,255,255,0.3); box-shadow: 0 8px 25px rgba(0,0,0,0.1); transition: all 0.3s ease; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.15); }
    .stat-number { font-size: 2.2rem; font-weight: 800; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: 8px; }
    .stat-label { color: #4a5568; font-weight: 600; font-size: 0.95rem; }
    .filter-bar { display: flex; align-items: center; gap: 18px; flex-wrap: wrap; background: linear-gradient(120deg, #fff 80%, #e3eafc 100%); border-radius: 18px; box-shadow: 0 4px 18px rgba(102,126,234,0.10); padding: 18px 24px; margin: 0 auto 32px auto; max-width: 1100px; width: 98%; position: relative; animation: fadeInBar 0.7s cubic-bezier(.77,0,.18,1); }
    @keyframes fadeInBar { from { opacity: 0; transform: translateY(-18px) scale(0.98); } to { opacity: 1; transform: none; } }
    .filter-bar select, .filter-bar input[type="text"] { padding: 10px 18px; border-radius: 12px; border: 2px solid #667eea; font-size: 1.08rem; background: #f8f8ff; color: #333; font-weight: 600; box-shadow: 0 2px 8px rgba(102,126,234,0.07); transition: border-color 0.2s, box-shadow 0.2s; outline: none; }
    .filter-bar select:focus, .filter-bar input[type="text"]:focus { border-color: #764ba2; box-shadow: 0 0 0 4px rgba(118,75,162,0.13); }
    .filter-bar .export-btn { background: linear-gradient(135deg, #43cea2 0%, #185a9d 100%); color: #fff; border: none; border-radius: 10px; padding: 13px 28px; font-size: 1.08rem; font-weight: 700; cursor: pointer; box-shadow: 0 2px 12px rgba(102,126,234,0.10); transition: background 0.22s, box-shadow 0.22s, transform 0.18s; margin-left: 6px; display: flex; align-items: center; gap: 8px; }
    .filter-bar .export-btn:hover { background: linear-gradient(135deg, #2b5876 0%, #4e4376 100%); transform: scale(1.04); box-shadow: 0 6px 24px rgba(102,126,234,0.18); }
    .filter-bar .fa-download { color: #fff; font-size: 1.2rem; margin-left: 0; }
    .modern-table th, .modern-table td { text-align: center; vertical-align: middle; padding: 18px 16px; }
    .modern-table th { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; font-size: 1.08rem; font-weight: 700; border: none; }
    .modern-table td { background: rgba(255,255,255,0.92); font-size: 1.01rem; color: #2d3748; font-weight: 500; border-bottom: 1px solid #e2e8f0; }
    .modern-table tr:hover td { background: rgba(102,126,234,0.07); transform: scale(1.01); box-shadow: 0 4px 12px rgba(102,126,234,0.10); }
    .table-responsive { width: 100%; overflow-x: auto; border-radius: 16px; background: rgba(255,255,255,0.5); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); margin-bottom: 24px; }
    .btn-action { background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); color: #fff; border: none; padding: 8px 16px; border-radius: 8px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; }
    .btn-action:hover { background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); transform: translateY(-2px); }
    .btn-action:disabled { opacity: 0.6; cursor: not-allowed; }
    .pagination { display: flex; justify-content: center; gap: 8px; margin-top: 20px; }
    .page-item { list-style: none; }
    .page-link { background: #fff; border: 1.5px solid #667eea; color: #667eea; border-radius: 8px; font-size: 1.05rem; font-weight: 600; padding: 8px 16px; cursor: pointer; transition: all 0.2s; text-decoration: none; display: block; }
    .page-item.active .page-link, .page-link:hover { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; border-color: #667eea; }
    
    /* Dynamic Loading States */
    .loading-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); display: none; align-items: center; justify-content: center; z-index: 1000; border-radius: 24px; }
    .loading-spinner { width: 40px; height: 40px; border: 4px solid #e3eafc; border-top: 4px solid #667eea; border-radius: 50%; animation: spin 1s linear infinite; }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    
    /* Real-time Updates */
    .update-indicator { position: fixed; top: 20px; right: 20px; background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%); color: white; padding: 12px 20px; border-radius: 8px; font-weight: 600; transform: translateX(300px); transition: transform 0.3s ease; z-index: 2000; }
    .update-indicator.show { transform: translateX(0); }
    
    /* Enhanced Filters */
    .filter-bar { position: relative; }
    .filter-toggle { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; border: none; border-radius: 10px; padding: 10px 20px; font-size: 1rem; font-weight: 600; cursor: pointer; margin-left: 10px; }
    .advanced-filters { display: none; position: absolute; top: 100%; left: 0; right: 0; background: #fff; border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.1); padding: 20px; margin-top: 10px; z-index: 100; }
    .advanced-filters.show { display: block; animation: slideDown 0.3s ease; }
    @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    
    /* Interactive Table */
    .modern-table tbody tr { cursor: pointer; transition: all 0.2s ease; }
    .modern-table tbody tr:hover { background: rgba(102,126,234,0.1) !important; transform: scale(1.01); }
    .modern-table tbody tr.selected { background: rgba(102,126,234,0.15) !important; border-left: 4px solid #667eea; }
    
    /* Action Buttons */
    .bulk-actions { display: none; background: rgba(255,255,255,0.95); padding: 15px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 4px 16px rgba(0,0,0,0.1); }
    .bulk-actions.show { display: flex; align-items: center; gap: 15px; animation: fadeInUp 0.3s ease; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    
    /* Status Badges */
    .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; }
    .status-recent { background: #e8f5e8; color: #27ae60; }
    .status-old { background: #fef2e8; color: #f39c12; }
    
    @media (max-width: 900px) { .filter-bar { flex-direction: column; gap: 12px; align-items: stretch; padding: 12px 8px; } .modern-table th, .modern-table td { padding: 12px 6px; font-size: 0.97rem; } }
  </style>
</head>
<body>
<?php include 'admin_sidebar.php'; ?>
<div class="content">
  <div class="modern-table-container">
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
      <div class="loading-spinner"></div>
    </div>
    
    <div class="header-section">
      <h2><span class="table-icon"><i class="fa-solid fa-users"></i></span> Event Registrations</h2>
      <div>
        <button class="filter-toggle" onclick="toggleAdvancedFilters()">
          <i class="fa fa-filter"></i> Advanced Filters
        </button>
        <button class="filter-toggle" onclick="refreshData()" style="margin-left: 10px;">
          <i class="fa fa-refresh"></i> Refresh
        </button>
      </div>
    </div>
    <?php
    // Fetch all events for dropdown
    $events = $conn->query("SELECT id, title FROM announcements WHERE category = 'Event' ORDER BY date DESC, created_at DESC");
    $event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
    
    // Pagination logic
    $perPage = 15;
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $offset = ($page - 1) * $perPage;
    $where = $event_id ? "WHERE r.event_id = $event_id" : '';
    
    // Get total count safely
    $totalResult = $conn->query("SELECT COUNT(*) FROM event_registrations r $where");
    $total = $totalResult ? $totalResult->fetch_row()[0] : 0;
    $totalPages = $total > 0 ? ceil($total / $perPage) : 1;
    
    // Get registrations
    $sql = "SELECT r.id, a.title AS event_title, r.name, r.email, r.phone, r.created_at, r.event_id 
            FROM event_registrations r 
            LEFT JOIN announcements a ON r.event_id = a.id 
            $where 
            ORDER BY r.created_at DESC 
            LIMIT $perPage OFFSET $offset";
    $result = $conn->query($sql);
    
    // Dashboard stats
    $totalEventsResult = $conn->query("SELECT COUNT(*) FROM announcements WHERE category = 'Event'");
    $totalEvents = $totalEventsResult ? $totalEventsResult->fetch_row()[0] : 0;
    
    $mostPopular = $conn->query("SELECT event_id, COUNT(*) as cnt FROM event_registrations GROUP BY event_id ORDER BY cnt DESC LIMIT 1");
    $mostPopularTitle = '';
    $mostPopularCount = 0;
    if ($mostPopular && $mostPopular->num_rows > 0) {
        $row = $mostPopular->fetch_assoc();
        $mostPopularCount = $row['cnt'];
        $eid = $row['event_id'];
        $titleRes = $conn->query("SELECT title FROM announcements WHERE id = $eid");
        if ($titleRes && $titleRes->num_rows > 0) {
            $t = $titleRes->fetch_assoc();
            $mostPopularTitle = $t['title'];
        }
    }
    ?>
    <div class="stats-section" id="statsSection">
      <div class="stat-card">
        <div class="stat-number" id="totalRegistrations"><?php echo $total; ?></div>
        <div class="stat-label">Total Registrations</div>
      </div>
      <div class="stat-card">
        <div class="stat-number" id="totalEvents"><?php echo $totalEvents; ?></div>
        <div class="stat-label">Total Events</div>
      </div>
      <div class="stat-card">
        <div class="stat-number" id="mostPopularEvent"><?php echo $mostPopularTitle ? htmlspecialchars($mostPopularTitle) : 'N/A'; ?><br><span style="font-size:1rem;font-weight:500; color:#764ba2;" id="mostPopularCount">(<?php echo $mostPopularCount; ?> regs)</span></div>
        <div class="stat-label">Most Popular Event</div>
      </div>
    </div>
    <div class="filter-bar">
      <div style="display:flex; gap:18px; align-items:center; flex-wrap:wrap; width: 100%;">
        <select id="eventFilter" style="min-width:180px;">
          <option value="0">All Events</option>
          <?php if ($events && $events->num_rows > 0): foreach ($events as $ev): ?>
            <option value="<?php echo $ev['id']; ?>" <?php if ($event_id == $ev['id']) echo 'selected'; ?>><?php echo htmlspecialchars($ev['title']); ?></option>
          <?php endforeach; endif; ?>
        </select>
        <input type="text" id="searchInput" placeholder="Search by name, email, event, phone..." style="min-width:220px; flex: 1;">
        <button class="export-btn" onclick="exportTableToCSV('event_registrations.csv')">
          <i class="fa fa-download"></i> Export to CSV
        </button>
      </div>
      
      <!-- Advanced Filters -->
      <div class="advanced-filters" id="advancedFilters">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
          <div>
            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #667eea;">Date Range:</label>
            <input type="date" id="dateFrom" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
            <input type="date" id="dateTo" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px; margin-top: 5px;">
          </div>
          <div>
            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #667eea;">Sort By:</label>
            <select id="sortBy" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
              <option value="created_at_desc">Newest First</option>
              <option value="created_at_asc">Oldest First</option>
              <option value="name_asc">Name A-Z</option>
              <option value="name_desc">Name Z-A</option>
              <option value="event_title_asc">Event A-Z</option>
            </select>
          </div>
          <div>
            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #667eea;">Results Per Page:</label>
            <select id="perPage" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
              <option value="15">15 per page</option>
              <option value="25">25 per page</option>
              <option value="50">50 per page</option>
              <option value="100">100 per page</option>
            </select>
          </div>
        </div>
        <div style="margin-top: 15px; text-align: right;">
          <button onclick="applyAdvancedFilters()" style="background: #667eea; color: white; border: none; padding: 8px 20px; border-radius: 6px; margin-right: 10px;">Apply Filters</button>
          <button onclick="clearAdvancedFilters()" style="background: #6c757d; color: white; border: none; padding: 8px 20px; border-radius: 6px;">Clear All</button>
        </div>
      </div>
    </div>
    
    <!-- Bulk Actions -->
    <div class="bulk-actions" id="bulkActions">
      <span id="selectedCount">0 selected</span>
      <button onclick="bulkDelete()" style="background: #dc3545; color: white; border: none; padding: 8px 16px; border-radius: 6px;">
        <i class="fa fa-trash"></i> Delete Selected
      </button>
      <button onclick="bulkExport()" style="background: #28a745; color: white; border: none; padding: 8px 16px; border-radius: 6px;">
        <i class="fa fa-download"></i> Export Selected
      </button>
      <button onclick="clearSelection()" style="background: #6c757d; color: white; border: none; padding: 8px 16px; border-radius: 6px;">
        Clear Selection
      </button>
    </div>
    <div class="table-responsive">
      <table class="modern-table" id="registrationsTable">
        <thead>
          <tr>
            <th><input type="checkbox" id="selectAll" onchange="toggleSelectAll()"></th>
            <th onclick="sortTable('id')">ID <i class="fa fa-sort"></i></th>
            <th onclick="sortTable('event_title')">Event Title <i class="fa fa-sort"></i></th>
            <th onclick="sortTable('name')">Name <i class="fa fa-sort"></i></th>
            <th onclick="sortTable('email')">Email <i class="fa fa-sort"></i></th>
            <th onclick="sortTable('phone')">Phone <i class="fa fa-sort"></i></th>
            <th onclick="sortTable('created_at')">Registered At <i class="fa fa-sort"></i></th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="tableBody">
          <!-- Dynamic content will be loaded here -->
        </tbody>
      </table>
    </div>
    
    <!-- Dynamic Pagination -->
    <nav aria-label="Page navigation" id="paginationContainer">
      <!-- Pagination will be generated dynamically -->
    </nav>
  </div>
</div>

<!-- Update Indicator -->
<div class="update-indicator" id="updateIndicator">
  <i class="fa fa-check"></i> Data updated successfully!
</div>

<!-- Enhanced Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.1);">
      <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 16px 16px 0 0;">
        <h5 class="modal-title" id="detailsModalLabel"><i class="fa fa-user"></i> Registration Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="detailsModalBody" style="padding: 30px;">
        <!-- Populated by JS -->
      </div>
      <div class="modal-footer" style="border-top: 1px solid #eee; padding: 20px 30px;">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="editRegistration()">Edit Registration</button>
      </div>
    </div>
  </div>
</div>

<!-- Edit Registration Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius: 16px; border: none;">
      <div class="modal-header" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border-radius: 16px 16px 0 0;">
        <h5 class="modal-title" id="editModalLabel"><i class="fa fa-edit"></i> Edit Registration</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="padding: 30px;">
        <form id="editForm">
          <input type="hidden" id="editId">
          <div class="mb-3">
            <label for="editName" class="form-label">Name</label>
            <input type="text" class="form-control" id="editName" required>
          </div>
          <div class="mb-3">
            <label for="editEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="editEmail" required>
          </div>
          <div class="mb-3">
            <label for="editPhone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="editPhone" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" onclick="saveRegistration()">Save Changes</button>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Dynamic Event Registrations Management System
class EventRegistrationsManager {
  constructor() {
    this.currentPage = 1;
    this.perPage = 15;
    this.currentFilters = {
      event_id: 0,
      search: '',
      dateFrom: '',
      dateTo: '',
      sortBy: 'created_at_desc'
    };
    this.selectedRows = new Set();
    this.autoRefreshInterval = null;
    
    this.init();
  }
  
  init() {
    this.setupEventListeners();
    this.loadData();
    this.startAutoRefresh();
  }
  
  setupEventListeners() {
    // Search input with debounce
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', (e) => {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        this.currentFilters.search = e.target.value;
        this.currentPage = 1;
        this.loadData();
      }, 300);
    });
    
    // Event filter
    document.getElementById('eventFilter').addEventListener('change', (e) => {
      this.currentFilters.event_id = e.target.value;
      this.currentPage = 1;
      this.loadData();
    });
    
    // Advanced filters
    ['dateFrom', 'dateTo', 'sortBy', 'perPage'].forEach(id => {
      const element = document.getElementById(id);
      if (element) {
        element.addEventListener('change', () => {
          this.applyAdvancedFilters();
        });
      }
    });
  }
  
  async loadData() {
    this.showLoading(true);
    
    try {
      const params = new URLSearchParams({
        page: this.currentPage,
        per_page: this.perPage,
        ...this.currentFilters
      });
      
      const response = await fetch(`get_registrations_ajax.php?${params}`);
      const data = await response.json();
      
      if (data.success) {
        this.renderTable(data.registrations);
        this.renderPagination(data.pagination);
        this.updateStats(data.stats);
        this.showUpdateIndicator();
      } else {
        this.showError(data.error || 'Failed to load data');
      }
    } catch (error) {
      this.showError('Network error occurred');
      console.error('Load data error:', error);
    } finally {
      this.showLoading(false);
    }
  }
  
  renderTable(registrations) {
    const tbody = document.getElementById('tableBody');
    
    if (!registrations || registrations.length === 0) {
      tbody.innerHTML = '<tr><td colspan="8" class="text-center" style="padding: 40px; color: #666;">No registrations found</td></tr>';
      return;
    }
    
    tbody.innerHTML = registrations.map(reg => {
      const isRecent = this.isRecentRegistration(reg.created_at);
      const statusClass = isRecent ? 'status-recent' : 'status-old';
      const statusText = isRecent ? 'Recent' : 'Older';
      
      return `
        <tr data-id="${reg.id}" onclick="this.querySelector('input[type=checkbox]').click(); event.stopPropagation();">
          <td><input type="checkbox" onchange="registrationsManager.toggleRowSelection(${reg.id}, this.checked)" onclick="event.stopPropagation()"></td>
          <td>${reg.id}</td>
          <td>${this.escapeHtml(reg.event_title || 'Unknown Event')}</td>
          <td>${this.escapeHtml(reg.name)}</td>
          <td>${this.escapeHtml(reg.email)}</td>
          <td>${this.escapeHtml(reg.phone)}</td>
          <td>
            ${this.formatDate(reg.created_at)}
            <br><span class="status-badge ${statusClass}">${statusText}</span>
          </td>
          <td>
            <button class="btn-action" onclick="registrationsManager.showDetails(${reg.id}); event.stopPropagation();" style="margin-right: 5px;">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="btn-action" onclick="registrationsManager.deleteRegistration(${reg.id}); event.stopPropagation();">
              <i class="fa fa-trash"></i> Delete
            </button>
          </td>
        </tr>
      `;
    }).join('');
  }
  
  renderPagination(pagination) {
    const container = document.getElementById('paginationContainer');
    
    if (pagination.total_pages <= 1) {
      container.innerHTML = '';
      return;
    }
    
    let paginationHtml = '<ul class="pagination justify-content-center mt-3">';
    
    // Previous button
    if (pagination.current_page > 1) {
      paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="registrationsManager.goToPage(${pagination.current_page - 1}); return false;">Previous</a></li>`;
    }
    
    // Page numbers
    const startPage = Math.max(1, pagination.current_page - 2);
    const endPage = Math.min(pagination.total_pages, pagination.current_page + 2);
    
    for (let i = startPage; i <= endPage; i++) {
      const activeClass = i === pagination.current_page ? ' active' : '';
      paginationHtml += `<li class="page-item${activeClass}"><a class="page-link" href="#" onclick="registrationsManager.goToPage(${i}); return false;">${i}</a></li>`;
    }
    
    // Next button
    if (pagination.current_page < pagination.total_pages) {
      paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="registrationsManager.goToPage(${pagination.current_page + 1}); return false;">Next</a></li>`;
    }
    
    paginationHtml += '</ul>';
    container.innerHTML = paginationHtml;
  }
  
  updateStats(stats) {
    document.getElementById('totalRegistrations').textContent = stats.total_registrations;
    document.getElementById('totalEvents').textContent = stats.total_events;
    document.getElementById('mostPopularEvent').innerHTML = `${stats.most_popular_title || 'N/A'}<br><span style="font-size:1rem;font-weight:500; color:#764ba2;">(${stats.most_popular_count} regs)</span>`;
  }
  
  async showDetails(id) {
    try {
      const response = await fetch(`get_registration_details.php?id=${id}`);
      const data = await response.json();
      
      if (data.success) {
        const reg = data.registration;
        document.getElementById('detailsModalBody').innerHTML = `
          <div class="row">
            <div class="col-md-6">
              <h6 style="color: #667eea; margin-bottom: 15px;"><i class="fa fa-user"></i> Personal Information</h6>
              <p><strong>Name:</strong> ${this.escapeHtml(reg.name)}</p>
              <p><strong>Email:</strong> ${this.escapeHtml(reg.email)}</p>
              <p><strong>Phone:</strong> ${this.escapeHtml(reg.phone)}</p>
            </div>
            <div class="col-md-6">
              <h6 style="color: #667eea; margin-bottom: 15px;"><i class="fa fa-calendar"></i> Event Information</h6>
              <p><strong>Event:</strong> ${this.escapeHtml(reg.event_title || 'Unknown Event')}</p>
              <p><strong>Registered:</strong> ${this.formatDate(reg.created_at)}</p>
              <p><strong>Registration ID:</strong> #${reg.id}</p>
            </div>
          </div>
        `;
        
        // Store current registration data for editing
        this.currentRegistration = reg;
        
        const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
        modal.show();
      }
    } catch (error) {
      this.showError('Failed to load registration details');
    }
  }
  
  async deleteRegistration(id) {
    showConfirmModal(
      'Are you sure you want to delete this registration? This action cannot be undone.',
      async () => {
    
    try {
      const response = await fetch('delete_registration.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${encodeURIComponent(id)}`
      });
      
      const data = await response.json();
      
      if (data.success) {
        this.loadData();
        this.showUpdateIndicator('Registration deleted successfully');
      } else {
        this.showError(data.error || 'Delete failed');
      }
    } catch (error) {
      this.showError('Network error occurred');
    }
      },
      { title: 'Delete Registration?', confirmText: 'Yes, Delete', icon: 'fa-user-minus' }
    );
  }
  
  toggleRowSelection(id, checked) {
    if (checked) {
      this.selectedRows.add(id);
    } else {
      this.selectedRows.delete(id);
    }
    
    this.updateBulkActions();
    this.updateSelectAllCheckbox();
  }
  
  updateBulkActions() {
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');
    
    if (this.selectedRows.size > 0) {
      bulkActions.classList.add('show');
      selectedCount.textContent = `${this.selectedRows.size} selected`;
    } else {
      bulkActions.classList.remove('show');
    }
  }
  
  goToPage(page) {
    this.currentPage = page;
    this.loadData();
  }
  
  showLoading(show) {
    document.getElementById('loadingOverlay').style.display = show ? 'flex' : 'none';
  }
  
  showUpdateIndicator(message = 'Data updated successfully!') {
    const indicator = document.getElementById('updateIndicator');
    indicator.innerHTML = `<i class="fa fa-check"></i> ${message}`;
    indicator.classList.add('show');
    
    setTimeout(() => {
      indicator.classList.remove('show');
    }, 3000);
  }
  
  showError(message) {
    alert(message); // You can replace this with a better notification system
  }
  
  isRecentRegistration(dateString) {
    const regDate = new Date(dateString);
    const now = new Date();
    const diffHours = (now - regDate) / (1000 * 60 * 60);
    return diffHours <= 24; // Consider recent if within 24 hours
  }
  
  formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
  }
  
  escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }
  
  startAutoRefresh() {
    // Refresh data every 30 seconds
    this.autoRefreshInterval = setInterval(() => {
      this.loadData();
    }, 30000);
  }
  
  stopAutoRefresh() {
    if (this.autoRefreshInterval) {
      clearInterval(this.autoRefreshInterval);
    }
  }
}

// Global functions for UI interactions
function toggleAdvancedFilters() {
  const filters = document.getElementById('advancedFilters');
  filters.classList.toggle('show');
}

function applyAdvancedFilters() {
  registrationsManager.currentFilters.dateFrom = document.getElementById('dateFrom').value;
  registrationsManager.currentFilters.dateTo = document.getElementById('dateTo').value;
  registrationsManager.currentFilters.sortBy = document.getElementById('sortBy').value;
  registrationsManager.perPage = parseInt(document.getElementById('perPage').value);
  registrationsManager.currentPage = 1;
  registrationsManager.loadData();
}

function clearAdvancedFilters() {
  document.getElementById('dateFrom').value = '';
  document.getElementById('dateTo').value = '';
  document.getElementById('sortBy').value = 'created_at_desc';
  document.getElementById('perPage').value = '15';
  applyAdvancedFilters();
}

function refreshData() {
  registrationsManager.loadData();
}

function toggleSelectAll() {
  const selectAll = document.getElementById('selectAll');
  const checkboxes = document.querySelectorAll('#tableBody input[type="checkbox"]');
  
  checkboxes.forEach(cb => {
    cb.checked = selectAll.checked;
    const id = parseInt(cb.closest('tr').dataset.id);
    if (selectAll.checked) {
      registrationsManager.selectedRows.add(id);
    } else {
      registrationsManager.selectedRows.delete(id);
    }
  });
  
  registrationsManager.updateBulkActions();
}

function bulkDelete() {
  if (registrationsManager.selectedRows.size === 0) return;
  
  showConfirmModal(
    `Are you sure you want to delete ${registrationsManager.selectedRows.size} registration(s)? This action cannot be undone.`,
    function() { console.log('Bulk delete:', Array.from(registrationsManager.selectedRows)); },
    { title: 'Delete Selected?', confirmText: 'Yes, Delete All', icon: 'fa-trash' }
  );
}

function bulkExport() {
  if (registrationsManager.selectedRows.size === 0) return;
  
  // Implementation for bulk export
  console.log('Bulk export:', Array.from(registrationsManager.selectedRows));
}

function clearSelection() {
  registrationsManager.selectedRows.clear();
  document.querySelectorAll('#tableBody input[type="checkbox"]').forEach(cb => cb.checked = false);
  document.getElementById('selectAll').checked = false;
  registrationsManager.updateBulkActions();
}

function editRegistration() {
  if (!registrationsManager.currentRegistration) return;
  
  const reg = registrationsManager.currentRegistration;
  document.getElementById('editId').value = reg.id;
  document.getElementById('editName').value = reg.name;
  document.getElementById('editEmail').value = reg.email;
  document.getElementById('editPhone').value = reg.phone;
  
  bootstrap.Modal.getInstance(document.getElementById('detailsModal')).hide();
  
  const editModal = new bootstrap.Modal(document.getElementById('editModal'));
  editModal.show();
}

function saveRegistration() {
  // Implementation for saving edited registration
  console.log('Save registration');
}

function exportTableToCSV(filename) {
  // Enhanced CSV export with current filters
  const rows = document.querySelectorAll('#registrationsTable tr:not([style*="display: none"])');
  let csv = [];
  
  for (let i = 0; i < rows.length; i++) {
    let row = [], cols = rows[i].querySelectorAll('th:not(:first-child):not(:last-child), td:not(:first-child):not(:last-child)');
    for (let j = 0; j < cols.length; j++) {
      row.push('"' + cols[j].innerText.replace(/"/g, '""') + '"');
    }
    csv.push(row.join(','));
  }
  
  const csvFile = new Blob([csv.join('\n')], { type: 'text/csv' });
  const downloadLink = document.createElement('a');
  downloadLink.download = filename;
  downloadLink.href = window.URL.createObjectURL(csvFile);
  downloadLink.style.display = 'none';
  document.body.appendChild(downloadLink);
  downloadLink.click();
  document.body.removeChild(downloadLink);
}

// Initialize the system
let registrationsManager;
document.addEventListener('DOMContentLoaded', function() {
  registrationsManager = new EventRegistrationsManager();
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
  if (registrationsManager) {
    registrationsManager.stopAutoRefresh();
  }
});
</script>
<?php include 'delete_modal.php'; ?>
</body>
</html> 