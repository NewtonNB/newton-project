 const swiper = new Swiper('.slider-wrapper', {  // Use class selector with dot (.)
    loop: true,
    grabCursor: true,  // Corrected property name
    spaceBetween: 30,

    // If we need pagination
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
        dynamicBullets: true
    },

    // Navigation arrows
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },

    // Breakpoints for responsive behavior
    breakpoints: {
        0: {
            slidesPerView: 1
        },
        768: {
            slidesPerView: 2
        },
        1024: {
            slidesPerView: 3
        },
    }
});


// Mobile dropdown toggle logic
function setupMobileDropdowns() {
  if (window.innerWidth <= 700) {
    document.querySelectorAll('.dropdown > a').forEach(function(link) {
      link.onclick = function(e) {
        var parent = link.parentElement;
        if (parent.classList.contains('dropdown')) {
          e.preventDefault();
          // Close other open dropdowns
          document.querySelectorAll('.dropdown.open').forEach(function(dd) {
            if (dd !== parent) dd.classList.remove('open');
          });
          parent.classList.toggle('open');
        }
      };
    });
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
      if (!e.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown.open').forEach(function(dd) {
          dd.classList.remove('open');
        });
      }
    });
  } else {
    // Remove .open class on resize to desktop
    document.querySelectorAll('.dropdown.open').forEach(function(dd) {
      dd.classList.remove('open');
    });
  }
}

window.addEventListener('DOMContentLoaded', setupMobileDropdowns);
window.addEventListener('resize', setupMobileDropdowns);


