<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery</title>
    <!-- <link rel="stylesheet" href="contactus.css"> -->
    <link rel="stylesheet" href="modern-footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Bungee&family=Press+Start+2P&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bungee&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Press+Start+2P&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="navbar.css">
    <style>
:root {
  --gallery-bg: #f8f9fa;
  --gallery-primary: #1a237e;
  --gallery-secondary: #3949ab;
  --gallery-accent: #42a5f5;
  --gallery-card-bg: #fff;
  --gallery-card-radius: 18px;
  --gallery-card-shadow: 0 6px 24px rgba(26,35,126,0.10);
  --gallery-card-hover-shadow: 0 12px 32px rgba(26,35,126,0.18);
  --gallery-btn-bg: var(--gallery-primary);
  --gallery-btn-hover-bg: #0d133d;
  --gallery-btn-color: #fff;
  --gallery-spacing: 2.5rem;
  --gallery-font: 'Poppins', 'Segoe UI', Arial, sans-serif;
}
body {
  background: var(--gallery-bg);
  font-family: var(--gallery-font);
}
.gallery-hero {
    position: relative;
    width: 100%;
  min-height: 65vh;
  background: linear-gradient(120deg, rgba(26,35,126,0.82) 0%, rgba(26,35,126,0.55) 100%), url('nyabzgallery/current.jpg') center/cover no-repeat;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: var(--gallery-spacing);
  box-shadow: 0 8px 32px rgba(26,35,126,0.10);
  overflow: hidden;
}
.gallery-hero::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(120deg, rgba(26,35,126,0.45) 0%, rgba(66,165,245,0.10) 100%);
  z-index: 1;
}
.gallery-hero h1 {
  color: #fff;
  font-family: 'Bungee', cursive;
  font-size: 3.5rem;
  text-shadow: 0 4px 16px rgba(0,0,0,0.25);
  letter-spacing: 2px;
  margin: 0;
  position: relative;
  z-index: 2;
  animation: fadeInDown 1.1s cubic-bezier(.77,0,.18,1) both;
}
@keyframes fadeInDown {
  0% { opacity: 0; transform: translateY(-40px); }
  100% { opacity: 1; transform: translateY(0); }
}
.gallery-container {
  max-width: 1240px;
  margin: 0 auto var(--gallery-spacing) auto;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
  gap: var(--gallery-spacing);
  padding: 0 1.2rem;
}
.gallery-card {
  background: var(--gallery-card-bg);
  border-radius: var(--gallery-card-radius);
  box-shadow: var(--gallery-card-shadow);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  transition: box-shadow 0.35s, transform 0.35s;
  opacity: 0;
  animation: fadeInCard 0.9s cubic-bezier(.77,0,.18,1) forwards;
        }
@keyframes fadeInCard {
  0% { opacity: 0; transform: translateY(40px) scale(0.97); }
  100% { opacity: 1; transform: translateY(0) scale(1); }
}
        .gallery-card:hover {
  box-shadow: var(--gallery-card-hover-shadow);
  transform: translateY(-10px) scale(1.035);
        }
        .gallery-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
  transition: transform 0.35s cubic-bezier(.77,0,.18,1);
  border-bottom: 3px solid var(--gallery-accent);
        }
.gallery-card:hover img {
  transform: scale(1.07);
  border-bottom: 3px solid var(--gallery-primary);
            }
.gallery-card .card-content {
  padding: 1.5rem 1.1rem 1.7rem 1.1rem;
  flex: 1 1 auto;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: space-between;
}
.gallery-card h3 {
  color: var(--gallery-primary);
  font-size: 1.25rem;
  margin-bottom: 1.1rem;
  font-weight: 700;
  text-align: center;
  letter-spacing: 1px;
}
.gallery-card button {
  background: var(--gallery-btn-bg);
  color: var(--gallery-btn-color);
  border: none;
  padding: 0.85rem 2.2rem;
  border-radius: 8px;
  font-size: 1.08rem;
  font-weight: 600;
  cursor: pointer;
  box-shadow: 0 2px 12px rgba(26,35,126,0.10);
    position: relative;
  overflow: hidden;
  transition: background 0.22s, box-shadow 0.22s, transform 0.18s;
}
.gallery-card button::after {
  content: '';
    position: absolute;
  left: 50%;
  top: 50%;
  width: 0;
  height: 0;
  background: rgba(255,255,255,0.25);
  border-radius: 100%;
  transform: translate(-50%, -50%);
  transition: width 0.4s cubic-bezier(.77,0,.18,1), height 0.4s cubic-bezier(.77,0,.18,1);
  z-index: 1;
  }
.gallery-card button:hover {
  background: var(--gallery-btn-hover-bg);
  box-shadow: 0 6px 24px rgba(26,35,126,0.18);
  transform: scale(1.04);
    }
.gallery-card button:hover::after {
  width: 220%;
  height: 500%;
    }
.gallery-card button span {
  position: relative;
  z-index: 2;
}
@media (max-width: 900px) {
  .gallery-hero h1 {
    font-size: 2.2rem;
  }
  .gallery-container {
    gap: 1.2rem;
    }
  .gallery-card img {
    height: 140px;
  }
    }
@media (max-width: 600px) {
  .gallery-hero {
    min-height: 38vh;
  }
  .gallery-hero h1 {
    font-size: 1.3rem;
}
  .gallery-container {
    gap: 0.7rem;
    padding: 0 0.3rem;
}
  .gallery-card img {
    height: 90px;
}
  .gallery-card .card-content {
    padding: 0.7rem 0.5rem 1rem 0.5rem;
}
}
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<section class="gallery-hero">
  <h1>School Gallery</h1>
    </section>
    
    <div class="gallery-container">
        <!-- Existing Images -->
        <div class="gallery-card">
            <img src="nyabzgallery/harvad3.jpg" alt="General Album">
            <div class="card-content">
                <h3>General Album</h3>
                <a href="viewgallery.php"><button>VIEW NOW</button></a>
            </div>
        </div>
        <div class="gallery-card">
            <img src="nyabzgallery/esternblock - Copy.jpg" alt="Eastern Campus Album">
            <div class="card-content">
                <h3>Eastern Campus Album</h3>
                <a href="viewgallery.php"><button>VIEW NOW</button></a>
            </div>
        </div>
        <div class="gallery-card">
            <img src="nyabzgallery/SCHOOL MAIN HALL.jpg" alt="Main Campus Album">
            <div class="card-content">
                <h3>Main Campus Album</h3>
                <a href="viewgallery.php"><button>VIEW NOW</button></a>
            </div>
        </div>

        <!-- Additional Images -->
        <div class="gallery-card">
            <img src="nyabzgallery/current.jpg" alt="Current">
            <div class="card-content">
                <h3>Current</h3>
                <a href="viewgallery.php"><button>VIEW NOW</button></a>
            </div>
        </div>
        <div class="gallery-card">
            <img src="nyabzgallery/admin2 - Copy.JPG" alt="Admin">
            <div class="card-content">
                <h3>Admin</h3>
                <a href="viewgallery.php"><button>VIEW NOW</button></a>
            </div>
        </div>
        <div class="gallery-card">
            <img src="nyabzgallery/chapel - Copy.JPG" alt="Chapel">
            <div class="card-content">
                <h3>Chapel</h3>
                <a href="viewgallery.php"><button>VIEW NOW</button></a>
            </div>
        </div>
        <div class="gallery-card">
            <img src="nyabzgallery/boyswing - Copy.JPG" alt="Boys Wing">
            <div class="card-content">
                <h3>Boys Wing</h3>
                <a href="viewgallery.php"><button>VIEW NOW</button></a>
            </div>
        </div>
        <div class="gallery-card">
            <img src="nyabzgallery/havard - Copy.jpg" alt="Harvard View">
            <div class="card-content">
                <h3>Harvard View</h3>
                <a href="viewgallery.php"><button>VIEW NOW</button></a>
            </div>
        </div>
        <div class="gallery-card">
            <img src="nyabzgallery/staff.JPG" alt="Staff">
            <div class="card-content">
                <h3>Staff</h3>
                <a href="viewgallery.php"><button>VIEW NOW</button></a>
            </div>
        </div>
        <div class="gallery-card">
            <img src="nyabzgallery/sports.JPG" alt="Sports">
            <div class="card-content">
                <h3>Sports</h3>
                <a href="viewgallery.php"><button>VIEW NOW</button></a>
            </div>
        </div>
        <div class="gallery-card">
            <img src="nyabzgallery/library.JPG" alt="New Image 4">
            <div class="card-content">
                <h3>Library</h3>
                <a href="viewgallery.php"><button>VIEW NOW</button></a>
            </div>
        </div>
        <div class="gallery-card">
            <img src="nyabzgallery/chapel3.jpg" alt="New Image 5">
            <div class="card-content">
                <h3>Chapel</h3>
                <a href="viewgallery.php"><button>VIEW NOW</button></a>
            </div>
        </div>
        <div class="gallery-card">
            <img src="nyabzgallery/boyswing - Copy.JPG" alt="New Image 6">
            <div class="card-content">
                <h3>Boys Domitory</h3>
                <a href="viewgallery.php"><button>VIEW NOW</button></a>
            </div>
        </div>
        <div class="gallery-card">
            <img src="nyabzgallery/school life.JPG" alt="New Image 6">
            <div class="card-content">
                <h3>Sports Day</h3>
                <a href="viewgallery.php"><button>VIEW NOW</button></a>
            </div>
        </div>
        <div class="gallery-card">
            <img src="nyabzgallery/Alevelstudents.jpg" alt="New Image 6">
            <div class="card-content">
                <h3>Students</h3>
                <a href="viewgallery.php"><button>VIEW NOW</button></a>
            </div>
        </div>
    </div>

<?php include 'modern-footer.html'; ?>



     <!-- ------javascript for toggle menu--------- -->
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
  document.addEventListener("DOMContentLoaded", function () {
    let lastScrollTop = 0;
    const topbar = document.querySelector(".topbar");
    const navbar = document.querySelector(".navbar");

    if (!topbar || !navbar) {
      console.error("Topbar or Navbar not found.");
      return;
    }

    window.addEventListener("scroll", function () {
      const currentScroll = window.pageYOffset || document.documentElement.scrollTop;

      if (currentScroll > lastScrollTop && currentScroll > 20) {
        // Scrolling down
        topbar.classList.add("hide");
        navbar.classList.add("fixed-top");
      } else {
        // Scrolling up
        topbar.classList.remove("hide");
        navbar.classList.remove("fixed-top");
      }

      lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
    });
  });
</script>


</body>
</html>
