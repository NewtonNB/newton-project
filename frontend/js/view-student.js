/**
 * view-student.js — loads and renders student list for view_student.html
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
      class_id: p.get('class_id') || '0',
      level: p.get('level') || '',
      status: p.get('status') || '',
      search: p.get('search') || '',
      page: p.get('page') || '1'
    };
  }

  function buildQuery(extra) {
    const q = Object.assign(getParams(), extra || {});
    const parts = [];
    if (q.class_id && q.class_id !== '0') parts.push('class_id=' + encodeURIComponent(q.class_id));
    if (q.level) parts.push('level=' + encodeURIComponent(q.level));
    if (q.status) parts.push('status=' + encodeURIComponent(q.status));
    if (q.search) parts.push('search=' + encodeURIComponent(q.search));
    if (q.page && q.page !== '1') parts.push('page=' + encodeURIComponent(q.page));
    return parts.length ? '?' + parts.join('&') : '';
  }

  function populateClassFilters(classes, selectedId) {
    const filterSelect = document.getElementById('class_id');
    if (filterSelect && filterSelect.options.length <= 1) {
      classes.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.id;
        opt.textContent = c.name;
        if (String(c.id) === String(selectedId)) opt.selected = true;
        filterSelect.appendChild(opt);
      });
    }
    const params = getParams();
    ['class_id', 'level', 'status'].forEach(name => {
      const el = document.querySelector('[name="' + name + '"]');
      if (el && params[name] !== undefined && params[name] !== '') {
        el.value = params[name];
      }
    });
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) searchInput.value = params.search || '';
  }

  function classOptionsHtml(classes, selectedId) {
    return classes.map(c =>
      '<option value="' + c.id + '"' + (String(c.id) === String(selectedId) ? ' selected' : '') + '>' + esc(c.name) + '</option>'
    ).join('');
  }

  function renderStudents(data) {
    populateClassFilters(data.classes, data.filters.class_id);
    const students = data.students || [];
    tbody.innerHTML = '';

    if (!students.length) {
      tbody.innerHTML = '<tr><td colspan="7" class="table_td" style="text-align:center;padding:40px;color:#718096;font-style:italic;">' +
        '<i class="fas fa-users" style="font-size:2rem;margin-bottom:10px;display:block;color:#a0aec0;"></i>' +
        'No students found. Add your first student to get started!</td></tr>';
      return;
    }

    students.forEach((row, idx) => {
      const initial = (row.username || '?').charAt(0).toUpperCase();
      const utype = row.usertype || 'student';
      const grad = utype === 'admin'
        ? 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)'
        : 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
      const classOpts = '<option value="">Class</option>' + classOptionsHtml(data.classes, row.class_id);

      tbody.innerHTML += '<tr>' +
        '<td class="table_td">' + (idx + 1 + (data.pagination.page - 1) * data.pagination.perPage) + '</td>' +
        '<td class="table_td"><div style="display:flex;align-items:center;gap:10px;">' +
        '<div style="width:32px;height:32px;background:linear-gradient(135deg,#667eea,#764ba2);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:600;font-size:0.9rem;">' + esc(initial) + '</div>' +
        esc(row.username) + '</div></td>' +
        '<td class="table_td">' + esc(row.email) + '</td>' +
        '<td class="table_td">' + esc(row.phone) + '</td>' +
        '<td class="table_td">' + (row.class_name ? esc(row.class_name) : '<span style="color:#aaa;">-</span>') + '</td>' +
        '<td class="table_td"><span style="padding:6px 12px;border-radius:20px;font-size:0.85rem;font-weight:600;background:' + grad + ';color:white;display:inline-block;">' + esc(utype.charAt(0).toUpperCase() + utype.slice(1)) + '</span></td>' +
        '<td class="table_td"><div style="display:flex;flex-direction:column;gap:8px;align-items:flex-start;">' +
        '<form class="assign-class-form" style="display:flex;gap:8px;align-items:center;width:100%;">' +
        '<input type="hidden" name="assign_student_id" value="' + row.id + '">' +
        '<select name="assign_class_id" required style="padding:6px 10px;border-radius:8px;border:1.5px solid #667eea;font-size:0.95rem;background:#f8f8ff;color:#333;font-weight:500;min-width:90px;">' + classOpts + '</select>' +
        '<button type="submit" class="btn-action btn-edit" style="padding:7px 14px;min-width:90px;"><i class="fas fa-save"></i> Update</button></form>' +
        '<div style="display:flex;gap:8px;width:100%;">' +
        '<button class="btn-action btn-edit btn-edit-student" data-id="' + row.id + '" data-username="' + esc(row.username) + '" data-email="' + esc(row.email) + '" data-phone="' + esc(row.phone) + '" data-usertype="' + esc(utype) + '" data-class_id="' + (row.class_id || '') + '" style="flex:1;min-width:0;"><i class="fas fa-edit"></i> Edit</button>' +
        '<a href="#" class="btn-action btn-delete" onclick="softDelete(\'student\', ' + row.id + ', this)" style="flex:1;min-width:0;"><i class="fas fa-trash"></i> Remove</a>' +
        '</div></div></td></tr>';
    });

    tbody.querySelectorAll('.assign-class-form').forEach(form => {
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        const fd = new FormData(form);
        fetch(backend + 'assign_student_class.php', {
          method: 'POST',
          body: fd,
          credentials: 'include'
        })
          .then(r => r.json())
          .then(d => {
            if (d.success) loadStudents();
          });
      });
    });
  }

  function renderPagination(pagination) {
    const nav = document.querySelector('nav[aria-label="Students pagination"] ul');
    if (!nav || !pagination) return;
    const q = getParams();
    let html = '';
    if (pagination.page > 1) {
      html += '<li><a href="' + buildQuery({ page: pagination.page - 1 }) + '" class="btn-action btn-edit" style="padding:6px 14px;">&laquo; Prev</a></li>';
    }
    for (let i = 1; i <= pagination.totalPages; i++) {
      const active = i === pagination.page ? 'btn-edit' : '';
      html += '<li><a href="' + buildQuery({ page: i }) + '" class="btn-action ' + active + '" style="padding:6px 14px;">' + i + '</a></li>';
    }
    if (pagination.page < pagination.totalPages) {
      html += '<li><a href="' + buildQuery({ page: pagination.page + 1 }) + '" class="btn-action btn-edit" style="padding:6px 14px;">Next &raquo;</a></li>';
    }
    nav.innerHTML = html;
  }

  function loadStudents() {
    fetch(backend + 'get_students_ajax.php' + buildQuery(), { credentials: 'include' })
      .then(r => r.json())
      .then(data => {
        if (data.error) return;
        renderStudents(data);
        renderPagination(data.pagination);
        document.querySelectorAll('form[action*="export_students"] input[type="hidden"]').forEach(el => {
          const n = el.getAttribute('name');
          if (n && data.filters[n] !== undefined) el.value = data.filters[n];
        });
      })
      .catch(() => {});
  }

  document.addEventListener('DOMContentLoaded', loadStudents);
})();
