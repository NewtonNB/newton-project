/**
 * view-teacher.js — loads and renders teacher list for view_teacher.html
 */
(function () {
  'use strict';

  const backend = window.NYABZ_BACKEND || '../backend/';
  const tbody = document.querySelector('.modern-table tbody');
  if (!tbody) return;

  function esc(s) {
    const d = document.createElement('div');
    d.textContent = s == null ? '' : String(s);
    return d.innerHTML;
  }

  function getParams() {
    const p = new URLSearchParams(window.location.search);
    return {
      search: p.get('search') || '',
      page: p.get('page') || '1'
    };
  }

  function buildQuery(extra) {
    const q = Object.assign(getParams(), extra || {});
    const parts = [];
    if (q.search) parts.push('search=' + encodeURIComponent(q.search));
    if (q.page && q.page !== '1') parts.push('page=' + encodeURIComponent(q.page));
    return parts.length ? '?' + parts.join('&') : '';
  }

  function photoSrc(photo) {
    if (!photo) return 'nyabzgallery/default.svg';
    return 'nyabzgallery/' + String(photo).replace(/[<>"']/g, '');
  }

  function renderStats(stats) {
    const cards = document.querySelectorAll('.stats-section .stat-number');
    if (cards[0]) cards[0].textContent = stats.total ?? 0;
    if (cards[1]) cards[1].textContent = stats.male ?? 0;
    if (cards[2]) cards[2].textContent = stats.female ?? 0;
  }

  function renderTeachers(data) {
    const search = data.filters.search || '';
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) searchInput.value = search;

    const clearLink = document.getElementById('clear-search-link');
    if (clearLink) clearLink.style.display = search ? '' : 'none';

    renderStats(data.stats || {});

    const teachers = data.teachers || [];
    tbody.innerHTML = '';

    if (!teachers.length) {
      tbody.innerHTML = '<tr><td colspan="9" class="table_td" style="text-align:center;padding:40px;color:#718096;font-style:italic;">' +
        '<i class="fas fa-chalkboard-teacher" style="font-size:2rem;margin-bottom:10px;display:block;color:#a0aec0;"></i>' +
        'No teachers found. Add your first teacher to get started!</td></tr>';
      return;
    }

    const start = (data.pagination.page - 1) * data.pagination.perPage;

    teachers.forEach((row, idx) => {
      const initial = (row.full_name || '?').charAt(0).toUpperCase();
      const genderBg = row.gender === 'Male'
        ? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
        : 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)';

      tbody.innerHTML += '<tr>' +
        '<td class="table_td">' + (start + idx + 1) + '</td>' +
        '<td class="table_td"><img src="' + photoSrc(row.photo) + '" alt="Teacher Photo" style="width:48px;height:48px;object-fit:cover;border-radius:50%;border:2px solid #667eea;background:#f3f3f3;"></td>' +
        '<td class="table_td"><div style="display:flex;align-items:center;gap:10px;">' +
        '<div style="width:32px;height:32px;background:linear-gradient(135deg,#f093fb,#f5576c);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:600;font-size:0.9rem;">' + esc(initial) + '</div>' +
        esc(row.full_name) + '</div></td>' +
        '<td class="table_td">' + esc(row.email) + '</td>' +
        '<td class="table_td">' + esc(row.phone) + '</td>' +
        '<td class="table_td"><span style="padding:6px 12px;border-radius:20px;font-size:0.85rem;font-weight:600;background:linear-gradient(135deg,#4facfe,#00f2fe);color:white;display:inline-block;">' + esc(row.subject) + '</span></td>' +
        '<td class="table_td"><span style="padding:6px 12px;border-radius:20px;font-size:0.85rem;font-weight:600;background:' + genderBg + ';color:white;display:inline-block;">' + esc(row.gender) + '</span></td>' +
        '<td class="table_td">' + esc(row.joined_on) + '</td>' +
        '<td class="table_td">' +
        '<a href="#" class="btn-action btn-edit btn-edit-teacher" data-id="' + row.id + '"><i class="fas fa-edit"></i> Edit</a> ' +
        '<a href="#" class="btn-action btn-delete" onclick="softDelete(\'teacher\', ' + row.id + ', this)"><i class="fas fa-trash"></i> Remove</a>' +
        '</td></tr>';
    });
  }

  function renderPagination(pagination) {
    const nav = document.querySelector('nav[aria-label="Teachers pagination"] ul');
    if (!nav || !pagination) return;
    if (pagination.totalPages <= 1) {
      nav.innerHTML = '';
      return;
    }
    let html = '';
    if (pagination.page > 1) {
      html += '<li><a href="' + buildQuery({ page: pagination.page - 1 }) + '" class="btn-action btn-edit" style="padding:6px 14px;">&laquo; Prev</a></li>';
    }
    for (let i = 1; i <= pagination.totalPages; i++) {
      const cls = i === pagination.page ? 'btn-delete' : 'btn-edit';
      const style = i === pagination.page ? 'padding:6px 14px;pointer-events:none;opacity:0.85;' : 'padding:6px 14px;';
      html += '<li><a href="' + buildQuery({ page: i }) + '" class="btn-action ' + cls + '" style="' + style + '">' + i + '</a></li>';
    }
    if (pagination.page < pagination.totalPages) {
      html += '<li><a href="' + buildQuery({ page: pagination.page + 1 }) + '" class="btn-action btn-edit" style="padding:6px 14px;">Next &raquo;</a></li>';
    }
    nav.innerHTML = html;
  }

  function openEditModal(teacherId) {
    const editTeacherModal = document.getElementById('editTeacherModal');
    const editTeacherMsg = document.getElementById('editTeacherMsg');
    const editTeacherCurrentPhoto = document.getElementById('editTeacherCurrentPhoto');
    if (!editTeacherModal) return;

    fetch(backend + 'get_teacher.php?id=' + encodeURIComponent(teacherId), { credentials: 'include' })
      .then(res => res.json())
      .then(data => {
        if (!data.success) {
          alert(data.error || 'Failed to fetch teacher data.');
          return;
        }
        const t = data.teacher;
        document.getElementById('editTeacherId').value = t.id;
        document.getElementById('editTeacherName').value = t.full_name || '';
        document.getElementById('editTeacherEmail').value = t.email || '';
        document.getElementById('editTeacherPhone').value = t.phone || '';
        document.getElementById('editTeacherSubject').value = t.subject || '';
        document.getElementById('editTeacherGender').value = t.gender || '';
        if (editTeacherCurrentPhoto) {
          editTeacherCurrentPhoto.src = t.photo
            ? 'nyabzgallery/' + t.photo + '?t=' + Date.now()
            : 'nyabzgallery/default.svg';
        }
        if (editTeacherMsg) editTeacherMsg.textContent = '';
        if (typeof openModal === 'function') openModal('editTeacherModal');
        else {
          editTeacherModal.classList.add('active');
          editTeacherModal.style.display = 'flex';
        }
      })
      .catch(() => alert('Network error.'));
  }

  tbody.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-edit-teacher');
    if (!btn) return;
    e.preventDefault();
    const id = btn.getAttribute('data-id');
    if (id) openEditModal(id);
  });

  function loadTeachers() {
    fetch(backend + 'get_teachers_ajax.php' + buildQuery(), { credentials: 'include' })
      .then(r => r.json())
      .then(data => {
        if (data.error) return;
        renderTeachers(data);
        renderPagination(data.pagination);
      })
      .catch(() => {});
  }

  window.loadTeachers = loadTeachers;

  document.addEventListener('DOMContentLoaded', loadTeachers);
})();
