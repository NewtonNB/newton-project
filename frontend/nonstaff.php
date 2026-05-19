<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teaching Staff Gallery</title>
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="modern-footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Bungee&family=Press+Start+2P&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bungee&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Press+Start+2P&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css"
  rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<style>
@import url('https:fonts.googleapis.com/css?family=Poppins:200,300,400,500,600,700,800,900&display=swap');

/* Modern Hero Section for Non-Teaching Staff */
.nonstaff-hero {
    position: relative;
    min-height: 54vh;
    width: 100%;
    background-image: linear-gradient(rgba(44,90,160,0.7),rgba(16,185,129,0.7)), url('nyabzgallery/current.jpg');
    background-position: center;
    background-size: cover;
    background-attachment: fixed;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 80px;
    padding-bottom: 0;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(44,90,160,0.10);
    border-radius: 0 0 24px 24px;
    margin-bottom: 2.5rem;
}

.nonstaff-hero-overlay {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: linear-gradient(135deg, rgba(44,90,160,0.7) 0%, rgba(16,185,129,0.7) 100%);
    z-index: 1;
    border-radius: 0 0 24px 24px;
}

.nonstaff-hero .text-box {
    position: relative;
    z-index: 2;
    text-align: center;
    padding: 60px 20px 30px 20px;
    color: #fff;
    width: 100%;
    max-width: 700px;
    margin: 0 auto;
}

.nonstaff-hero .page-title {
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

.divider {
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, #2c5aa0 0%, #10b981 100%);
    border-radius: 2px;
    margin: 0 auto 2.5rem auto;
    opacity: 0.18;
}

body {
  font-family: 'Poppins', 'Roboto', Arial, sans-serif;
  background: linear-gradient(135deg, #f8f9fa 60%, #e3f0ff 100%);
  color: #222;
  margin: 0;
  padding: 0;
  min-height: 100vh;
}
.gallery-section {
  padding: 2.5rem 0 3rem 0;
}
.container.gallery-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 2.2rem;
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.07);
  padding: 2.5rem 1.2rem;
  margin: 0 auto;
  max-width: 1200px;
}

.staff-item {
    background: #f9f9f9;
    border-radius: 18px;
    box-shadow: 0 2px 12px rgba(22,111,38,0.10);
    text-align: center;
    padding: 2.2rem 1.2rem 1.5rem 1.2rem;
    width: 230px;
    transition: transform 0.22s, box-shadow 0.22s, background 0.22s;
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 0.5rem;
    border: 1.5px solid #e0e0e0;
}
.staff-item img {
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
.staff-item h3 {
    font-size: 1.13rem;
    color: #166f26;
    margin: 0.5rem 0 0.2rem 0;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-shadow: 0 1px 4px rgba(44,90,160,0.04);
}
.staff-item p {
    font-size: 1.01rem;
    color: #555;
    margin: 0;
    font-weight: 500;
    letter-spacing: 0.1px;
}
.staff-item:hover {
    transform: translateY(-10px) scale(1.045);
    box-shadow: 0 10px 32px rgba(22,111,38,0.16);
    background: #f4fff7;
    border: 1.5px solid #10b981;
}

@media (max-width: 900px) {
    .container.gallery-container {
        gap: 1.2rem;
        padding: 1.2rem 0.5rem;
    }
    .staff-item {
        width: 45vw;
        min-width: 170px;
        max-width: 260px;
    }
}
@media (max-width: 600px) {
    .container.gallery-container {
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        padding: 0.5rem 0.2rem;
    }
    .staff-item {
        width: 90vw;
        min-width: 140px;
        max-width: 98vw;
        padding: 1.2rem 0.5rem;
    }
    .page-title {
        font-size: 2rem;
    }
    .nonstaff-hero {
        border-radius: 0 0 12px 12px;
    }
}

</style>
</head>
<body>

<?php require_once '../shared/config.php'; ?>
<?php include 'navbar.php'; ?>

<main>
    <section class="header nonstaff-hero" aria-label="Non-Teaching Staff Hero">
        <div class="nonstaff-hero-overlay animate__animated animate__fadeInDown" style="animation-delay:0.05s;"></div>
        <div class="text-box animate__animated animate__fadeInDown">
            <h1 class="page-title animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">Non Teaching Staff</h1>
            <p class="hero-subtitle animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
                Meet our dedicated non-teaching staff who support the smooth running of Nyabikoni Secondary School and contribute to our culture of excellence.
            </p>
        </div>
    </section>

    <section class="gallery-section animate__animated animate__fadeInUp" style="animation-delay:0.2s;">
        <div class="divider"></div>
        <div class="container gallery-container">
            <!-- Staff Cards Start -->
            <?php
            $staff = [
                ["img" => "nyabzgallery/burser1.png", "name" => "MS.TUMUKUNDE AGATHA", "role" => "Burser"],
                ["img" => "nyabzgallery/burser2.jpg", "name" => "MS. MUSIIMENTA ELIZABETH", "role" => "Assistant Bursar"],
                ["img" => "nyabzgallery/burser3.jpg", "name" => "MS. OWOMUHANGI JEMMA", "role" => "Assistant Bursar"],
                ["img" => "nyabzgallery/secretary.jpg", "name" => "MS. KYOBUTUNGI CONFIDENCE", "role" => "Secretary"],
                ["img" => "nyabzgallery/librrian.png", "name" => "MS. AMPEIRE NANYRS", "role" => "Librarian"],
                ["img" => "nyabzgallery/LINET.jpg", "name" => "MS. KYAGABA LINET", "role" => "Office Assistant"],
                ["img" => "nyabzgallery/lab attendeant.png", "name" => "MR. NATURINDA CALLIST", "role" => "Lab Assistant"],
                ["img" => "nyabzgallery/cook7.png", "name" => "MR. KATWESIGYE OBED", "role" => "Cook"],
                ["img" => "nyabzgallery/cook6.png", "name" => "MR. TUKAMUSHABA BRUNO", "role" => "Head Cook"],
                ["img" => "nyabzgallery/cleaner.png", "name" => "MS. OWOMUGISHA DENIS", "role" => "Cleaner"],
                ["img" => "nyabzgallery/cook5.png", "name" => "MR. NDAHEBIRE GASTON", "role" => "Cook"],
                ["img" => "nyabzgallery/cook4.png", "name" => "MR. KWERI HILLARY", "role" => "Cook"],
                ["img" => "nyabzgallery/cook3.png", "name" => "MR. MBAGAYA POSIANO", "role" => "Cook"],
                ["img" => "nyabzgallery/cook2.png", "name" => "MR. ARIHO PETER", "role" => "Cook"],
                ["img" => "nyabzgallery/cook1.png", "name" => "MR. TWEHEYO DAVIS", "role" => "Cook"],
                ["img" => "nyabzgallery/OWOMUHANGI JEMMA.png", "name" => "MR. TWEHEYO DAVIS", "role" => "Gate Keeper"],
            ];
            $aos_types = ["fade-up", "fade-right", "fade-left"];
            $delay = 100;
            foreach ($staff as $i => $s) {
                $aos = $aos_types[$i % count($aos_types)];
                echo '<div class="staff-item" data-aos="'.$aos.'" data-aos-delay="'.$delay.'">';
                echo '<img src="'.$s["img"].'" alt="'.$s["name"].' - '.$s["role"].'">';
                echo '<h3>'.$s["name"].'</h3>';
                echo '<p>'.$s["role"].'</p>';
                echo '</div>';
                $delay += 100;
                if ($delay > 800) $delay = 100;
            }
            ?>
            <!-- Staff Cards End -->
        </div>
    </section>
</main>

<?php include 'modern-footer.html'; ?>


    <script>
        
    function showMenu() {
        document.getElementById('navlinks').style.right = "0";
    }

    function hideMenu() {
        document.getElementById('navlinks').style.right = "-200px";
    }


    </script>
    <!-- WOW.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
<script>
  new WOW().init();
</script>

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
      const fabOptions = document.querySelector('.fab-options');
      fabOptions.classList.toggle('show');
    }
    </script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Add scroll event listener with throttling for smoother animation
    let lastScrollTop = 0;
    const scrollThreshold = 100;
    let ticking = false;

    window.addEventListener('scroll', function() {
        if (!ticking) {
            window.requestAnimationFrame(function() {
                const topbar = document.querySelector('.topbar');
                const navbar = document.querySelector('.navbar');
                const scrollPosition = window.scrollY;
                
                // Determine scroll direction
                const scrollDirection = scrollPosition > lastScrollTop ? 'down' : 'up';
                
                if (scrollPosition > scrollThreshold) {
                    if (scrollDirection === 'down') {
                        topbar.classList.add('hide');
                        navbar.classList.add('show');
                    } else {
                        topbar.classList.remove('hide');
                        navbar.classList.remove('show');
                    }
                } else {
                    topbar.classList.remove('hide');
                    navbar.classList.remove('show');
                }
                
                lastScrollTop = scrollPosition;
                ticking = false;
            });
            
            ticking = true;
        }
    });
</script>
<!-- Add AOS JS and initialize at the end of the body -->
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 900,
    easing: 'ease-in-out',
    once: true
  });
</script>
</body>
</html>
