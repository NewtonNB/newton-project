<?php
require '../shared/config.php';
$teachers = $conn->query("SELECT * FROM teachers ORDER BY full_name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teaching Staff Gallery</title>
    <!-- Cleaned up CSS links -->
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="modern-footer.css">
    <!-- Font Awesome 6 for navbar icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Fonts for Bungee and Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Bungee&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

<style>
/* Staff Page Styles */
@import url('https://fonts.googleapis.com/css2?family=Bungee&family=Poppins:wght@300;400;500;600;700;800;900&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    line-height: 1.6;
    color: #333;
    background: linear-gradient(135deg, #f8f9fa 60%, #e3f0ff 100%);
    min-height: 100vh;
}

.header {
    background: linear-gradient(135deg, #2c5aa0 0%, #1e3a8a 100%);
    color: white;
    text-align: center;
    padding: 120px 0 80px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(44,90,160,0.10);
    border-radius: 0 0 24px 24px;
    margin-bottom: 2.5rem;
    z-index: 10;
}

.header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('./nyabzgallery/current.jpg') no-repeat center center/cover;
    opacity: 0.3;
    z-index: 1;
    border-radius: 0 0 24px 24px;
}

.text-box {
    position: relative;
    z-index: 2;
    max-width: 800px;
    margin: 0 auto;
    padding: 0 20px;
}

.text-box h1 {
    font-family: 'Bungee', cursive;
    font-size: 3.5rem;
    margin-bottom: 1rem;
    text-shadow: 2px 2px 8px rgba(0,0,0,0.3);
    color: #166f26;
    letter-spacing: 1.5px;
}

.gallery {
    padding: 4rem 0;
    background: transparent;
}

.gallery-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.07);
}

.teacher-card {
    background: #f9f9f9;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(22,111,38,0.10);
    transition: transform 0.22s, box-shadow 0.22s, background 0.22s;
    text-align: center;
    border: 1.5px solid #e0e0e0;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 2.2rem 1.2rem 1.5rem 1.2rem;
    margin-bottom: 0.5rem;
}

.teacher-card:hover {
    transform: translateY(-10px) scale(1.045);
    box-shadow: 0 10px 32px rgba(22,111,38,0.16);
    background: #f4fff7;
    border: 1.5px solid #10b981;
}

.teacher-card img {
    border-radius: 50%;
    width: 120px;
    height: 120px;
    object-fit: cover;
    margin-bottom: 1.1rem;
    border: 3px solid #e0e0e0;
    background: #fff;
    box-shadow: 0 2px 8px rgba(44,90,160,0.07);
    transition: box-shadow 0.22s, border 0.22s;
}

.teacher-card h3 {
    font-family: 'Bungee', cursive;
    color: #166f26;
    font-size: 1.13rem;
    margin: 0.5rem 0 0.2rem 0;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-shadow: 0 1px 4px rgba(44,90,160,0.04);
}

.teacher-card p {
    color: #555;
    font-size: 1.01rem;
    margin: 0;
    font-weight: 500;
    letter-spacing: 0.1px;
}

.teaching-hero {
  min-height: 44vh;
  width: 100%;
  background-image: linear-gradient(rgba(44,90,160,0.7),rgba(16,185,129,0.7)), url('nyabzgallery/current.jpg');
  background-position: center;
  background-size: cover;
  background-attachment: fixed;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 32px rgba(44,90,160,0.10);
  border-radius: 0 0 24px 24px;
  margin-bottom: 2.5rem;
  z-index: 10;
  position: relative;
  overflow: hidden;
}
.teaching-hero-overlay {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: linear-gradient(135deg, rgba(44,90,160,0.7) 0%, rgba(16,185,129,0.7) 100%);
  z-index: 1;
  border-radius: 0 0 24px 24px;
}
.teaching-hero .text-box {
  position: relative;
  z-index: 2;
  text-align: center;
  color: #fff;
  width: 100%;
  max-width: 700px;
  margin: 0 auto;
  padding: 60px 20px 30px 20px;
}
.page-title {
  font-family: 'Bungee', cursive;
  font-size: 2.7rem;
  color: #166f26;
  margin-top: 2rem;
  margin-bottom: 0.5rem;
  text-align: center;
  letter-spacing: 1.5px;
  text-shadow: 0 2px 8px rgba(44,90,160,0.08);
}
.hero-subtitle {
  font-size: 1.18rem;
  color: #e5e7eb;
  max-width: 600px;
  margin: 0 auto 1.5rem auto;
  opacity: 0.97;
  font-weight: 500;
  text-align: center;
  letter-spacing: 0.2px;
}
@media (max-width: 700px) {
  .teaching-hero {
    min-height: 30vh;
    padding: 20px 0;
    border-radius: 0 0 12px 12px;
  }
  .teaching-hero .page-title {
    font-size: 1.5rem;
  }
  .teaching-hero .text-box {
    padding: 30px 5px 20px 5px;
  }
}
@media (max-width: 900px) {
    .gallery-container {
        gap: 1.2rem;
        padding: 1.2rem 0.5rem;
    }
    .teacher-card {
        width: 45vw;
        min-width: 170px;
        max-width: 260px;
    }
}
@media (max-width: 600px) {
    .gallery-container {
        grid-template-columns: 1fr;
        gap: 1rem;
        padding: 0 10px;
    }
    .teacher-card {
        width: 90vw;
        min-width: 140px;
        max-width: 98vw;
        padding: 1.2rem 0.5rem;
    }
    .text-box h1 {
        font-size: 2rem;
    }
    .header {
        padding: 100px 0 60px;
        border-radius: 0 0 12px 12px;
    }
}
/* Reduce navbar font size only for this page */
.navbar, .navbar .container {
    font-size: 15px !important;
}
.nav-link {
    font-size: 15px !important;
    padding: 0 12px !important;
}
.navbar-title, .navbar-title-marquee {
    font-size: 1em !important;
}
</style>    
</head>
<body>
<?php include 'navbar.php'; ?>

<section class="teaching-hero animate__animated animate__fadeInDown" aria-label="Teaching Staff Hero">
  <div class="teaching-hero-overlay"></div>
  <div class="text-box animate__animated animate__fadeInUp">
    <h1 class="page-title animate__animated animate__fadeInUp" style="animation-delay:0.1s;">Teaching Staff</h1>
    <p class="hero-subtitle animate__animated animate__fadeInUp" style="animation-delay:0.3s;">
      Meet our dedicated teaching staff who inspire, educate, and guide our students to excellence every day.
    </p>
  </div>
</section>


        <div class="text-box wow animate__animated animate__fadeInUp animate__delay-1s" style="display:none;">
           <h1 style="font-family: Bungee;  margin-bottom:100px;">TEACHING STAFF</h1><br>
      
            <div class="teacher-card slow-fade" data-aos="fade-up" data-aos-delay="100">
      <img src="nyabzgallery/default.png" alt="Mr. Kimuli">
      <h3>MR. KIMULI</h3>
      <p>Agriculture</p>
    </div>
    <div class="teacher-card slow-fade" data-aos="fade-up" data-aos-delay="100">
      <img src="nyabzgallery/teacher_686e47b5b0f0f2.30009133.jpg" alt="Mr. Kimuli">
      <h3>MR. KIMULI</h3>
      <p>Agriculture</p>
    </div>
    <div class="teacher-card slow-fade" data-aos="fade-up" data-aos-delay="100">
      <img src="nyabzgallery/teacher_686e47c8aa1c94.23380109.jpg" alt="Ms. Kaketo Sulait">
      <h3>MS. KAKETO SULAIT</h3>
      <p>Geography</p>
    </div>
    <div class="teacher-card slow-fade" data-aos="fade-up" data-aos-delay="100">
      <img src="nyabzgallery/teacher_686e47eecf4363.94450118.jpg" alt="Ms. Agatha">
      <h3>MS. AGATHA</h3>
      <p>English</p>
    </div>
    <div class="teacher-card slow-fade" data-aos="fade-up" data-aos-delay="100">
      <img src="nyabzgallery/teacher_686e64cb718099.16589115.png" alt="Mr. Nicholas Akampurira">
      <h3>MR. NICHOLAS AKAMPURIRA</h3>
      <p>Mathematics</p>
    </div>
    <div class="teacher-card slow-fade" data-aos="fade-up" data-aos-delay="100">
      <img src="nyabzgallery/teacher_686e64e87bda74.25457742.jpg" alt="Mr. Turyasiima Elly">
      <h3>MR. TURYASIIMA ELLY</h3>
      <p>Headmaster</p>
    </div>
</div>
    </section>
    
<section class="gallery">
  <div class="gallery-container">
    <?php if ($teachers && $teachers->num_rows > 0): ?>
      <?php while($teacher = $teachers->fetch_assoc()): ?>
        <div class="teacher-card" data-aos="fade-up" data-aos-delay="100">
          <img src="nyabzgallery/<?php echo !empty($teacher['photo']) ? htmlspecialchars($teacher['photo']) : 'default.png'; ?>" alt="<?php echo htmlspecialchars($teacher['full_name']); ?>">
          <h3><?php echo htmlspecialchars(strtoupper($teacher['full_name'])); ?></h3>
          <p><?php echo htmlspecialchars($teacher['subject']); ?></p>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="text-align:center; grid-column: 1 / -1; padding: 40px; font-style:italic; color:#777;">No teachers found in the database.</p>
    <?php endif; ?>
  </div>
</section>


<?php include 'modern-footer.html'; ?>


    <script>
        
    function showMenu() {
        document.getElementById('navlinks').style.right = "0";
    }

    function hideMenu() {
        document.getElementById('navlinks').style.right = "-200px";
    }


    </script>


<!-- AOS.js -->
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init();
</script>


<script>
    function scrollToTop() {
window.scrollTo({
    top: 0, // Scroll to the top of the page
    behavior: 'smooth' // Smooth scrolling animation
});
}


AOS.init({
  duration: 800,
  easing: 'ease-in-out',
  once: true,
});

</script>

<script>
    function toggleFab() {
      const fabOptions = document.querySelector('.fab-options');
      fabOptions.classList.toggle('show');
    }
    </script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
