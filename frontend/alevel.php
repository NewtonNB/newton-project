<?php
// alevel.php - Dynamic A-Level subjects page
require '../shared/config.php';

// Fetch A-Level subjects from database
$alevel_subjects = $conn->query("SELECT * FROM alevel_subjects ORDER BY subject_name ASC");

// Check if query was successful
if ($alevel_subjects === false) {
    // Query failed - table might not exist
    $alevel_subjects = null;
    $error_message = "Database error occurred";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A-Level Subjects - Nyabikoni Secondary School</title>
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="modern-footer.css">
    <!-- Font Awesome 6 for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Fonts: Bungee and Poppins only -->
    <link href="https://fonts.googleapis.com/css2?family=Bungee&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <style>
    /* Modern, professional styles for A-Level page */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', Arial, Helvetica, sans-serif;
    }
    body {
      color: #1f2937;
      background: #f8f9fa;
      line-height: 1.6;
    }
    .alevel-hero {
      min-height: 48vh;
      width: 100%;
      background: url('nyabzgallery/current.jpg') center center/cover no-repeat;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-top: 56px;
      box-shadow: 0 8px 32px 0 rgba(25, 118, 210, 0.15);
      overflow: hidden;
    }
    .alevel-hero-overlay {
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background: linear-gradient(120deg, rgba(44,90,160,0.7) 0%, rgba(16,185,129,0.7) 100%);
      backdrop-filter: blur(2.5px);
      z-index: 1;
    }
    .alevel-hero-text {
      position: relative;
      z-index: 2;
      text-align: center;
      color: #fff;
      padding: 60px 20px 30px 20px;
      max-width: 700px;
      margin: 0 auto;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 220px;
    }
    .alevel-hero-title {
      font-family: 'Bungee', cursive;
      font-size: 2.2rem;
      margin-bottom: 1rem;
      letter-spacing: 1.5px;
      text-shadow: 0 2px 12px rgba(44,90,160,0.25), 0 4px 24px rgba(0,0,0,0.18);
      font-weight: 900;
      color: #fff;
    }
    .alevel-hero-subtitle {
      font-size: 1.05rem;
      color: #e3f2fd;
      max-width: 650px;
      margin: 0 auto 10px auto;
      text-shadow: 0 1px 6px rgba(44,90,160,0.12), 0 2px 8px rgba(0,0,0,0.10);
      font-weight: 500;
      letter-spacing: 0.5px;
    }
    @media (max-width: 700px) {
      .alevel-hero-title {
        font-size: 1.3rem;
      }
      .alevel-hero-subtitle {
        font-size: 0.95rem;
      }
    }
    .alevel-divider {
      height: 2px;
      background: linear-gradient(90deg, #1976d2 0%, #42a5f5 100%);
      margin: 0 0 32px 0;
      border-radius: 2px;
    }
    .alevel-section-heading {
      text-align: center;
      color: #1976d2;
      font-family: 'Bungee', cursive;
      font-size: 2rem;
      margin-bottom: 36px;
      letter-spacing: 1px;
    }
    .alevel-section {
      padding: 60px 0 30px 0;
      background: #f8f9fa;
    }
    .subject-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
      gap: 32px;
      max-width: 1100px;
      margin: 0 auto;
      padding: 0 16px;
    }
    .course {
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 4px 24px rgba(44,90,160,0.10), 0 1.5px 6px 0 rgba(44,90,160,0.08);
      border-left: 6px solid #268b17;
      padding: 2.2rem 1.5rem 1.5rem 1.5rem;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      transition: transform 0.22s, box-shadow 0.22s, background 0.22s;
      position: relative;
      min-height: 160px;
    }
    .course:hover {
      transform: translateY(-10px) scale(1.035);
      box-shadow: 0 10px 32px rgba(44,90,160,0.16);
      background: #f4fff7;
      border-left: 6px solid #10b981;
    }
    .course h3 {
      font-family: 'Bungee', cursive;
      color: #166f26;
      font-size: 1.18rem;
      margin-bottom: 0.5rem;
      font-weight: 700;
      letter-spacing: 0.5px;
      text-shadow: 0 1px 4px rgba(44,90,160,0.04);
    }
    .course p {
      color: #555;
      font-size: 1.01rem;
      margin: 0;
      font-weight: 500;
      letter-spacing: 0.1px;
    }
    .no-subjects, .database-error {
      text-align: center;
      padding: 50px 20px;
      color: #666;
    }
    .no-subjects i, .database-error i {
      font-size: 4rem;
      color: #ddd;
      margin-bottom: 20px;
    }
    .no-subjects h3, .database-error h3 {
      color: #268b17;
      margin-bottom: 15px;
    }
    .no-subjects p, .database-error p {
      font-size: 1.1rem;
      line-height: 1.6;
    }
    .database-error {
      color: #d32f2f;
      background: #ffebee;
      border-radius: 10px;
      margin: 20px;
    }
    .database-error i {
      color: #d32f2f;
    }
    .database-error h3 {
      color: #d32f2f;
    }
    .database-error a {
      color: #1976d2;
      text-decoration: none;
      font-weight: 600;
    }
    .database-error a:hover {
      text-decoration: underline;
    }
    @media (max-width: 900px) {
      .subject-grid {
        gap: 18px;
        padding: 0 6px;
      }
      .course {
        padding: 1.2rem 0.8rem 1.1rem 0.8rem;
      }
    }
    @media (max-width: 600px) {
      .subject-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
        padding: 0 2px;
      }
      .course {
        width: 98vw;
        min-width: 140px;
        max-width: 99vw;
        padding: 1.1rem 0.5rem;
      }
    }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<!-- Modern Hero Section -->
<section class="alevel-hero" aria-label="A-Level Hero">
  <div class="alevel-hero-overlay"></div>
  <div class="alevel-hero-text" data-aos="fade-down" data-aos-duration="1200">
    <h1 class="alevel-hero-title">A-LEVEL SUBJECTS</h1>
    <p class="alevel-hero-subtitle">Our Advanced level program serves over 240 students from S.5 to S.6. Academics centre around a Uganda National curriculum, including the following subjects:</p>
  </div>
</section>

<section class="alevel-section" aria-label="A-Level Subjects">
  <div class="alevel-divider"></div>
  <h2 class="alevel-section-heading" data-aos="fade-up" data-aos-delay="100">Explore A-Level Subjects</h2>
  <div class="subject-grid" role="list" aria-label="A-Level Subjects List">
    <?php if ($alevel_subjects && $alevel_subjects->num_rows > 0): ?>
      <?php $delay = 0; ?>
      <?php while ($subject = $alevel_subjects->fetch_assoc()): ?>
        <article class="course" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>" role="listitem" tabindex="0" aria-label="<?php echo htmlspecialchars($subject['subject_name']); ?>">
          <h3><?php echo htmlspecialchars($subject['subject_name']); ?></h3>
          <p>Advanced study of <?php echo htmlspecialchars(strtolower($subject['subject_name'])); ?> principles, theories, and practical applications.</p>
        </article>
        <?php $delay += 100; ?>
      <?php endwhile; ?>
    <?php elseif ($alevel_subjects === null): ?>
      <div class="database-error" data-aos="fade-in" role="alert">
        <i class="fa fa-database" aria-hidden="true"></i>
        <h3>Database Setup Required</h3>
        <p>The subjects database needs to be set up. Please run the database setup script.</p>
        <p><a href="setup_database.php">Click here to set up the database</a></p>
      </div>
    <?php else: ?>
      <div class="no-subjects" data-aos="fade-in" role="status">
        <i class="fa fa-university" aria-hidden="true"></i>
        <h3>No A-Level Subjects Available</h3>
        <p>Subjects will be displayed here once they are added to the system.</p>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include 'modern-footer.html'; ?>

<!-- Back to Top Button -->
<button onclick="scrollToTop()" aria-label="Back to Top" style="position:fixed;bottom:32px;right:32px;z-index:999;background:#1976d2;color:#fff;border:none;border-radius:50%;width:48px;height:48px;box-shadow:0 2px 8px rgba(25,118,210,0.18);font-size:1.5rem;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background 0.2s;">
  <i class="fas fa-arrow-up"></i>
</button>

<!-- AOS JS -->
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 900,
    easing: 'ease-in-out',
    once: true,
  });
</script>
    
    <!-- JavaScript for toggle menu -->
    <script>
        var navlinks = document.getElementById("navlinks");
        function showMenu(){
            navlinks.style.right = "0"
        }
        function hideMenu(){
            navlinks.style.right = "-200px"
        }
    </script>

    <script>
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    </script>
</body>
</html> 