<?php
// about.php - About page for Nyabikoni Secondary School
// This file contains all the content from about.html wrapped in PHP for future dynamic functionality
require 'config.php';

function get_count($conn, $sql, $label) {
    $result = $conn->query($sql);
    if ($result === false) {
        die("<b>SQL error in $label:</b> Database error<br>Query: $sql");
    }
    return $result->fetch_row()[0];
}

// Fetch statistics from the database
// $graduatedCount = get_count($conn, "SELECT COUNT(*) FROM students WHERE status='Graduated'", 'Graduated Students');
// $teacherCount = get_count($conn, "SELECT COUNT(*) FROM teachers", 'Teachers');
// $currentStudentCount = get_count($conn, "SELECT COUNT(*) FROM students WHERE status='Active'", 'Current Students');
// $activityCount = get_count($conn, "SELECT COUNT(*) FROM extracurricular_activities WHERE status='Active'", 'Activities');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Nyabikoni SS</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bungee&family=Press+Start+2P&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS (if needed) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- Animate.css and AOS (if needed) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet"/>
    <!-- Your site styles -->
    <link rel="stylesheet" href="style3.css">
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="modern-footer.css">
    <!-- Font Awesome (only once, latest version, before footer) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<!-- Modern Hero Section -->
<section class="about-hero-section position-relative" style="min-height: 90vh; display: flex; align-items: center; justify-content: center; background: url('nyabzgallery/current.jpg') center/cover no-repeat; padding-top: 120px;">
  <div class="about-hero-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(120deg, rgba(44,90,160,0.85) 0%, rgba(30,58,138,0.7) 60%, rgba(16,185,129,0.5) 100%); z-index: 1;"></div>
  <div class="container position-relative" style="z-index: 2;">
    <div class="text-center" data-aos="fade-down">
      <h1 style="font-family: 'Bungee', cursive; font-size: 3rem; color: #fff; text-shadow: 0 4px 24px rgba(44,90,160,0.25), 2px 2px 8px rgba(0,0,0,0.25); letter-spacing: 2px; margin-bottom: 1.2rem;">About Nyabikoni Secondary School</h1>
      <h2 style="font-family: 'Poppins', sans-serif; font-size: 1.7rem; color: #e5e7eb; font-weight: 600; margin-bottom: 1.5rem; text-shadow: 0 2px 8px rgba(44,90,160,0.18);">Striving for Excellence in Education & Character</h2>
      <p style="font-size: 1.15rem; color: #d1d5db; max-width: 700px; margin: 0 auto 2rem auto; text-shadow: 0 2px 8px rgba(44,90,160,0.10);">
        Welcome to Nyabikoni Secondary School, where we nurture future leaders through holistic education, modern facilities, and a commitment to excellence. Discover our vision, mission, and the vibrant community that makes us unique.
      </p>
      <a href="#about-2" class="about-hero-btn" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff; border: none; border-radius: 30px; padding: 16px 44px; font-size: 1.2rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1.2px; box-shadow: 0 4px 18px rgba(16, 185, 129, 0.18); transition: all 0.2s cubic-bezier(.4,2,.6,1); text-decoration: none; display: inline-flex; align-items: center; gap: 0.7em; outline: none;" data-aos="zoom-in" data-aos-delay="300">
        Learn More <i class="fas fa-arrow-down"></i>
      </a>
    </div>
  </div>
</section>

<section
  style="margin: 40px auto; max-width: 1200px; padding: 20px 40px; box-sizing: border-box;"
>
  <h2
    class="text-center animate__animated animate__fadeInDown"
    style="font-family: 'Bungee'; font-size: 2.5rem; margin-bottom: 30px;"
  >
    HEADMASTER'S MESSAGE
  </h2>

  <div
    class="d-flex flex-column flex-md-row align-items-center"
    style="gap: 30px;">
    <!-- Image with fadeInLeft -->
    <img
      src="nyabzgallery/HM.JPG"
      alt="Headmaster"
      class="animate__animated animate__fadeInLeft"
      style="
        width: 100%;
        max-width: 450px;
        border-radius: 20%;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        object-fit: cover;
        flex-shrink: 0;
        animation-delay: 0.3s;
        animation-fill-mode: both;
      "
    />

    <!-- Text Content with fadeInRight -->
    <div
      class="animate__animated animate__fadeInRight"
      style="
        flex: 1;
        color: #2c3e50;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        animation-delay: 0.6s;
        animation-fill-mode: both;
      "
    >
      <p style="font-size: 1.1rem; line-height: 1.7; margin-bottom: 20px;">
        Welcome to our website, dear visitor. We sincerely appreciate you taking
        the time out of your busy schedule for us. I am glad to see that you are
        interested in learning more about Nyabikoni Secondary School. There is no
        question that learning more about us will be a good decision.
      </p>
      <p style="font-size: 1.1rem; line-height: 1.7; margin-bottom: 20px;">
        The goal of Nyabikoni Secondary School is to provide a holistic education,
        generating people who are intellectually strong, physically healthy,
        socially balanced, and God-fearing. By emphasizing academics and
        extracurricular activities, this is achieved.
      </p>
      <p style="font-size: 1.1rem; line-height: 1.7; margin-bottom: 20px;">
        I am honored to be affiliated with Nyabikoni Secondary School, an
        institution whose mission is "To produce competent human resources to
        serve the church, state, and world." We try our hardest to make our dream
        a reality and not only talk about it; we actually put our vision into
        action.
      </p>
      <p
        style="
          font-weight: 700;
          font-size: 1.2rem;
          margin-top: 30px;
          color: #1b263b;
        "
      >
        By Head Teacher<br />
        <span style="font-size: 1.5rem;">Mr. Turyasiima Elly</span>
      </p>
    </div>
  </div>
 </section>

<div id="about-2">
  <div class="container">
    <div class="row row-cols-1 row-cols-md-3 g-4 text-center">
      <!-- VISION -->
      <div class="col h-100" data-aos="fade-up" data-aos-delay="100">
        <div class="about-item h-100">
          <i class="fa fa-book about-icon"></i>
          <h3 class="about-title">VISION</h3>
          <hr />
          <p>To be at the helm of producing competent human resource to serve the church, state and the world.</p>
        </div>
      </div>

      <!-- MISSION -->
      <div class="col h-100" data-aos="fade-up" data-aos-delay="200">
        <div class="about-item h-100">
          <i class="fa fa-pencil about-icon"></i>
          <h3 class="about-title">MISSION</h3>
          <hr />
          <p>To provide quality education through practical skills, teamwork, self-reliance and produce God-fearing persons.</p>
        </div>
      </div>

      <!-- CORE VALUES -->
      <div class="col h-100" data-aos="fade-up" data-aos-delay="300">
        <div class="about-item h-100">
          <i class="fa fa-globe about-icon"></i>
          <h3 class="about-title">CORE VALUES</h3>
          <hr />
          <ul>
            <li>Fear God</li>
            <li>Excellence</li>
            <li>Integrity</li>
            <li>Respect of persons and property</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Our Story Section -->
<section class="our-story-section py-5" style="background: #f8fafc;">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-down">
      <h2 class="section-title display-5 fw-bold mb-3" style="font-family: 'Bungee', cursive; color: #2c5aa0;">Our Story</h2>
      <p class="fs-5 text-muted">Founded in 2003, Nyabikoni Secondary School has grown from a humble beginning to a leading institution in Kabale, Uganda. Our journey is marked by a commitment to academic excellence, holistic development, and community service. Over the years, we have empowered thousands of students to become leaders and changemakers.</p>
    </div>
    <div class="row align-items-center g-4">
      <div class="col-md-6" data-aos="fade-right">
        <img src="nyabzgallery/Alevelstudents.jpg" alt="School History" class="img-fluid rounded shadow" style="min-height: 260px; object-fit: cover;">
      </div>
      <div class="col-md-6" data-aos="fade-left">
        <ul class="timeline list-unstyled">
          <li><strong>2003:</strong> School founded by Rev. Fr. Busingye Christopher.</li>
          <li><strong>2007:</strong> First cohort of O' Level graduates.</li>
          <li><strong>2012:</strong> Launch of A' Level program.</li>
          <li><strong>2018:</strong> Modern science labs and ICT center established.</li>
          <li><strong>2023:</strong> Celebrated 20 years of excellence.</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- Leadership Team Section -->
<section class="leadership-section py-5" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-down">
      <h2 class="section-title display-5 fw-bold mb-3" style="font-family: 'Bungee', cursive; color: #2c5aa0;">Leadership Team</h2>
      <p class="fs-5 text-muted">Meet our dedicated and experienced leadership team.</p>
    </div>
    <div class="row g-4 justify-content-center">
      <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="100">
        <div class="card border-0 shadow text-center h-100">
          <img src="nyabzgallery/HM.JPG" class="card-img-top rounded-circle mx-auto mt-3" alt="Headmaster" style="width: 120px; height: 120px; object-fit: cover;">
          <div class="card-body">
            <h5 class="card-title mb-1">Mr. Turyasiima Elly</h5>
            <p class="card-text text-muted mb-0">Head Teacher</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="200">
        <div class="card border-0 shadow text-center h-100">
          <img src="nyabzgallery/scholar.JPG" class="card-img-top rounded-circle mx-auto mt-3" alt="Director" style="width: 120px; height: 120px; object-fit: cover;">
          <div class="card-body">
            <h5 class="card-title mb-1">Rev. Fr. Busingye Christopher</h5>
            <p class="card-text text-muted mb-0">Executive Director</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="300">
        <div class="card border-0 shadow text-center h-100">
          <img src="nyabzgallery/LINET.jpg" class="card-img-top rounded-circle mx-auto mt-3" alt="Technical Advisor" style="width: 120px; height: 120px; object-fit: cover;">
          <div class="card-body">
            <h5 class="card-title mb-1">Eng. Agaba Wenceslaus</h5>
            <p class="card-text text-muted mb-0">Technical Advisor</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="400">
        <div class="card border-0 shadow text-center h-100">
          <img src="nyabzgallery/KAKETO SULAIT.jpg" class="card-img-top rounded-circle mx-auto mt-3" alt="Software Developer" style="width: 120px; height: 120px; object-fit: cover;">
          <div class="card-body">
            <h5 class="card-title mb-1">Ngabirano Agricola</h5>
            <p class="card-text text-muted mb-0">Software Developer</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Why Choose Us Section -->
<section class="why-choose-section py-5" style="background: #fff;">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-down">
      <h2 class="section-title display-5 fw-bold mb-3" style="font-family: 'Bungee', cursive; color: #2c5aa0;">Why Choose Nyabikoni SS?</h2>
      <p class="fs-5 text-muted">What sets us apart from other schools?</p>
    </div>
    <div class="row g-4 justify-content-center">
      <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="100">
        <div class="card border-0 shadow-sm text-center h-100 p-3">
          <i class="fas fa-graduation-cap fa-2x mb-3 text-primary"></i>
          <h5 class="fw-bold mb-2">Academic Excellence</h5>
          <p class="text-muted mb-0">Consistently high performance in national exams and a strong academic culture.</p>
        </div>
      </div>
      <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="200">
        <div class="card border-0 shadow-sm text-center h-100 p-3">
          <i class="fas fa-users fa-2x mb-3 text-success"></i>
          <h5 class="fw-bold mb-2">Holistic Development</h5>
          <p class="text-muted mb-0">Balanced focus on academics, sports, arts, and character building.</p>
        </div>
      </div>
      <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="300">
        <div class="card border-0 shadow-sm text-center h-100 p-3">
          <i class="fas fa-chalkboard-teacher fa-2x mb-3 text-warning"></i>
          <h5 class="fw-bold mb-2">Qualified Staff</h5>
          <p class="text-muted mb-0">Experienced, passionate teachers and a supportive administration.</p>
        </div>
      </div>
      <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="400">
        <div class="card border-0 shadow-sm text-center h-100 p-3">
          <i class="fas fa-hands-helping fa-2x mb-3 text-info"></i>
          <h5 class="fw-bold mb-2">Community & Values</h5>
          <p class="text-muted mb-0">A nurturing environment rooted in respect, integrity, and service.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Testimonials Carousel Section -->
<section class="testimonials-section py-5" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-down">
      <h2 class="section-title display-5 fw-bold mb-3" style="font-family: 'Bungee', cursive; color: #2c5aa0;">Testimonials</h2>
      <p class="fs-5 text-muted">What our students, parents, and alumni say about us</p>
    </div>
    <div id="testimonialsCarousel" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <div class="card border-0 shadow-sm h-100 p-4 text-center mx-auto" style="max-width: 400px;">
            <img src="nyabzgallery/scholar.JPG" alt="Testimonial 1" class="rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; object-fit: cover;">
            <p class="text-muted mb-3">"Nyabikoni SS gave me the confidence and skills to pursue my dreams. The teachers truly care about every student."</p>
            <h6 class="fw-bold mb-0">Scholastic K.</h6>
            <span class="text-secondary small">Alumnus</span>
          </div>
        </div>
        <div class="carousel-item">
          <div class="card border-0 shadow-sm h-100 p-4 text-center mx-auto" style="max-width: 400px;">
            <img src="nyabzgallery/HM.JPG" alt="Testimonial 2" class="rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; object-fit: cover;">
            <p class="text-muted mb-3">"The supportive environment and modern facilities at Nyabikoni SS made all the difference in my education."</p>
            <h6 class="fw-bold mb-0">Elly T.</h6>
            <span class="text-secondary small">Parent</span>
          </div>
        </div>
        <div class="carousel-item">
          <div class="card border-0 shadow-sm h-100 p-4 text-center mx-auto" style="max-width: 400px;">
            <img src="nyabzgallery/LINET.jpg" alt="Testimonial 3" class="rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; object-fit: cover;">
            <p class="text-muted mb-3">"I am proud to be part of a school that values both academic and personal growth."</p>
            <h6 class="fw-bold mb-0">Linet K.</h6>
            <span class="text-secondary small">Student</span>
          </div>
        </div>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
      <div class="carousel-indicators mt-4">
        <button type="button" data-bs-target="#testimonialsCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Testimonial 1"></button>
        <button type="button" data-bs-target="#testimonialsCarousel" data-bs-slide-to="1" aria-label="Testimonial 2"></button>
        <button type="button" data-bs-target="#testimonialsCarousel" data-bs-slide-to="2" aria-label="Testimonial 3"></button>
      </div>
    </div>
  </div>
</section>

<!-- Call to Action Section -->
<section class="about-cta-section py-5 text-center" style="background: #2c5aa0; color: #fff;">
  <div class="container" data-aos="zoom-in">
    <h2 class="display-5 fw-bold mb-3" style="font-family: 'Bungee', cursive;">Ready to Join Nyabikoni SS?</h2>
    <p class="fs-5 mb-4">Contact us today to learn more about admissions, programs, and how you can become part of our vibrant community.</p>
    <a href="contactus.php" class="btn btn-lg" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff; border-radius: 30px; font-weight: 700; padding: 14px 40px; font-size: 1.2rem; text-transform: uppercase; letter-spacing: 1.2px; box-shadow: 0 4px 18px rgba(16, 185, 129, 0.18);">Contact Us <i class="fas fa-envelope ms-2"></i></a>
  </div>
</section>

<!-- Section Divider SVG (bottom) -->
<div style="line-height:0;">
  <svg viewBox="0 0 1440 80" width="100%" height="80" preserveAspectRatio="none" style="display:block;"><path fill="#fff" d="M0,64L48,58.7C96,53,192,43,288,53.3C384,64,480,96,576,101.3C672,107,768,85,864,80C960,75,1056,85,1152,80C1248,75,1344,53,1392,42.7L1440,32L1440,80L1392,80C1344,80,1248,80,1152,80C1056,80,960,80,864,80C768,80,672,80,576,80C480,80,384,80,288,80C192,80,96,80,48,80L0,80Z"></path></svg>
</div>


<!-- Accordion Section with Logo on Side -->
<section style="background: linear-gradient(to right, #f0f8ff, #e6f2ff); padding: 60px 20px;">
  <div class="container" data-aos="fade-up">
    <div class="text-center mb-5">
      <h2 style="font-family: 'Bungee', sans-serif; font-size: 2.5rem;">Our Key Facilities</h2>
      <p class="text-muted">Discover the excellent infrastructure we provide for holistic education.</p>
    </div>

    <div class="row align-items-center justify-content-center">
      <!-- Accordion Column -->
      <div class="col-lg-7">
        <div class="accordion accordion-flush" id="accordionFlushExample">
          <!-- Classrooms -->
          <div class="accordion-item mb-3 shadow-sm rounded" data-aos="fade-up" data-aos-delay="100">
            <h2 class="accordion-header" id="flush-headingOne">
              <button class="accordion-button collapsed rounded" type="button" data-bs-toggle="collapse"
                data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                <i class="fa fa-building me-2 text-primary"></i> Well Built Classrooms
              </button>
            </h2>
            <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne"
              data-bs-parent="#accordionFlushExample">
              <div class="accordion-body bg-white p-4">
                Our classrooms are well equipped and designed for an engaging and comfortable learning experience.
              </div>
            </div>
          </div>

          <!-- Health Center -->
          <div class="accordion-item mb-3 shadow-sm rounded" data-aos="fade-up" data-aos-delay="200">
            <h2 class="accordion-header" id="flush-headingTwo">
              <button class="accordion-button collapsed rounded" type="button" data-bs-toggle="collapse"
                data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                <i class="fa fa-heartbeat me-2 text-danger"></i> Health Center
              </button>
            </h2>
            <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo"
              data-bs-parent="#accordionFlushExample">
              <div class="accordion-body bg-white p-4">
                Student wellness is a priority — our health center ensures all students receive quick and effective medical attention.
              </div>
            </div>
          </div>

          <!-- Computer Lab -->
          <div class="accordion-item mb-3 shadow-sm rounded" data-aos="fade-up" data-aos-delay="300">
            <h2 class="accordion-header" id="flush-headingThree">
              <button class="accordion-button collapsed rounded" type="button" data-bs-toggle="collapse"
                data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                <i class="fa fa-laptop-code me-2 text-success"></i> Computer Lab
              </button>
            </h2>
            <div id="flush-collapseThree" class="accordion-collapse collapse" aria-labelledby="flush-headingThree"
              data-bs-parent="#accordionFlushExample">
              <div class="accordion-body bg-white p-4">
                Our well-resourced Computer Lab prepares students with essential digital skills to thrive in today's tech-driven world.
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Logo Column -->
      <div class="col-lg-5 d-flex justify-content-center align-items-center" data-aos="fade-left" data-aos-delay="400">
        <a href="#">
          <img src="nyabzgallery/nyabz logo.png" alt="Nyabikoni Logo" style="max-width: 100%; max-height: 200px; object-fit: contain;">
        </a>
      </div>
    </div>
  </div>
</section>

<?php include 'modern-footer.html'; ?>

<!-- -------script----- -->

<!-- Navbar functionality handled by navbar.css -->

    <script>
        function scrollToTop() {
    window.scrollTo({
        top: 0, // Scroll to the top of the page
        behavior: 'smooth' // Smooth scrolling animation
    });
}
    </script>

<script>
  function toggleFab() {
    document.querySelector('.fab-container').classList.toggle('open');
  }
</script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const counters = document.querySelectorAll(".counter");

            counters.forEach(counter => {
                const updateCounter = () => {
                    const target = +counter.getAttribute("data-target");
                    const count = +counter.innerText;
                    const increment = target / 100; // Adjust speed

                    if (count < target) {
                        counter.innerText = Math.ceil(count + increment);
                        setTimeout(updateCounter, 30); // Adjust speed timing
                    } else {
                        counter.innerText = target;
                    }
                };
                updateCounter();
            });
        });
    </script>

<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>

<script>
  AOS.init({
    duration: 800,
    once: true, // animation only happens once when scrolled into view
  });
  
</script>


<!-- Scripts -->
<script>
  function scrollToTop() {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  }

  window.addEventListener('scroll', function () {
    var arrow = document.getElementById('up-arrow');
    arrow.style.display = window.scrollY > 300 ? 'block' : 'none';
  });

  new WOW().init();
</script>

<!-- Navbar scroll behavior handled by navbar.css -->



<!-- WOW.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
<script>
  new WOW().init();
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>

<style>
/* Our Story Timeline */
.timeline {
  position: relative;
  padding-left: 32px;
  margin-top: 1.5rem;
}
.timeline li {
  position: relative;
  margin-bottom: 1.2rem;
  font-size: 1.08rem;
  color: #374151;
  padding-left: 16px;
}
.timeline li:before {
  content: '\f111';
  font-family: 'Font Awesome 6 Free';
  font-weight: 900;
  position: absolute;
  left: -32px;
  top: 2px;
  color: #10b981;
  font-size: 0.9em;
}
.timeline:after {
  content: '';
  position: absolute;
  left: -24px;
  top: 0;
  width: 2px;
  height: 100%;
  background: linear-gradient(to bottom, #10b981 0%, #2c5aa0 100%);
  border-radius: 1px;
}
.our-story-section img {
  border-radius: 18px;
  box-shadow: 0 8px 32px rgba(44,90,160,0.13);
  transition: transform 0.3s;
}
.our-story-section img:hover {
  transform: scale(1.03);
}
.our-story-section .card {
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 4px 18px rgba(44,90,160,0.07);
}

/* Leadership Team */
.leadership-section .card {
  border-radius: 18px;
  box-shadow: 0 4px 18px rgba(44,90,160,0.10);
  transition: transform 0.2s, box-shadow 0.2s;
}
.leadership-section .card:hover {
  transform: translateY(-8px) scale(1.03);
  box-shadow: 0 8px 32px rgba(44,90,160,0.18);
}
.leadership-section .card-img-top {
  border: 4px solid #10b981;
  box-shadow: 0 2px 8px rgba(44,90,160,0.10);
  width: 120px;
  height: 120px;
  object-fit: cover;
  margin-bottom: 0.5rem;
}
.leadership-section .card-title {
  font-weight: 700;
  color: #1e3a8a;
}
.leadership-section .card-text {
  font-size: 0.98rem;
}

/* Why Choose Us */
.why-choose-section .card {
  background: #f8fafc;
  border-radius: 18px;
  box-shadow: 0 2px 12px rgba(44,90,160,0.07);
  transition: transform 0.2s, box-shadow 0.2s;
  border: none;
}
.why-choose-section .card:hover {
  transform: scale(1.04);
  box-shadow: 0 8px 24px rgba(16,185,129,0.13);
}
.why-choose-section i {
  background: #e0f7f1;
  border-radius: 50%;
  padding: 16px;
  margin-bottom: 1rem;
  color: #10b981;
  box-shadow: 0 2px 8px rgba(16,185,129,0.10);
}
.why-choose-section .fw-bold {
  color: #2c5aa0;
}

/* Testimonials */
.testimonials-section {
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
}
.testimonials-section .card {
  border-radius: 18px;
  box-shadow: 0 4px 18px rgba(44,90,160,0.10);
  border: none;
  position: relative;
}
.testimonials-section .card:before {
  content: '\f10d';
  font-family: 'Font Awesome 6 Free';
  font-weight: 900;
  color: #10b981;
  font-size: 2.2rem;
  position: absolute;
  left: 24px;
  top: 18px;
  opacity: 0.15;
  z-index: 0;
}
.testimonials-section .rounded-circle {
  border: 3px solid #10b981;
  box-shadow: 0 2px 8px rgba(44,90,160,0.10);
}
.testimonials-section .fw-bold {
  color: #1e3a8a;
}

/* Call to Action */
.about-cta-section {
  background: linear-gradient(120deg, #2c5aa0 0%, #10b981 100%);
  color: #fff;
  position: relative;
  overflow: hidden;
}
.about-cta-section:before {
  content: '';
  position: absolute;
  top: 0; left: 0; width: 100%; height: 100%;
  background: linear-gradient(135deg, rgba(44,90,160,0.18) 0%, rgba(16,185,129,0.13) 100%);
  z-index: 0;
}
.about-cta-section .container {
  position: relative;
  z-index: 1;
}
.about-cta-section .btn {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: #fff;
  border-radius: 30px;
  font-weight: 700;
  padding: 16px 44px;
  font-size: 1.2rem;
  text-transform: uppercase;
  letter-spacing: 1.2px;
  box-shadow: 0 4px 18px rgba(16, 185, 129, 0.18);
  transition: box-shadow 0.2s, transform 0.2s;
}
.about-cta-section .btn:hover,
.about-cta-section .btn:focus {
  box-shadow: 0 8px 32px rgba(16, 185, 129, 0.28), 0 0 16px 4px #10b98144;
  transform: translateY(-2px) scale(1.04);
  color: #fff;
}
</style>




 