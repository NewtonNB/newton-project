/**
 * js/includes.js
 * - Loads navbar.html, admin sidebar, footer, delete modal
 * - Session-guards admin pages
 * - Loads page-data.js for dynamic content
 */

(function () {
  'use strict';

  const ADMIN_PAGES = [
    'dashboard', 'view_student', 'view_teacher', 'announcements',
    'attendance', 'manage_classes', 'manage_admins', 'manage_subjects',
    'manage_gallery', 'dynamic_gallery_admin', 'exam_schedule', 'settings',
    'user', 'studenthome', 'view_event_registrations', 'view_payment_details',
    'report_cards', 'trash', 'trash_messages', 'admission'
  ];

  const scriptSrc = document.currentScript ? document.currentScript.src : '';
  const jsDir = scriptSrc.substring(0, scriptSrc.lastIndexOf('/') + 1);
  const frontendBase = (window.NYABZ_CONFIG && window.NYABZ_CONFIG.frontend) ||
    jsDir.substring(0, jsDir.lastIndexOf('js/'));
  const backendBase = (window.NYABZ_CONFIG && window.NYABZ_CONFIG.backend) ||
    frontendBase.replace(/\/?$/, '../backend/');

  window.NYABZ_FRONTEND = frontendBase.endsWith('/') ? frontendBase : frontendBase + '/';
  window.NYABZ_BACKEND = backendBase.endsWith('/') ? backendBase : backendBase + '/';

  const currentPage = window.location.pathname
    .split('/').pop()
    .replace(/\.html$/, '').replace(/\.php$/, '') || 'index';

  const isAdminPage = ADMIN_PAGES.includes(currentPage);
  const staticOnly = !!(window.NYABZ_CONFIG && window.NYABZ_CONFIG.staticOnly);

  if (isAdminPage && staticOnly) {
    window.location.replace((window.NYABZ_CONFIG && window.NYABZ_CONFIG.frontend) || frontendBase + 'index.html');
    return;
  }

  const ADMIN_DATA_PAGES = new Set([
    'announcements', 'manage_classes', 'manage_admins', 'user', 'settings',
    'attendance', 'admission', 'exam_schedule', 'manage_subjects', 'report_cards',
    'trash', 'trash_messages', 'view_payment_details', 'studenthome', 'event_details'
  ]);

  function loadScript(src) {
    if (document.querySelector('script[src="' + src + '"]')) return;
    const s = document.createElement('script');
    s.src = src;
    document.body.appendChild(s);
  }

  if (isAdminPage && !staticOnly) {
    fetch(backendBase + 'check_session.php', { credentials: 'include' })
      .then(r => r.json())
      .then(data => {
        if (!data.loggedIn) {
          window.location.href = frontendBase + 'login.html';
        }
      })
      .catch(() => {});
  }

  function injectNavbar() {
    const placeholder = document.getElementById('navbar-placeholder');
    if (!placeholder) return;

    fetch(frontendBase + 'navbar.html')
      .then(r => r.text())
      .then(html => {
        placeholder.outerHTML = html;
        setActiveNavLink();
        requestAnimationFrame(function () {
          initNavbarScroll();
        });
      })
      .catch(() => {});
  }

  function setActiveNavLink() {
    document.querySelectorAll('.navbar-nav .nav-link, .dropdown-menu .dropdown-item').forEach(link => {
      const href = link.getAttribute('href') || '';
      const linkPage = href.split('/').pop().replace(/\.html$/, '').replace(/\.php$/, '');
      if (linkPage === currentPage) {
        link.classList.add('active');
        const parentDropdown = link.closest('.dropdown');
        if (parentDropdown) {
          const toggle = parentDropdown.querySelector('.dropdown-toggle');
          if (toggle) toggle.classList.add('active');
        }
      }
    });
  }

  function updateHeaderHeight() {
    const topbar = document.querySelector('.topbar');
    const navbar = document.querySelector('.navbar');
    if (!navbar) return;
    const h = (topbar ? topbar.offsetHeight : 0) + navbar.offsetHeight;
    document.documentElement.style.setProperty('--site-header-height', h + 'px');
  }

  function syncNavbarOnScroll() {
    const topbar = document.querySelector('.topbar');
    const navbar = document.querySelector('.navbar');
    if (!navbar) return;

    const isHomePage = document.body.classList.contains('home-page');
    const scrolled = window.scrollY > 50;

    if (scrolled) {
      if (topbar) topbar.classList.add('hidden');
      navbar.classList.add('at-top');
      navbar.classList.remove('transparent');
      if (!isHomePage) {
        document.body.style.paddingTop = navbar.offsetHeight + (topbar && !topbar.classList.contains('hidden') ? topbar.offsetHeight : 0) + 'px';
      } else {
        document.body.style.paddingTop = '';
      }
    } else {
      if (topbar) topbar.classList.remove('hidden');
      navbar.classList.remove('at-top');
      if (isHomePage) navbar.classList.add('transparent');
      document.body.style.paddingTop = '';
    }
  }

  function initNavbarScroll() {
    const topbar = document.querySelector('.topbar');
    const navbar = document.querySelector('.navbar');
    if (!navbar) return;

    const isHomePage = document.body.classList.contains('home-page') ||
      !!document.querySelector('.nyab-hero-carousel, .nyab-hero-section, #hero, .nyab-hero-caption');
    if (isHomePage) {
      document.body.classList.add('home-page');
      navbar.classList.add('transparent');
    }

    updateHeaderHeight();
    syncNavbarOnScroll();
    window.addEventListener('scroll', syncNavbarOnScroll, { passive: true });
    window.addEventListener('resize', updateHeaderHeight);

    const toggler = navbar.querySelector('.navbar-toggler');
    const collapse = navbar.querySelector('.navbar-collapse');
    if (toggler && collapse) {
      toggler.addEventListener('click', () => collapse.classList.toggle('show'));
      document.addEventListener('click', e => {
        if (!toggler.contains(e.target) && !collapse.contains(e.target)) {
          collapse.classList.remove('show');
        }
      });
      navbar.querySelectorAll('.dropdown-toggle').forEach(t => {
        t.addEventListener('click', function (e) {
          if (window.innerWidth < 992) {
            e.preventDefault();
            this.nextElementSibling.classList.toggle('show');
          }
        });
      });
    }

    if (window.innerWidth >= 992) {
      navbar.querySelectorAll('.dropdown').forEach(item => {
        item.addEventListener('mouseenter', () => item.querySelector('.dropdown-menu').classList.add('show'));
        item.addEventListener('mouseleave', () => item.querySelector('.dropdown-menu').classList.remove('show'));
      });
    }
  }

  function injectSidebar() {
    const placeholder = document.getElementById('sidebar-placeholder');
    if (!placeholder) return;

    fetch(frontendBase + 'admin_sidebar.html')
      .then(r => r.text())
      .then(html => {
        placeholder.outerHTML = html;
        document.querySelectorAll('.sidebar-nav a').forEach(link => {
          const href = link.getAttribute('href') || '';
          const linkPage = href.split('/').pop().replace(/\.html$/, '').replace(/\.php$/, '');
          if (linkPage === currentPage) link.classList.add('active');
        });
      })
      .catch(() => {});
  }

  function injectFooter() {
    if (document.querySelector('.green-modern-footer')) return;
    const hasFooterCss = Array.from(document.querySelectorAll('link[rel="stylesheet"]'))
      .some(l => (l.getAttribute('href') || '').includes('modern-footer'));
    const placeholder = document.getElementById('footer-placeholder');
    if (!hasFooterCss && !placeholder) return;

    const target = placeholder || (function () {
      const el = document.createElement('div');
      el.id = 'footer-placeholder';
      document.body.appendChild(el);
      return el;
    })();

    fetch(frontendBase + 'modern-footer.html')
      .then(r => r.text())
      .then(html => {
        target.outerHTML = html;
        fixFooterYear();
      })
      .catch(() => {});
  }

  function injectDeleteModal() {
    if (document.getElementById('dmOverlay') || document.getElementById('softDeleteModal')) return;
    const placeholder = document.getElementById('delete-modal-placeholder');
    if (!placeholder && !isAdminPage) return;

    fetch(frontendBase + 'delete_modal.html')
      .then(r => r.text())
      .then(html => {
        if (placeholder) {
          placeholder.outerHTML = html;
        } else {
          document.body.insertAdjacentHTML('beforeend', html);
        }
      })
      .catch(() => {});
  }

  function injectSoftDeleteModal() {
    if (!isAdminPage || document.getElementById('softDeleteModal')) return;

    const html = `
<div id="softDeleteModal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.4);z-index:99999;align-items:center;justify-content:center;">
  <div style="background:#fff;border-radius:18px;max-width:380px;width:90vw;padding:32px;text-align:center;box-shadow:0 12px 40px rgba(0,0,0,0.15);">
    <div style="width:64px;height:64px;background:#fff5f5;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
      <i class="fa-solid fa-trash" style="font-size:1.8rem;color:#e74c3c;"></i>
    </div>
    <h3 style="margin:0 0 8px;color:#333;font-size:1.2rem;">Move to Trash?</h3>
    <p style="color:#888;margin:0 0 24px;font-size:0.92rem;">This will move the item to trash. You can restore it later.</p>
    <div style="display:flex;gap:12px;justify-content:center;">
      <button id="softDeleteConfirm" style="background:linear-gradient(135deg,#e74c3c,#c0392b);color:#fff;border:none;border-radius:10px;padding:11px 28px;font-size:1rem;font-weight:700;cursor:pointer;">Move to Trash</button>
      <button id="softDeleteCancel" style="background:#f0f0f0;color:#555;border:none;border-radius:10px;padding:11px 28px;font-size:1rem;font-weight:600;cursor:pointer;">Keep it</button>
    </div>
  </div>
</div>`;
    document.body.insertAdjacentHTML('beforeend', html);

    let _sdType = null, _sdId = null, _sdRow = null;
    const _sdModal = document.getElementById('softDeleteModal');

    window.softDelete = function (type, id, el) {
      _sdType = type;
      _sdId = id;
      _sdRow = el ? el.closest('tr') : null;
      _sdModal.style.display = 'flex';
    };

    document.getElementById('softDeleteCancel').onclick = () => {
      _sdModal.style.display = 'none';
    };

    document.getElementById('softDeleteConfirm').onclick = function () {
      if (!_sdType || !_sdId) return;
      const btn = this;
      btn.disabled = true;
      const urls = {
        student: backendBase + 'delete_student.php',
        teacher: backendBase + 'delete_teacher.php',
        message: backendBase + 'delete_message_ajax.php',
        announcement: backendBase + 'delete_announcement_ajax.php'
      };
      fetch(urls[_sdType] || urls.student, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=trash&id=' + encodeURIComponent(_sdId)
      })
        .then(r => r.json())
        .then(d => {
          _sdModal.style.display = 'none';
          if (d.success && _sdRow) {
            _sdRow.style.transition = 'opacity 0.4s, transform 0.4s';
            _sdRow.style.opacity = '0';
            setTimeout(() => _sdRow.remove(), 400);
          } else if (d.success) {
            location.reload();
          }
          btn.disabled = false;
        })
        .catch(() => {
          _sdModal.style.display = 'none';
          btn.disabled = false;
        });
    };
  }

  function fixFooterYear() {
    document.querySelectorAll('p, span').forEach(el => {
      if (el.innerHTML.includes('<!-- php-removed -->')) {
        el.innerHTML = el.innerHTML.replace(/<!--\s*php-removed\s*-->/g, new Date().getFullYear());
      }
    });
  }

  function run() {
    injectNavbar();
    if (isAdminPage && !staticOnly) injectSidebar();
    injectFooter();
    if (!staticOnly) {
      injectDeleteModal();
      injectSoftDeleteModal();
    }
    fixFooterYear();
    if (staticOnly) {
      loadScript(jsDir + 'static-site.js');
      return;
    }
    loadScript(jsDir + 'page-data.js');
    if (currentPage === 'view_student') loadScript(jsDir + 'view-student.js');
    if (currentPage === 'view_teacher') loadScript(jsDir + 'view-teacher.js');
    if (ADMIN_DATA_PAGES.has(currentPage)) loadScript(jsDir + 'admin-data.js');
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', run);
  } else {
    run();
  }
})();
