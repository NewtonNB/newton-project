/**
 * static-site.js — public site works without PHP backend (Netlify static deploy)
 */
(function () {
  'use strict';

  if (!window.NYABZ_CONFIG || !window.NYABZ_CONFIG.staticOnly) return;

  const page = window.location.pathname.split('/').pop().replace(/\.html$/, '') || 'index';
  const stats = window.NYABZ_CONFIG.stats || {
    totalStudents: 1500,
    totalTeachers: 120,
    graduatedStudents: 450,
    activityCount: 12
  };

  const DEMO_EVENTS = {
    '1': { title: 'Independence Day', date: '09 OCT 2025', time: '10:00 AM - 2:00 PM', image: 'nyabzgallery/indepe.jpg', gallery: ['nyabzgallery/indepe.jpg', 'nyabzgallery/current.jpg'], location: 'Nyabikoni Secondary School Grounds', download: '', speakers: [{ name: 'Hon. Janet Kataaha Museveni', title: 'Minister of Education', photo: 'nyabzgallery/HEADMASTER.jpg' }, { name: 'Mr. John Doe', title: 'Headmaster' }], description: "Uganda will celebrate the 62nd anniversary of its independence hosted by the northern district of Kitgum, highlighting the nation's unity and progress." },
    '2': { title: 'Induction Ceremony', date: '15 JUL 2025', time: '9:00 AM - 12:00 PM', image: 'nyabzgallery/ceremony.jpg', gallery: ['nyabzgallery/ceremony.jpg'], location: 'Main Hall', download: '', speakers: [{ name: 'Ms. Jane Smith', title: 'Senior Teacher' }], description: 'The school will hold an induction ceremony to officially welcome S.1 and S.5 learners.' },
    '3': { title: 'Pre-Mock Examinations', date: '20 JUL 2025', time: '', image: 'nyabzgallery/exams.jpg', gallery: ['nyabzgallery/exams.jpg'], location: 'Examination Rooms', download: '', speakers: [], description: 'Learners in S.4 and S.6 will sit their pre-mock examinations.' },
    '4': { title: 'Parent Visitation Day (Term III, 2025)', date: '10 AUG 2025', time: '', image: 'nyabzgallery/VD.jpg', gallery: ['nyabzgallery/VD.jpg'], location: 'School Compound', download: '', speakers: [], description: 'Visitation day is scheduled for Sunday, 10 August 2025 starting at 8:00 AM.' },
    '5': { title: 'UACE Results, 2025', date: '15 MAR 2025', time: '', image: 'nyabzgallery/sucsess2.jpg', gallery: ['nyabzgallery/sucsess2.jpg'], location: 'Ministry of Education', download: '', speakers: [], description: 'Release of the 2025 Uganda Advanced Certificate of Education results.' },
    '6': { title: 'UCE Results, 2025', date: '22 MAR 2025', time: '', image: 'nyabzgallery/UCE RESULTS (2).jpg', gallery: ['nyabzgallery/UCE RESULTS (2).jpg'], location: 'State House', download: '', speakers: [], description: 'Release of the 2025 Uganda Certificate of Education results.' },
    '7': { title: 'Chapel Times', date: '28 JUL 2025', time: '', image: 'nyabzgallery/chapel3.jpg', gallery: ['nyabzgallery/chapel3.jpg'], location: 'School Chapel', download: '', speakers: [], description: 'Students will gather for the Thanksgiving Mass.' },
    '8': { title: 'Parent Visitation Day (Term II, 2025)', date: '01 JUL 2025', time: '', image: 'nyabzgallery/VD.jpg', gallery: ['nyabzgallery/VD.jpg'], location: 'School Compound', download: '', speakers: [], description: 'Parents and guardians are invited to visitation day on Tuesday, 1 July 2025.' },
    '9': { title: 'School Life', date: '05 AUG 2025', time: '', image: 'nyabzgallery/school life.JPG', gallery: ['nyabzgallery/school life.JPG'], location: 'School Premises', download: '', speakers: [], description: 'At Nyabikoni Secondary School, students enjoy a safe, inclusive environment.' }
  };

  function esc(s) {
    const d = document.createElement('div');
    d.textContent = s == null ? '' : String(s);
    return d.innerHTML;
  }

  function runIndexCounters() {
    if (page !== 'index') return;
    const targets = [stats.totalStudents, stats.totalTeachers, stats.graduatedStudents, stats.activityCount];
    document.querySelectorAll('.counter').forEach((el, i) => {
      const target = targets[i] != null ? targets[i] : 0;
      el.setAttribute('data-target', target);
      el.innerText = '0';
      const step = () => {
        const count = +el.innerText;
        const inc = Math.max(1, Math.ceil(target / 100));
        if (count < target) {
          el.innerText = Math.min(target, count + inc);
          setTimeout(step, 18);
        } else el.innerText = target;
      };
      step();
    });
  }

  function runEventDetails() {
    if (page !== 'event_details') return;
    const root = document.querySelector('.event-details-container');
    if (!root) return;
    const id = new URLSearchParams(window.location.search).get('id') || '1';
    const e = DEMO_EVENTS[id];
    if (!e) {
      root.innerHTML = '<div class="event-title">Event Not Found</div><p>Sorry, this event is not available.</p><a class="back-link" href="events.html">&larr; Back to Events</a>';
      return;
    }
    document.title = e.title;
    let html = '<div class="event-title">' + esc(e.title) + '</div><div class="event-date">' + esc(e.date) + '</div>';
    if (e.time) html += '<span class="event-time"><i class="fa fa-clock"></i> ' + esc(e.time) + '</span>';
    html += '<img class="event-image" src="' + esc(e.image) + '" alt="">';
    if (e.location) html += '<div class="event-location"><i class="fa fa-map-marker-alt"></i> ' + esc(e.location) + '</div>';
    html += '<div class="event-description">' + esc(e.description) + '</div>';
    if (e.speakers && e.speakers.length) {
      html += '<div class="event-speakers"><strong>Speakers:</strong><br>';
      e.speakers.forEach(sp => {
        html += '<div class="event-speaker">' + (sp.photo ? '<img class="event-speaker-photo" src="' + esc(sp.photo) + '">' : '') +
          '<div class="event-speaker-info"><span class="event-speaker-name">' + esc(sp.name) + '</span>' +
          (sp.title ? '<span class="event-speaker-title"> - ' + esc(sp.title) + '</span>' : '') + '</div></div>';
      });
      html += '</div>';
    }
    if (e.gallery && e.gallery.length) {
      html += '<div class="event-gallery"><strong>Gallery:</strong><br>' + e.gallery.map(img => '<img src="' + esc(img) + '" alt="">').join('') + '</div>';
    }
    html += '<a class="back-link" href="events.html">&larr; Back to Events</a>';
    root.innerHTML = html;
  }

  function patchContactForm() {
    if (page !== 'contactus') return;
    const form = document.getElementById('dynamicContactForm');
    if (!form || form.dataset.staticPatched) return;
    form.dataset.staticPatched = '1';
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      e.stopImmediatePropagation();
      const note = document.querySelector('.notification-container') || document.body;
      const div = document.createElement('div');
      div.className = 'notification show';
      div.style.cssText = 'position:fixed;top:20px;right:20px;z-index:99999;background:#27ae60;color:#fff;padding:16px 20px;border-radius:12px;max-width:360px;';
      div.innerHTML = '<i class="fas fa-info-circle"></i> This demo site cannot send messages. Please email <strong>nyabikonisecschool@gmail.com</strong> or call +256 703 599 882.';
      note.appendChild(div);
      setTimeout(() => div.remove(), 8000);
    }, true);
  }

  function patchEventRegistration() {
    if (page !== 'events') return;
    const origFetch = window.fetch;
    window.fetch = function (url, opts) {
      const u = String(url || '');
      if (u.includes('register_event')) {
        return Promise.resolve({
          ok: true,
          json: () => Promise.resolve({ success: false, message: 'Online registration is available on the full school portal. Please contact the school office.' })
        });
      }
      return origFetch.apply(this, arguments);
    };
  }

  function patchNavbarLogin() {
    const link = document.getElementById('nav-login-link');
    if (link) {
      link.href = 'contactus.html';
      link.innerHTML = '<i class="fas fa-envelope"></i> CONTACT';
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    runIndexCounters();
    runEventDetails();
    patchContactForm();
    patchEventRegistration();
    setTimeout(patchNavbarLogin, 500);
  });
})();
