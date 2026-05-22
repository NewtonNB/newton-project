/**
 * admin-data.js — loads dynamic data for admin/student HTML pages
 */
(function () {
  'use strict';

  if (window.NYABZ_CONFIG && window.NYABZ_CONFIG.staticOnly) return;

  const backend = window.NYABZ_BACKEND || '../backend/';
  const page = window.location.pathname.split('/').pop().replace(/\.html$/, '').replace(/\.php$/, '') || '';

  function esc(s) {
    const d = document.createElement('div');
    d.textContent = s == null ? '' : String(s);
    return d.innerHTML;
  }

  function fmtDate(d) {
    if (!d) return '';
    const dt = new Date(d);
    return isNaN(dt.getTime()) ? d : dt.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
  }

  function qs(p) { return new URLSearchParams(window.location.search).get(p) || ''; }

  /* ── Announcements ─────────────────────────────────────── */
  function loadAnnouncements() {
    const list = document.querySelector('.announcements-list');
    if (!list) return;
    fetch(backend + 'get_announcements_ajax.php', { credentials: 'include' })
      .then(r => r.json())
      .then(data => {
        if (data.error) return;
        const items = data.announcements || [];
        if (!items.length) {
          list.innerHTML = '<div class="announcement-card">No announcements or events found.</div>';
          return;
        }
        list.innerHTML = '';
        items.forEach(a => {
          let speakers = '';
          if (a.speakers) {
            try {
              const s = JSON.parse(a.speakers);
              if (Array.isArray(s)) speakers = s.join(', ');
            } catch (e) { speakers = a.speakers; }
          }
          const cat = a.category || '';
          const cardClass = 'announcement-card' + (cat === 'Event' ? ' category-event' : '');
          let galleryHtml = '';
          if (a.gallery) {
            try {
              const g = JSON.parse(a.gallery);
              if (Array.isArray(g)) galleryHtml = '<div class="announcement-gallery">' + g.map(img => '<img src="' + esc(img) + '" alt="">').join('') + '</div>';
            } catch (e) {}
          }
          list.innerHTML += '<div class="' + cardClass + '"><h3>' + esc(a.title) +
            (cat ? ' <span class="event-category-badge">' + esc(cat) + '</span>' : '') + '</h3>' +
            '<div class="announcement-meta-grid">' +
            (a.date ? '<div class="announcement-meta"><i class="fa fa-calendar-alt"></i> ' + esc(fmtDate(a.date)) + '</div>' : '') +
            (a.time ? '<div class="announcement-meta"><i class="fa fa-clock"></i> ' + esc(a.time) + '</div>' : '') +
            (a.location ? '<div class="announcement-meta"><i class="fa fa-map-marker-alt"></i> ' + esc(a.location) + '</div>' : '') +
            (speakers ? '<div class="announcement-meta"><i class="fa fa-users"></i> ' + esc(speakers) + '</div>' : '') +
            '</div><div class="announcement-content">' + esc(a.content || '').replace(/\n/g, '<br>') + '</div>' +
            galleryHtml +
            '<div style="margin-top:12px;"><button type="button" class="btn btn-sm btn-warning btn-edit-announcement" data-id="' + a.id + '"><i class="fa fa-edit"></i> Edit</button>' +
            ' <a href="#" class="btn btn-sm btn-danger" onclick="softDelete(\'announcement\', ' + a.id + ', this)" style="margin-left:8px;"><i class="fa fa-trash"></i> Remove</a></div></div>';
        });
      });
  }

  /* ── Manage classes ─────────────────────────────────────── */
  function loadManageClasses() {
    const tbody = document.querySelector('.card table tbody') || document.querySelector('.modern-table tbody');
    if (!tbody) return;
    fetch(backend + 'get_classes_ajax.php', { credentials: 'include' })
      .then(r => r.json())
      .then(data => {
        const rows = data.classes || [];
        tbody.innerHTML = rows.length ? rows.map((c, i) =>
          '<tr><td>' + (i + 1) + '</td><td>' + esc(c.class_name) + '</td><td>' + esc(c.level) + '</td>' +
          '<td><button class="btn btn-sm btn-primary" data-id="' + c.id + '" data-name="' + esc(c.class_name) + '" data-level="' + esc(c.level) + '"><i class="fa fa-edit"></i></button> ' +
          '<button class="btn btn-sm btn-danger" onclick="showDeleteModal(\'' + esc(c.class_name) + '\', \'../backend/delete_class_ajax.php?delete=' + c.id + '\')"><i class="fa fa-trash"></i></button> ' +
          '<a href="view_student.html?class_id=' + c.id + '" class="btn btn-primary btn-sm">Students</a></td></tr>'
        ).join('') : '<tr><td colspan="4" style="text-align:center;padding:24px;">No classes yet.</td></tr>';
      });
  }

  /* ── Manage admins ─────────────────────────────────────── */
  function loadManageAdmins() {
    const tbody = document.querySelector('.admins-table tbody');
    if (!tbody) return;
    fetch(backend + 'get_admins_ajax.php', { credentials: 'include' })
      .then(r => r.json())
      .then(data => {
        const rows = data.admins || [];
        tbody.innerHTML = rows.length ? rows.map(a =>
          '<tr><td>' + a.id + '</td><td>' + esc(a.username) + '</td><td>' + esc(a.email) + '</td><td>' + esc(a.phone) + '</td>' +
          '<td>' + esc(a.status) + '</td><td>' + esc(a.created_at) + '</td><td>' +
          '<button class="btn-edit" onclick="editAdmin(' + a.id + ')"><i class="fas fa-edit"></i></button> ' +
          '<button class="btn-delete" onclick="deleteAdmin(' + a.id + ')"><i class="fas fa-trash"></i></button></td></tr>'
        ).join('') : '<tr><td colspan="7" style="text-align:center;">No administrators found.</td></tr>';
      });
  }

  /* ── User hub ─────────────────────────────────────── */
  function loadUser() {
    fetch(backend + 'get_user_stats_ajax.php', { credentials: 'include' })
      .then(r => r.json())
      .then(data => {
        const cards = document.querySelectorAll('.user-card .count');
        if (cards[0]) cards[0].textContent = data.students ?? 0;
        if (cards[1]) cards[1].textContent = data.teachers ?? 0;
        if (cards[2]) cards[2].textContent = data.admins ?? 0;
      });
  }

  /* ── Settings profile ─────────────────────────────────────── */
  function loadSettings() {
    fetch(backend + 'get_settings_profile_ajax.php', { credentials: 'include' })
      .then(r => r.json())
      .then(data => {
        const u = data.user || {};
        const n = document.getElementById('profileName');
        const e = document.getElementById('profileEmail');
        const p = document.getElementById('profilePhone');
        if (n) n.value = u.name || u.username || '';
        if (e) e.value = u.email || '';
        if (p) p.value = u.phone || '';
      });
  }

  /* ── Attendance ─────────────────────────────────────── */
  function loadAttendance() {
    const classId = qs('class_id') || document.getElementById('class_id')?.value || '';
    const date = qs('date') || document.getElementById('date')?.value || new Date().toISOString().slice(0, 10);
    const q = '?class_id=' + encodeURIComponent(classId) + '&date=' + encodeURIComponent(date);
    fetch(backend + 'get_attendance_ajax.php' + q, { credentials: 'include' })
      .then(r => r.json())
      .then(data => {
        const sel = document.getElementById('class_id');
        if (sel && sel.options.length <= 1) {
          (data.classes || []).forEach(c => {
            const o = document.createElement('option');
            o.value = c.id;
            o.textContent = c.class_name;
            if (String(c.id) === String(data.filters.class_id)) o.selected = true;
            sel.appendChild(o);
          });
        }
        const dateEl = document.getElementById('date');
        if (dateEl && data.filters.date) dateEl.value = data.filters.date;
        const tbody = document.querySelector('.attendance-table tbody');
        if (!tbody) return;
        const students = data.students || [];
        const map = data.attendance_map || {};
        if (!students.length) {
          tbody.innerHTML = '<tr><td colspan="3" style="text-align:center;">Select a class to take attendance.</td></tr>';
          return;
        }
        tbody.innerHTML = students.map(s => {
          const st = map[s.id] || 'Present';
          const mk = v => st === v ? ' checked' : '';
          return '<tr><td>' + esc(s.full_name) + '</td><td><div class="attendance-status">' +
            ['Present', 'Absent', 'Late', 'Excused'].map(v =>
              '<label><input type="radio" name="attendance[' + s.id + ']" value="' + v + '"' + mk(v) + '> ' + v + '</label>'
            ).join('') + '</div></td></tr>';
        }).join('');
      });
  }

  /* ── Admissions ─────────────────────────────────────── */
  function loadAdmission() {
    const tbody = document.querySelector('.modern-table tbody');
    if (!tbody) return;
    fetch(backend + 'get_admissions_ajax.php', { credentials: 'include' })
      .then(r => r.json())
      .then(data => {
        const apps = data.applications || [];
        const stat = document.querySelector('.stats-info span');
        if (stat) stat.textContent = 'Total Applications: ' + (data.total ?? apps.length);
        if (!apps.length) {
          tbody.innerHTML = '<tr><td colspan="10" style="text-align:center;padding:32px;">No pending applications.</td></tr>';
          return;
        }
        tbody.innerHTML = apps.map(info => {
          const photo = info.passport_photo ? esc(info.passport_photo) : 'nyabzgallery/nyabzlogo.png';
          return '<tr><td><div style="display:flex;align-items:center;"><img class="avatar" src="' + photo + '" alt="" style="width:36px;height:36px;border-radius:50%;margin-right:8px;"><span>' + esc(info.name || '-') + '</span></div></td>' +
            '<td>' + esc(info.dob || '-') + '</td><td>' + esc(info.gender || '-') + '</td><td>' + esc(info.class_applying || '-') + '</td>' +
            '<td>' + esc(info.parent_name || '-') + '</td><td>' + esc(info.parent_phone || '-') + '</td><td>' + esc(info.email || '-') + '</td><td>' + esc(info.phone || '-') + '</td>' +
            '<td>' + (info.passport_photo ? '<img class="passport-thumb" src="' + photo + '" style="max-width:48px;">' : '-') + '</td>' +
            '<td class="actions-col"><form method="POST" action="../backend/process_admission.php" style="display:inline;"><input type="hidden" name="id" value="' + info.id + '"><input type="hidden" name="action" value="approve"><button type="submit" class="btn btn-success btn-sm">Approve</button></form> ' +
            '<form method="POST" action="../backend/process_admission.php" style="display:inline;"><input type="hidden" name="id" value="' + info.id + '"><input type="hidden" name="action" value="reject"><button type="submit" class="btn btn-danger btn-sm">Reject</button></form></td></tr>';
        }).join('');
      });
  }

  /* ── Exam schedule list ─────────────────────────────────────── */
  function loadExamSchedule() {
    const q = window.location.search || '';
    fetch(backend + 'get_exams_ajax.php' + q, { credentials: 'include' })
      .then(r => r.json())
      .then(data => {
        ['#class_id', 'select[name="class_id"]'].forEach(sel => {
          document.querySelectorAll(sel).forEach(el => {
            if (el.options.length <= 1 && data.classes) {
              data.classes.forEach(c => {
                const o = document.createElement('option');
                o.value = c.id;
                o.textContent = c.class_name;
                el.appendChild(o);
              });
            }
          });
        });
        const sub = document.querySelector('select[name="subject_name"]');
        if (sub && sub.options.length <= 1 && data.subjects) {
          data.subjects.forEach(s => {
            const o = document.createElement('option');
            o.value = s;
            o.textContent = s;
            sub.appendChild(o);
          });
        }
        const tbody = document.querySelector('.exam-table tbody');
        if (!tbody) return;
        const exams = data.exams || [];
        tbody.innerHTML = exams.length ? exams.map(ex =>
          '<tr><td>' + esc(ex.exam_name) + '</td><td>' + esc(ex.class_name) + '</td><td>' + esc(ex.subject_name) + '</td>' +
          '<td>' + esc(ex.exam_date) + '</td><td>' + esc(ex.start_time) + ' - ' + esc(ex.end_time) + '</td><td>' + esc(ex.room || '-') + '</td>' +
          '<td><button type="button" class="btn btn-edit" onclick="editExam(' + ex.id + ')">Edit</button> ' +
          '<button type="button" class="btn btn-danger" onclick="showDeleteModal(\'' + esc(ex.exam_name) + '\', \'../backend/delete_exam.php?delete=' + ex.id + '\')">Delete</button></td></tr>'
        ).join('') : '<tr><td colspan="7" style="text-align:center;">No exams scheduled.</td></tr>';
      });
  }

  /* ── Manage subjects ─────────────────────────────────────── */
  function loadManageSubjects() {
    fetch(backend + 'get_manage_subjects_ajax.php', { credentials: 'include' })
      .then(r => r.json())
      .then(data => {
        document.querySelectorAll('table').forEach((table, idx) => {
          const tbody = table.querySelector('tbody');
          if (!tbody) return;
          const list = idx === 0 ? (data.olevel || []) : (data.alevel || []);
          const level = idx === 0 ? 'olevel' : 'alevel';
          tbody.innerHTML = list.length ? list.map(s =>
            '<tr><td>' + s.id + '</td><td>' + esc(s.subject_name) + '</td><td>' +
            '<button class="btn btn-danger btn-sm" onclick="showDeleteModal(\'' + esc(s.subject_name) + '\', \'../backend/delete_subject.php?delete=' + s.id + '&level=' + level + '\')">Delete</button></td></tr>'
          ).join('') : '<tr><td colspan="3" style="text-align:center;">No subjects.</td></tr>';
        });
      });
  }

  /* ── Report cards ─────────────────────────────────────── */
  function loadReportCards() {
    const sel = document.getElementById('class_id');
    if (!sel || sel.options.length > 1) return;
    fetch(backend + 'get_classes_ajax.php', { credentials: 'include' })
      .then(r => r.json())
      .then(data => {
        (data.classes || []).forEach(c => {
          const o = document.createElement('option');
          o.value = c.id;
          o.textContent = c.class_name;
          sel.appendChild(o);
        });
      });
  }

  /* ── Trash ─────────────────────────────────────── */
  const trashUrls = {
    students: 'delete_student.php',
    teachers: 'delete_teacher.php',
    announcements: 'delete_announcement_ajax.php',
    messages: 'delete_message_ajax.php'
  };

  window.trashAct = function (type, action, id) {
    const url = trashUrls[type];
    if (!url) return;
    const run = () => fetch(backend + url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'action=' + action + '&id=' + id,
      credentials: 'include'
    }).then(r => r.json()).then(d => {
      if (d.success) loadTrash();
    });
    if (action === 'permanent' && typeof showConfirmModal === 'function') {
      showConfirmModal('Permanently delete this item?', run, { title: 'Delete Forever?', confirmText: 'Yes, Delete' });
    } else run();
  };

  function loadTrash() {
    const panel = document.getElementById('trash-panel');
    if (!panel) return;
    const tab = qs('tab') || 'students';
    fetch(backend + 'get_trash_ajax.php?tab=' + encodeURIComponent(tab), { credentials: 'include' })
      .then(r => r.json())
      .then(data => {
        const counts = data.counts || {};
        const total = data.total || 0;
        let tabs = '<div class="tabs">';
        ['students', 'teachers', 'announcements', 'messages'].forEach(t => {
          const label = t.charAt(0).toUpperCase() + t.slice(1);
          tabs += '<a href="?tab=' + t + '" class="tab' + (tab === t ? ' active' : '') + '">' + label + ' <span class="badge">' + (counts[t] || 0) + '</span></a>';
        });
        tabs += '</div>';
        const items = data.items || [];
        let body = '';
        if (!items.length) {
          body = '<div class="empty"><i class="fa-solid fa-trash"></i> No deleted ' + tab + '</div>';
        } else {
          const cols = {
            students: ['Name', 'Email', 'Class', 'Deleted', 'Actions'],
            teachers: ['Name', 'Email', 'Subject', 'Deleted', 'Actions'],
            announcements: ['Title', 'Category', 'Date', 'Deleted', 'Actions'],
            messages: ['Name', 'Email', 'Message', 'Deleted', 'Actions']
          };
          body = '<table><thead><tr>' + (cols[tab] || []).map(c => '<th>' + c + '</th>').join('') + '</tr></thead><tbody>';
          items.forEach(r => {
            const del = r.deleted_at ? fmtDate(r.deleted_at) : '';
            if (tab === 'students') {
              body += '<tr><td>' + esc(r.username) + '</td><td>' + esc(r.email) + '</td><td>' + esc(r.class_id) + '</td><td>' + del + '</td><td>' +
                '<button class="btn-restore" onclick="trashAct(\'students\',\'restore\',' + r.id + ')">Restore</button> ' +
                '<button class="btn-perm" onclick="trashAct(\'students\',\'permanent\',' + r.id + ')">Delete Forever</button></td></tr>';
            } else if (tab === 'teachers') {
              body += '<tr><td>' + esc(r.full_name) + '</td><td>' + esc(r.email) + '</td><td>' + esc(r.subject) + '</td><td>' + del + '</td><td>' +
                '<button class="btn-restore" onclick="trashAct(\'teachers\',\'restore\',' + r.id + ')">Restore</button> ' +
                '<button class="btn-perm" onclick="trashAct(\'teachers\',\'permanent\',' + r.id + ')">Delete Forever</button></td></tr>';
            } else if (tab === 'announcements') {
              body += '<tr><td>' + esc(r.title) + '</td><td>' + esc(r.category) + '</td><td>' + esc(r.date) + '</td><td>' + del + '</td><td>' +
                '<button class="btn-restore" onclick="trashAct(\'announcements\',\'restore\',' + r.id + ')">Restore</button> ' +
                '<button class="btn-perm" onclick="trashAct(\'announcements\',\'permanent\',' + r.id + ')">Delete Forever</button></td></tr>';
            } else {
              body += '<tr><td>' + esc((r.first_name || '') + ' ' + (r.last_name || '')) + '</td><td>' + esc(r.email) + '</td><td>' + esc((r.message || '').substring(0, 50)) + '</td><td>' + del + '</td><td>' +
                '<button class="btn-restore" onclick="trashAct(\'messages\',\'restore\',' + r.id + ')">Restore</button> ' +
                '<button class="btn-perm" onclick="trashAct(\'messages\',\'permanent\',' + r.id + ')">Delete Forever</button></td></tr>';
            }
          });
          body += '</tbody></table>';
        }
        panel.innerHTML = tabs + body;
        const badge = document.querySelector('.content .card .header-row h2 span');
        if (badge) badge.textContent = total + ' items';
      });
  }

  window.emptyAll = function () {
    const tab = qs('tab') || 'students';
    const url = trashUrls[tab];
    if (!url) return;
    const run = () => {
      fetch(backend + 'get_trash_ajax.php?tab=' + encodeURIComponent(tab), { credentials: 'include' })
        .then(r => r.json())
        .then(data => {
          const items = data.items || [];
          Promise.all(items.map(r =>
            fetch(backend + url, {
              method: 'POST',
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
              body: 'action=permanent&id=' + r.id,
              credentials: 'include'
            })
          )).then(() => loadTrash());
        });
    };
    if (typeof showConfirmModal === 'function') {
      showConfirmModal('Permanently delete ALL items in trash? This cannot be undone.', run, { title: 'Empty All Trash?', confirmText: 'Yes, Delete All', icon: 'fa-trash' });
    } else run();
  };

  /* ── Trash messages ─────────────────────────────────────── */
  function loadTrashMessages() {
    const tbody = document.getElementById('trashTable');
    if (!tbody) return;
    fetch(backend + 'get_trash_messages_ajax.php', { credentials: 'include' })
      .then(r => r.json())
      .then(data => {
        const btn = document.querySelector('.empty-trash-btn');
        if (btn) btn.innerHTML = '<i class="fa-solid fa-trash"></i> Empty Trash (' + (data.count || 0) + ')';
        const msgs = data.messages || [];
        if (!msgs.length) {
          tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Trash is empty</td></tr>';
          return;
        }
        tbody.innerHTML = msgs.map(row =>
          '<tr id="row-' + row.id + '"><td>' + esc((row.first_name || '') + ' ' + (row.last_name || '')) + '</td><td>' + esc(row.email) + '</td>' +
          '<td>' + esc((row.message || '').substring(0, 50)) + '</td><td>' + fmtDate(row.deleted_at) + '</td><td>' +
          '<button class="btn-restore" onclick="restoreMsg(' + row.id + ')">Restore</button> ' +
          '<button class="btn-delete" onclick="permanentDelete(' + row.id + ')">Delete Forever</button></td></tr>'
        ).join('');
      });
  }

  /* ── Payment details ─────────────────────────────────────── */
  function loadPaymentDetails() {
    const sid = qs('student_id');
    if (!sid) return;
    fetch(backend + 'get_payment_details_ajax.php?student_id=' + encodeURIComponent(sid), { credentials: 'include' })
      .then(r => r.json())
      .then(data => {
        if (data.error) return;
        const s = data.student || {};
        const vals = document.querySelectorAll('.info-value');
        if (vals[0]) vals[0].textContent = s.username || '';
        if (vals[1]) vals[1].textContent = s.class_name || '';
        if (vals[2]) vals[2].textContent = s.email || '';
        if (vals[3]) vals[3].textContent = s.phone || '';
        const summary = document.querySelector('.summary-card h2');
        if (summary) summary.textContent = 'UGX ' + Number(data.total_paid || 0).toLocaleString();
        const tbody = document.querySelector('table tbody');
        if (tbody) {
          const pays = data.payments || [];
          tbody.innerHTML = pays.length ? pays.map((p, i) => {
            const badge = (p.payment_method || '').toLowerCase();
            return '<tr><td>' + (i + 1) + '</td><td>' + esc(p.payment_date) + '</td><td class="amount">UGX ' + Number(p.amount_paid || 0).toLocaleString() + '</td>' +
              '<td><span class="badge badge-' + esc(badge) + '">' + esc(p.payment_method || '') + '</span></td>' +
              '<td>' + esc(p.term || '') + '</td><td>' + esc(p.academic_year || '') + '</td><td></td></tr>';
          }).join('') : '<tr><td colspan="7">No payments recorded.</td></tr>';
        }
      });
  }

  /* ── Student home ─────────────────────────────────────── */
  function loadStudentHome() {
    fetch(backend + 'get_student_home_ajax.php', { credentials: 'include' })
      .then(r => r.json())
      .then(data => {
        if (data.error) return;
        const av = document.querySelector('.student-avatar');
        const nm = document.querySelector('.student-name');
        if (av) av.src = data.profilePic || 'nyabzgallery/student3.jpg';
        if (nm) nm.textContent = 'Welcome, ' + (data.full_name || data.username) + '!';
        const classCard = document.querySelector('.dashboard-section .card');
        if (classCard) classCard.innerHTML = '<ul class="list">' + (data.classes || []).map(c => '<li>' + esc(c) + '</li>').join('') + '</ul>';
        const assignUl = document.querySelectorAll('.list')[0];
        if (assignUl) {
          const a = data.upcomingAssignments || [];
          assignUl.innerHTML = a.length ? a.map(x => '<li><strong>' + esc(x.subject) + ':</strong> ' + esc(x.title) + ' (Due: ' + esc(x.due) + ')</li>').join('') : '<li>No upcoming assignments.</li>';
        }
        const gradeUl = document.querySelectorAll('.list')[1];
        if (gradeUl) {
          const g = data.recentGrades || [];
          gradeUl.innerHTML = g.length ? g.map(x => '<li><strong>' + esc(x.subject) + ':</strong> ' + esc(x.grade) + '</li>').join('') : '<li>No recent grades.</li>';
        }
        const feeCards = document.querySelectorAll('.dashboard-grid .card');
        if (feeCards[1]) feeCards[1].textContent = data.feeBalance || '';
      });
  }

  /* ── Event details (public) ─────────────────────────────────────── */
  function loadEventDetails() {
    const root = document.querySelector('.event-details-container');
    if (!root) return;
    const id = qs('id') || '1';
    fetch(backend + 'get_event_details_ajax.php?id=' + encodeURIComponent(id))
      .then(r => r.json())
      .then(data => {
        if (!data.found || !data.event) {
          root.innerHTML = '<div class="event-title">Event Not Found</div><div class="event-description">Sorry, the event you are looking for does not exist.</div><a class="back-link" href="events.html">&larr; Back to Events</a>';
          document.title = 'Event Not Found';
          return;
        }
        const e = data.event;
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
        if (e.download) html += '<a class="event-download" href="' + esc(e.download) + '" download>Download File</a>';
        html += '<a class="back-link" href="events.html">&larr; Back to Events</a>';
        root.innerHTML = html;
      });
  }

  const handlers = {
    announcements: loadAnnouncements,
    manage_classes: loadManageClasses,
    manage_admins: loadManageAdmins,
    user: loadUser,
    settings: loadSettings,
    attendance: loadAttendance,
    admission: loadAdmission,
    exam_schedule: loadExamSchedule,
    manage_subjects: loadManageSubjects,
    report_cards: loadReportCards,
    trash: loadTrash,
    trash_messages: loadTrashMessages,
    view_payment_details: loadPaymentDetails,
    studenthome: loadStudentHome,
    event_details: loadEventDetails
  };

  if (handlers[page]) {
    document.addEventListener('DOMContentLoaded', handlers[page]);
    window.reloadAdminPage = handlers[page];
  }
})();
