// Gallery-specific JavaScript for Nyabikoni Secondary School

document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true
    });

    // Initialize Lightbox
    lightbox.option({
        'resizeDuration': 200,
        'wrapAround': true,
        'albumLabel': 'Image %1 of %2',
        'fadeDuration': 300,
        'imageFadeDuration': 300
    });

    // Gallery filtering functionality
    const filterButtons = document.querySelectorAll('.filter-btn');
    const galleryItems = document.querySelectorAll('.gallery-item');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter gallery items
            galleryItems.forEach(item => {
                const category = item.getAttribute('data-category');
                
                if (filter === 'all' || category === filter) {
                    item.style.display = 'block';
                    setTimeout(() => {
                        item.classList.add('show');
                    }, 100);
                } else {
                    item.classList.remove('show');
                    setTimeout(() => {
                        item.style.display = 'none';
                    }, 300);
                }
            });
        });
    });

    // Counter animation for stats
    const counters = document.querySelectorAll('.stat-number');
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                const target = parseInt(counter.getAttribute('data-count'));
                const duration = 2000; // 2 seconds
                const increment = target / (duration / 16); // 60fps
                let current = 0;

                const updateCounter = () => {
                    current += increment;
                    if (current < target) {
                        counter.textContent = Math.floor(current);
                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.textContent = target;
                    }
                };

                updateCounter();
                observer.unobserve(counter);
            }
        });
    }, observerOptions);

    counters.forEach(counter => {
        observer.observe(counter);
    });

    // Like functionality
    window.toggleLike = function(button) {
        const icon = button.querySelector('i');
        const countSpan = button.querySelector('.like-count');
        let count = parseInt(countSpan.textContent);
        
        if (button.classList.contains('liked')) {
            button.classList.remove('liked');
            icon.classList.remove('fas');
            icon.classList.add('far');
            count--;
        } else {
            button.classList.add('liked');
            icon.classList.remove('far');
            icon.classList.add('fas');
            count++;
        }
        
        countSpan.textContent = count;
    };

    // Download functionality
    window.downloadImage = function(imageSrc, imageName) {
        const link = document.createElement('a');
        link.href = imageSrc;
        link.download = imageName + '.jpg';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Show download notification
        showNotification('Image downloaded successfully!', 'success');
    };

    // Share functionality
    window.shareImage = function(imageSrc, imageName) {
        if (navigator.share) {
            navigator.share({
                title: 'Nyabikoni Secondary School Gallery',
                text: `Check out this photo: ${imageName}`,
                url: window.location.href
            });
        } else {
            // Fallback for browsers that don't support Web Share API
            const url = window.location.href;
            const text = `Check out this photo from Nyabikoni Secondary School: ${imageName}`;
            
            // Create temporary textarea to copy to clipboard
            const textarea = document.createElement('textarea');
            textarea.value = `${text}\n${url}`;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            
            showNotification('Link copied to clipboard!', 'info');
        }
    };

    // Notification system
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        // Add notification styles
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 10000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            max-width: 300px;
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    // Lazy loading for gallery images
    const images = document.querySelectorAll('.gallery-image img');
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.src; // Trigger load
                img.classList.add('loaded');
                imageObserver.unobserve(img);
            }
        });
    });

    images.forEach(img => {
        imageObserver.observe(img);
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Parallax effect for hero section
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const hero = document.querySelector('.gallery-hero');
        if (hero) {
            const rate = scrolled * -0.5;
            hero.style.transform = `translateY(${rate}px)`;
        }
    });

    // Gallery search functionality
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.placeholder = 'Search photos...';
    searchInput.className = 'gallery-search';
    searchInput.style.cssText = `
        position: absolute;
        top: 20px;
        right: 20px;
        padding: 0.5rem 1rem;
        border: 2px solid #e1e5e9;
        border-radius: 25px;
        font-size: 0.9rem;
        outline: none;
        transition: all 0.3s ease;
        z-index: 10;
    `;

    const filterSection = document.querySelector('.gallery-filter-section');
    if (filterSection) {
        filterSection.style.position = 'relative';
        filterSection.appendChild(searchInput);

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            galleryItems.forEach(item => {
                const title = item.querySelector('h4').textContent.toLowerCase();
                const category = item.querySelector('.gallery-category').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || category.includes(searchTerm)) {
                    item.style.display = 'block';
                    item.classList.add('show');
                } else {
                    item.classList.remove('show');
                    setTimeout(() => {
                        item.style.display = 'none';
                    }, 300);
                }
            });
        });

        // Search input focus effects
        searchInput.addEventListener('focus', function() {
            this.style.borderColor = '#667eea';
            this.style.boxShadow = '0 0 0 3px rgba(102, 126, 234, 0.1)';
        });

        searchInput.addEventListener('blur', function() {
            this.style.borderColor = '#e1e5e9';
            this.style.boxShadow = 'none';
        });
    }

    // Keyboard navigation for gallery
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            // Close lightbox if open
            const lightbox = document.querySelector('.lb-outerContainer');
            if (lightbox) {
                const closeBtn = document.querySelector('.lb-close');
                if (closeBtn) closeBtn.click();
            }
        }
    });

    // Add loading animation to gallery items
    galleryItems.forEach((item, index) => {
        item.style.animationDelay = `${index * 0.1}s`;
        item.classList.add('loading');
        
        setTimeout(() => {
            item.classList.remove('loading');
            item.classList.add('loaded');
        }, index * 100);
    });

    // Initialize masonry-like layout
    function initMasonry() {
        const grid = document.querySelector('#gallery-grid');
        if (grid) {
            const items = grid.querySelectorAll('.gallery-item');
            let maxHeight = 0;
            
            items.forEach(item => {
                const height = item.offsetHeight;
                if (height > maxHeight) {
                    maxHeight = height;
                }
            });
            
            // Apply equal height to items in the same row
            items.forEach(item => {
                item.style.height = maxHeight + 'px';
            });
        }
    }

    // Call masonry after images load
    window.addEventListener('load', initMasonry);
    
    // Recalculate on window resize
    window.addEventListener('resize', initMasonry);

    // Filtering
    document.querySelectorAll('.filter-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const filter = btn.getAttribute('data-filter');
            document.querySelectorAll('.gallery-item').forEach(function(item) {
                if (filter === 'all' || item.getAttribute('data-category') === filter) {
                    item.style.display = '';
                    item.style.opacity = 0;
                    setTimeout(() => { item.style.opacity = 1; }, 50);
                } else {
                    item.style.opacity = 0;
                    setTimeout(() => { item.style.display = 'none'; }, 200);
                }
            });
        });
    });
    // Hover effect
    document.querySelectorAll('.gallery-img').forEach(function(img) {
        img.addEventListener('mouseenter', function() {
            img.style.transform = 'scale(1.04)';
            img.style.boxShadow = '0 8px 24px rgba(102,126,234,0.18)';
            img.style.transition = 'all 0.2s';
        });
        img.addEventListener('mouseleave', function() {
            img.style.transform = '';
            img.style.boxShadow = '';
        });
    });
});

// Additional utility functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Export functions for global access
window.galleryUtils = {
    showNotification: function(message, type) {
        // Implementation moved to main function
    },
    debounce: debounce
}; 