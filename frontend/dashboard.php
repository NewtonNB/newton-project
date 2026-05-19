<?php
// dashboard.php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

require '../shared/config.php'; // database connection

// Fetch basic counts
$studentCount = $conn->query("SELECT COUNT(*) FROM students WHERE deleted_at IS NULL AND usertype='student'")->fetch_row()[0];
$teacherCount = $conn->query("SELECT COUNT(*) FROM teachers WHERE deleted_at IS NULL")->fetch_row()[0];
$classCount   = $conn->query("SELECT COUNT(*) FROM classes")->fetch_row()[0] ?: 0;
$feeCollected = $conn->query("SELECT SUM(amount_paid) FROM fees")->fetch_row()[0] ?: 0;

// Fetch event statistics
$totalEvents = $conn->query("SELECT COUNT(*) FROM announcements WHERE category = 'Event'")->fetch_row()[0] ?: 0;
$totalEventRegistrations = $conn->query("SELECT COUNT(*) FROM event_registrations")->fetch_row()[0] ?: 0;
$upcomingEvents = $conn->query("SELECT COUNT(*) FROM announcements WHERE category = 'Event' AND date >= date('now')")->fetch_row()[0] ?: 0;

// Fetch recent event registrations
$eventRegistrationsSql = "SELECT er.*, a.title as event_title 
                         FROM event_registrations er 
                         LEFT JOIN announcements a ON er.event_id = a.id 
                         ORDER BY er.created_at DESC 
                         LIMIT 10";
$eventRegistrationsResult = $conn->query($eventRegistrationsSql);

// Fetch popular events
$popularEventsSql = "SELECT a.title, a.date, COUNT(er.id) as registration_count 
                    FROM announcements a 
                    LEFT JOIN event_registrations er ON a.id = er.event_id 
                    WHERE a.category = 'Event' 
                    GROUP BY a.id, a.title, a.date 
                    ORDER BY registration_count DESC, a.date DESC 
                    LIMIT 5";
$popularEventsResult = $conn->query($popularEventsSql);

$sql = "SELECT * FROM contact_messages WHERE deleted_at IS NULL ORDER BY submitted_at DESC LIMIT 5";
$result = $conn->query($sql);
$trashCount = $conn->query("SELECT COUNT(*) FROM contact_messages WHERE deleted_at IS NOT NULL")->fetch_row()[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NYABIKONI SECONDARY SCHOOL - Admin Dashboard</title>
    <!-- Font Awesome CDN for icons -->
    <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">-->
    <?php include 'admin_css.php'; ?>
<style>
   
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        font-family: "poppins";
        margin: 0;
        padding: 0;
    }
    
    .content {
        margin-top: 40px;
        margin-left: 280px;
        display: block;
        min-height: 100vh;
        padding: 0 20px;
        max-width: calc(100vw - 280px);
    }
    
    .modern-content {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 24px;
        box-shadow: 
            0 20px 40px rgba(0, 0, 0, 0.1),
            0 8px 16px rgba(0, 0, 0, 0.05),
            inset 0 1px 0 rgba(255, 255, 255, 0.8);
        padding: 56px 40px 40px 40px;
        width: 100%;
        max-width: 1200px;
        margin-top: 0;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.2);
        animation: fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    @keyframes fadeInUp {
        from { 
            opacity: 0; 
            transform: translateY(40px); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0); 
        }
    }
    
    .modern-content::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #f5576c);
        border-radius: 24px 24px 0 0;
    }
    
    .dashboard-title {
        font-size: 2.8rem;
        font-weight: 900;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 8px;
        text-align: center;
        letter-spacing: -1px;
        line-height: 1.1;
    }
    
    .dashboard-subtitle {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-size: 1.2rem;
        text-align: center;
        margin-bottom: 32px;
        font-weight: 500;
        opacity: 0.9;
    }
    
    .dashboard-divider {
        border: none;
        border-top: 2px solid rgba(102, 126, 234, 0.2);
        margin: 0 auto 40px auto;
        width: 80px;
        height: 2px;
        background: linear-gradient(90deg, transparent, #667eea, transparent);
    }
    
    .dashboard-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
        justify-content: center;
        margin-bottom: 48px;
    }
    
    .event-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
        justify-content: center;
        margin-bottom: 48px;
    }
    
    .stat-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 40px 28px 32px 28px;
        text-align: center;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        animation: slideInUp 0.6s ease forwards;
        opacity: 0;
        transform: translateY(30px);
    }
    
    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
    .stat-card:nth-child(4) { animation-delay: 0.4s; }
    
    @keyframes slideInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2);
        border-radius: 20px 20px 0 0;
    }
    
    .stat-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }
    
    .stat-icon {
        font-size: 3.5rem;
        margin-bottom: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        filter: drop-shadow(0 4px 8px rgba(102, 126, 234, 0.3));
        transition: transform 0.3s ease;
    }
    
    .stat-card:hover .stat-icon {
        transform: scale(1.1) rotate(-5deg);
    }
    
    .stat-card.students .stat-icon { 
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .stat-card.teachers .stat-icon { 
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .stat-card.classes .stat-icon { 
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .stat-card.fees .stat-icon { 
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .stat-card.events .stat-icon { 
        background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .stat-card.registrations .stat-icon { 
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .stat-card.upcoming .stat-icon { 
        background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .stat-card h3 {
        margin-bottom: 12px;
        color: #2d3748;
        font-size: 1.4rem;
        font-weight: 700;
        letter-spacing: -0.5px;
    }
    
    .stat-card .stat-value {
        font-size: 2.8rem;
        font-weight: 900;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-top: 8px;
        line-height: 1;
    }
    
    .dashboard-chart-container {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        padding: 32px 24px 24px 24px;
        margin: 48px auto 48px auto;
        max-width: 800px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .dashboard-chart-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2);
        border-radius: 20px 20px 0 0;
    }
    
    .quick-links-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        padding: 40px 32px 32px 32px;
        margin-bottom: 48px;
        max-width: 1000px;
        margin: 48px auto 48px auto;
        border: 1px solid rgba(255, 255, 255, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .quick-links-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #f093fb, #f5576c);
        border-radius: 20px 20px 0 0;
    }
    
    .quick-links-title {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 800;
        font-size: 1.8rem;
        margin-bottom: 32px;
        letter-spacing: -0.5px;
        text-align: center;
    }
    
    .quick-links-btns {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
        justify-content: center;
    }
    .quick-link-btn:nth-child(4) {
        grid-column: 2 / 3; /* Center the 4th button under the 3 above */
    }
    
    .quick-link-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        border: none;
        border-radius: 16px;
        font-size: 1.1rem;
        font-weight: 600;
        padding: 20px 32px;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        position: relative;
        overflow: hidden;
    }
    
    .quick-link-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }
    
    .quick-link-btn:hover::before {
        left: 100%;
    }
    
    .quick-link-btn:hover {
        background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
    }
    
    .quick-link-btn i {
        font-size: 1.4rem;
    }
    
    .recent-messages-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        padding: 40px 32px 32px 32px;
        margin-bottom: 48px;
        max-width: 1200px;
        margin: 48px auto 48px auto;
        border: 1px solid rgba(255, 255, 255, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .recent-messages-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #43e97b, #38f9d7);
        border-radius: 20px 20px 0 0;
    }
    
    .recent-messages-title {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 800;
        font-size: 1.8rem;
        margin-bottom: 32px;
        letter-spacing: -0.5px;
        text-align: center;
    }
    
    .recent-messages-table-scroll {
        overflow-x: auto;
        overflow-y: auto;
        width: 100%;
        max-height: 400px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.5);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    .modern-table.recent-messages-table {
        width: 100%;
        min-width: 1100px;
        table-layout: auto;
        border-collapse: separate;
        border-spacing: 0;
        background: transparent;
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 0;
        font-size: 1rem;
    }
    
    .modern-table.recent-messages-table th {
        position: sticky;
        top: 0;
        z-index: 2;
        padding: 20px 24px;
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        color: #fff;
        font-size: 1.1rem;
        text-align: left;
        border: none;
        letter-spacing: 0.5px;
        font-weight: 700;
        white-space: normal;
        word-break: break-word;
    }
    
    .modern-table.recent-messages-table td {
        padding: 18px 24px;
        background: rgba(255, 255, 255, 0.8);
        color: #2d3748;
        border-bottom: 1px solid rgba(67, 233, 123, 0.1);
        vertical-align: middle;
        word-break: break-word;
        white-space: normal;
        font-size: 1rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .modern-table.recent-messages-table tr:last-child td {
        border-bottom: none;
    }
    
    .modern-table.recent-messages-table tbody tr:nth-child(even) td {
        background: rgba(67, 233, 123, 0.05);
    }
    
    .modern-table.recent-messages-table tbody tr:hover td {
        background: rgba(67, 233, 123, 0.1);
        transform: scale(1.01);
        box-shadow: 0 4px 12px rgba(67, 233, 123, 0.1);
    }
    
    .reply-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 12px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .reply-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }
    
    .reply-btn:hover::before {
        left: 100%;
    }
    
    .reply-btn:hover {
        background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }
    
    @media (max-width: 1200px) {
        .modern-content { padding: 40px 20px; max-width: 98vw; }
        .dashboard-stats { gap: 20px; grid-template-columns: repeat(2, 1fr); }
        .event-stats { gap: 20px; }
        .quick-links-btns { grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); }
    }
    
    @media (max-width: 900px) {
        .modern-content { padding: 32px 16px; }
        .dashboard-stats { grid-template-columns: 1fr; gap: 20px; }
        .event-stats { grid-template-columns: 1fr; gap: 16px; }
        .quick-links-btns { grid-template-columns: 1fr; gap: 16px; }
        .dashboard-title { font-size: 2.2rem; }
        .dashboard-subtitle { font-size: 1.1rem; }
    }
    
    @media (max-width: 700px) {
        .modern-content { padding: 24px 12px; }
        .content { margin-top: 20px; padding: 0 10px; }
        .dashboard-title { font-size: 1.8rem; }
        .stat-card { padding: 24px 16px; }
        .stat-card .stat-value { font-size: 2.2rem; }
        .quick-links-card, .recent-messages-card { padding: 24px 16px; }
    }
    </style>
</head>
<body>
<?php include 'admin_sidebar.php'; ?>
<div class="content">
  <div class="modern-content">
    <div class="dashboard-title">Admin Dashboard</div>
    <div class="dashboard-subtitle">Welcome to NYABIKONI Secondary School Management System</div>
    <hr class="dashboard-divider" />
    
    <div class="dashboard-stats">
      <div class="stat-card students">
        <div class="stat-icon"><i class="fa-solid fa-user-graduate"></i></div>
        <h3>Total Students</h3>
        <div class="stat-value"><?php echo $studentCount; ?></div>
      </div>
      <div class="stat-card teachers">
        <div class="stat-icon"><i class="fa-solid fa-chalkboard-teacher"></i></div>
        <h3>Total Teachers</h3>
        <div class="stat-value"><?php echo $teacherCount; ?></div>
      </div>
      <div class="stat-card classes">
        <div class="stat-icon"><i class="fa-solid fa-layer-group"></i></div>
        <h3>Total Classes</h3>
        <div class="stat-value"><?php echo $classCount; ?></div>
      </div>
      <div class="stat-card fees">
        <div class="stat-icon"><i class="fa-solid fa-money-bill-wave"></i></div>
        <h3>Fees Collected</h3>
        <div class="stat-value">UGX <?php echo $feeCollected ? number_format($feeCollected) : '0'; ?></div>
      </div>
    </div>
    
    <!-- Event Statistics Section -->
    <div class="recent-messages-card" style="margin: 32px auto;">
      <div class="recent-messages-title"><i class="fa-solid fa-calendar-check"></i> Event Management Overview</div>
      <div class="event-stats">
        <div class="stat-card events">
          <div class="stat-icon"><i class="fa-solid fa-calendar-days"></i></div>
          <h3>Total Events</h3>
          <div class="stat-value"><?php echo $totalEvents; ?></div>
        </div>
        <div class="stat-card registrations">
          <div class="stat-icon"><i class="fa-solid fa-user-check"></i></div>
          <h3>Total Registrations</h3>
          <div class="stat-value"><?php echo $totalEventRegistrations; ?></div>
        </div>
        <div class="stat-card upcoming">
          <div class="stat-icon"><i class="fa-solid fa-clock"></i></div>
          <h3>Upcoming Events</h3>
          <div class="stat-value"><?php echo $upcomingEvents; ?></div>
        </div>
      </div>
    </div>
    
    <!-- Popular Events Section -->
    <div class="recent-messages-card" style="margin: 32px auto;">
      <div class="recent-messages-title"><i class="fa-solid fa-trophy"></i> Most Popular Events</div>
      <div class="recent-messages-table-scroll">
        <table class="modern-table recent-messages-table">
          <thead>
            <tr>
              <th>Event Title</th>
              <th>Event Date</th>
              <th>Registrations</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($popularEventsResult && $popularEventsResult->num_rows > 0) {
              while($event = $popularEventsResult->fetch_assoc()) {
                $eventDate = $event['date'] ? date('M j, Y', strtotime($event['date'])) : 'No date set';
                $isUpcoming = $event['date'] && strtotime($event['date']) >= time();
                $status = $isUpcoming ? '<span style="color: #27ae60; font-weight: 600;">Upcoming</span>' : '<span style="color: #e74c3c; font-weight: 600;">Past</span>';
                
                echo '<tr>';
                echo '<td>' . htmlspecialchars($event['title']) . '</td>';
                echo '<td>' . htmlspecialchars($eventDate) . '</td>';
                echo '<td><span style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 4px 12px; border-radius: 12px; font-weight: 600;">' . $event['registration_count'] . '</span></td>';
                echo '<td>' . $status . '</td>';
                echo '<td><a href="announcements.php" class="reply-btn"><i class="fa-solid fa-edit"></i> Manage</a></td>';
                echo '</tr>';
              }
            } else {
              echo '<tr><td colspan="5" style="text-align: center; color: #666;">No events found</td></tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
    
    <div class="dashboard-chart-container">
      <canvas id="dashboardChart" height="110"></canvas>
    </div>
    
    <!-- Quick Links Section -->
    <div class="quick-links-card">
      <div class="quick-links-title"><i class="fa-solid fa-bolt"></i> Quick Actions</div>
      <div class="quick-links-btns">
        <a href="view_student.php" class="quick-link-btn"><i class="fa-solid fa-users"></i> Manage Students</a>
        <a href="view_teacher.php" class="quick-link-btn"><i class="fa-solid fa-chalkboard-teacher"></i> Manage Teachers</a>
        <a href="manage_classes.php" class="quick-link-btn"><i class="fa-solid fa-layer-group"></i> Manage Classes</a>
        <a href="announcements.php" class="quick-link-btn"><i class="fa-solid fa-bullhorn"></i> Manage Events</a>
        <a href="view_event_registrations.php" class="quick-link-btn"><i class="fa-solid fa-calendar-check"></i> Event Registrations</a>
        <a href="events.php" class="quick-link-btn"><i class="fa-solid fa-calendar-days"></i> View Public Events</a>
      </div>
    </div>
    
    
    <!-- Recent Event Registrations Section -->
    <div class="recent-messages-card">
      <div class="recent-messages-title"><i class="fa-solid fa-user-plus"></i> Recent Event Registrations</div>
      <div class="recent-messages-table-scroll">
        <table class="modern-table recent-messages-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Event Title</th>
              <th>Participant Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Registered At</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($eventRegistrationsResult && $eventRegistrationsResult->num_rows > 0) {
              while($reg = $eventRegistrationsResult->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($reg['id']) . '</td>';
                echo '<td>' . htmlspecialchars($reg['event_title'] ?: 'Unknown Event') . '</td>';
                echo '<td>' . htmlspecialchars($reg['name']) . '</td>';
                echo '<td>' . htmlspecialchars($reg['email']) . '</td>';
                echo '<td>' . htmlspecialchars($reg['phone']) . '</td>';
                echo '<td>' . htmlspecialchars(date('M j, Y H:i', strtotime($reg['created_at']))) . '</td>';
                echo '<td><a href="view_event_registrations.php?event_id=' . htmlspecialchars($reg['event_id']) . '" class="reply-btn"><i class="fa-solid fa-eye"></i> View Details</a></td>';
                echo '</tr>';
              }
            } else {
              echo '<tr><td colspan="7" style="text-align: center; color: #666;">No event registrations yet</td></tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
      <div style="text-align: center; margin-top: 20px;">
        <a href="view_event_registrations.php" class="quick-link-btn" style="display: inline-flex; padding: 12px 24px; font-size: 1rem;">
          <i class="fa-solid fa-list"></i> View All Registrations
        </a>
      </div>
    </div>
    
    <!-- Recent Contact Messages Section -->
    <div class="recent-messages-card">
      <div class="recent-messages-title">
        <i class="fa-solid fa-envelope-open-text"></i> Recent Contact Messages
        <?php if ($trashCount > 0): ?>
          <a href="trash_messages.php" style="margin-left:12px; background:#e74c3c; color:#fff; border-radius:20px; padding:3px 12px; font-size:0.8rem; font-weight:700; text-decoration:none;">
            <i class="fa-solid fa-trash"></i> Trash (<?php echo $trashCount; ?>)
          </a>
        <?php endif; ?>
      </div>
      <div class="recent-messages-table-scroll">
        <table class="modern-table recent-messages-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>First Name</th>
              <th>Last Name</th>
              <th>Email</th>
              <th>Message</th>
              <th>Submitted At</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
              while($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                echo '<td>' . htmlspecialchars($row['first_name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['last_name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                echo '<td>' . htmlspecialchars(mb_strimwidth($row['message'], 0, 40, '...')) . '</td>';
                echo '<td>' . htmlspecialchars($row['submitted_at']) . '</td>';
                echo '<td><button type="button" class="reply-btn" data-id="' . htmlspecialchars($row['id']) . '" data-firstname="' . htmlspecialchars($row['first_name']) . '" data-email="' . htmlspecialchars($row['email']) . '"><i class="fa-solid fa-reply"></i> Reply</button>
                <button type="button" class="remove-msg-btn" data-id="' . htmlspecialchars($row['id']) . '" style="background:linear-gradient(135deg,#ff6b6b,#ee5a52);color:#fff;border:none;border-radius:8px;padding:7px 14px;font-size:0.85rem;font-weight:600;cursor:pointer;margin-left:6px;"><i class="fa-solid fa-trash"></i> Remove</button></td>';
                echo '</tr>';
              }
            } else {
              echo '<tr><td colspan="7" style="text-align: center; color: #666;">No contact messages yet</td></tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
      <div style="text-align: center; margin-top: 20px;">
        <a href="contactus.php" class="quick-link-btn" style="display: inline-flex; padding: 12px 24px; font-size: 1rem;">
          <i class="fa-solid fa-envelope"></i> View All Messages
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Reply Modal -->
<div id="replyModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(44,62,80,0.18); z-index:9999; align-items:center; justify-content:center;">
  <div id="replyModalContent" style="background:#fff; border-radius:18px; max-width:420px; width:95vw; padding:0; position:relative; box-shadow:0 12px 48px rgba(52,152,219,0.22); overflow:hidden; animation:modalPopIn 0.32s cubic-bezier(.68,-0.55,.27,1.55);">
    <div style="background:linear-gradient(90deg,#3498db 0%,#6dd5fa 100%); padding:28px 24px 18px 24px; display:flex; align-items:center; gap:16px;">
      <div style="background:#fff; border-radius:50%; width:54px; height:54px; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 8px rgba(52,152,219,0.13);">
        <i class="fa-solid fa-user-circle" style="font-size:2.2rem; color:#3498db;"></i>
      </div>
      <div>
        <div style="color:#fff; font-size:1.18rem; font-weight:700; letter-spacing:0.5px;">Reply to Message</div>
        <div id="modalRecipient" style="color:#eaf6fb; font-size:0.98rem; font-weight:500;"></div>
      </div>
      <button id="closeReplyModal" style="position:absolute; top:18px; right:22px; background:none; border:none; font-size:1.7rem; color:#fff; cursor:pointer; opacity:0.7; transition:opacity 0.18s;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.7">&times;</button>
    </div>
    <form id="replyForm" method="POST" action="reply_contact.php" style="padding:28px 24px 18px 24px;">
      <input type="hidden" name="id" id="replyMessageId">
      <div style="margin-bottom:18px;">
        <label for="replyTo" style="font-weight:600; color:#2980b9; margin-bottom:6px; display:block;">To:</label>
        <input type="text" id="replyTo" name="to" readonly style="width:100%; padding:10px 12px; border-radius:8px; border:1.5px solid #e3eaf1; background:#f4f8fb; font-size:1.05rem; color:#2980b9; font-weight:500; outline:none;">
      </div>
      <div style="margin-bottom:18px;">
        <label for="replyMessage" style="font-weight:600; color:#2980b9; margin-bottom:6px; display:block;">Message:</label>
        <textarea id="replyMessage" name="reply" rows="5" required style="width:100%; padding:10px 12px; border-radius:8px; border:1.5px solid #e3eaf1; background:#f4f8fb; font-size:1.05rem; color:#222; outline:none; resize:vertical; transition:border 0.18s;"></textarea>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%; background:linear-gradient(90deg,#27ae60 0%,#1abc9c 100%); border:none; border-radius:8px; font-size:1.13rem; font-weight:700; padding:13px 0; color:#fff; box-shadow:0 2px 8px rgba(39,174,96,0.13); transition:background 0.18s, transform 0.15s; margin-bottom:8px;"> <i class="fa-solid fa-paper-plane"></i> Send Reply</button>
      <div id="replySuccessMsg" style="display:none; color:#27ae60; font-weight:600; text-align:center; margin-top:10px;">Reply sent successfully!</div>
    </form>
  </div>
</div>

<style>
@keyframes modalPopIn {
  0% { transform:scale(0.85) translateY(40px); opacity:0; }
  100% { transform:scale(1) translateY(0); opacity:1; }
}
#replyModal::-webkit-scrollbar { display:none; }
#replyModalContent input:focus, #replyModalContent textarea:focus {
  border:1.5px solid #3498db !important;
  background:#eaf6fb !important;
}
</style>
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var ctx = document.getElementById('dashboardChart').getContext('2d');
  var dashboardChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Students', 'Teachers', 'Classes', 'Events', 'Registrations'],
      datasets: [{
        label: 'Statistics',
        data: [
          <?php echo $studentCount; ?>,
          <?php echo $teacherCount; ?>,
          <?php echo $classCount; ?>,
          <?php echo $totalEvents; ?>,
          <?php echo $totalEventRegistrations; ?>
        ],
        backgroundColor: [
          'rgba(102, 126, 234, 0.8)',
          'rgba(240, 147, 251, 0.8)',
          'rgba(79, 172, 254, 0.8)',
          'rgba(255, 154, 158, 0.8)',
          'rgba(168, 237, 234, 0.8)'
        ],
        borderColor: [
          '#667eea',
          '#f093fb',
          '#4facfe',
          '#ff9a9e',
          '#a8edea'
        ],
        borderWidth: 2,
        borderRadius: 12,
        borderSkipped: false,
        maxBarThickness: 60
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            color: '#2d3748',
            font: { size: 14, weight: '600' }
          },
          grid: { 
            color: 'rgba(102, 126, 234, 0.1)',
            drawBorder: false
          }
        },
        x: {
          ticks: {
            color: '#2d3748',
            font: { size: 14, weight: '600' }
          },
          grid: { display: false }
        }
      }
    }
  });
});
document.querySelectorAll('.reply-btn').forEach(function(btn) {
  btn.addEventListener('click', function() {
    document.getElementById('replyModal').style.display = 'flex';
    document.getElementById('replyMessageId').value = btn.getAttribute('data-id');
    document.getElementById('replyTo').value = btn.getAttribute('data-email');
    document.getElementById('modalRecipient').textContent = btn.getAttribute('data-email') + ' (' + btn.getAttribute('data-firstname') + ')';
    document.getElementById('replyMessage').value = '';
    document.getElementById('replySuccessMsg').style.display = 'none';
  });
});
document.getElementById('closeReplyModal').onclick = function() {
  document.getElementById('replyModal').style.display = 'none';
};
document.getElementById('replyModal').addEventListener('click', function(e) {
  if (e.target === this) this.style.display = 'none';
});
// Optional: Show a success message after submit (simulate for demo)
document.getElementById('replyForm').addEventListener('submit', function(e) {
  // Uncomment below to use AJAX instead of normal POST
  // e.preventDefault();
  // document.getElementById('replySuccessMsg').style.display = 'block';
  // setTimeout(function(){ document.getElementById('replyModal').style.display = 'none'; }, 1200);
});
</script>
<!-- Soft Delete Modal -->
<div id="removeModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.4); z-index:10000; align-items:center; justify-content:center;">
  <div style="background:#fff; border-radius:18px; max-width:380px; width:90vw; padding:32px; text-align:center; box-shadow:0 12px 40px rgba(0,0,0,0.15);">
    <div style="width:60px;height:60px;background:#fff5f5;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
      <i class="fa-solid fa-trash" style="font-size:1.6rem;color:#e74c3c;"></i>
    </div>
    <h3 style="margin:0 0 8px;color:#333;font-size:1.2rem;">Remove this message?</h3>
    <p style="color:#888;margin:0 0 24px;font-size:0.95rem;">This will quietly remove the message from your dashboard. It won't affect anything else.</p>
    <div style="display:flex;gap:12px;justify-content:center;">
      <button id="confirmRemove" style="background:linear-gradient(135deg,#ff6b6b,#ee5a52);color:#fff;border:none;border-radius:10px;padding:11px 28px;font-size:1rem;font-weight:700;cursor:pointer;">Yes, Remove</button>
      <button id="cancelRemove" style="background:#f0f0f0;color:#555;border:none;border-radius:10px;padding:11px 28px;font-size:1rem;font-weight:600;cursor:pointer;">Keep it</button>
    </div>
  </div>
</div>

<script>
// Soft delete messages
let removeId = null;
const removeModal = document.getElementById('removeModal');

document.querySelectorAll('.remove-msg-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        removeId = this.dataset.id;
        removeModal.style.display = 'flex';
    });
});

document.getElementById('cancelRemove').onclick = () => {
    removeModal.style.display = 'none';
    removeId = null;
};

document.getElementById('confirmRemove').onclick = function() {
    if (!removeId) return;
    this.textContent = 'Removing...';
    this.disabled = true;

    fetch('delete_message_ajax.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + encodeURIComponent(removeId)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Fade out the row
            const btn = document.querySelector(`.remove-msg-btn[data-id="${removeId}"]`);
            if (btn) {
                const row = btn.closest('tr');
                row.style.transition = 'opacity 0.4s';
                row.style.opacity = '0';
                setTimeout(() => row.remove(), 400);
            }
        }
        removeModal.style.display = 'none';
        removeId = null;
    })
    .catch(() => {
        removeModal.style.display = 'none';
        removeId = null;
    });
};
</script>
</body>
</html>
