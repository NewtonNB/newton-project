/**
 * page-data.js
 * Loads dynamic data for HTML pages that previously used inline PHP.
 */
(function () {
  'use strict';

  const scriptSrc = document.currentScript ? document.currentScript.src : '';
  const jsDir = scriptSrc.substring(0, scriptSrc.lastIndexOf('/') + 1);
  const frontendBase = jsDir.substring(0, jsDir.lastIndexOf('js/'));
  const backendBase = (window.NYABZ_CONFIG && window.NYABZ_CONFIG.backend) ||
    frontendBase.replace(/\/?$/, '../backend/');
  const staticOnly = !!(window.NYABZ_CONFIG && window.NYABZ_CONFIG.staticOnly);

  const currentPage = window.location.pathname
    .split('/').pop()
    .replace(/\.html$/, '').replace(/\.php$/, '') || 'index';

  function esc(s) {
    if (s == null) return '';
    const d = document.createElement('div');
    d.textContent = String(s);
    return d.innerHTML;
  }

  function fmtDate(d) {
    if (!d) return 'No date set';
    const dt = new Date(d);
    return isNaN(dt.getTime()) ? d : dt.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
  }

  function fmtDateTime(d) {
    if (!d) return '';
    const dt = new Date(d);
    return isNaN(dt.getTime()) ? d : dt.toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit' });
  }

  // ── Homepage statistics counters ─────────────────────────────────────────
  if (currentPage === 'index' && staticOnly) return;

  if (currentPage === 'index') {
    fetch(backendBase + 'get_statistics.php', { credentials: 'include' })
      .then(r => r.json())
      .then(data => {
        const targets = [
          data.totalStudents || 0,
          data.totalTeachers || 0,
          data.graduatedStudents || 0,
          data.activityCount || 12
        ];
        const counters = document.querySelectorAll('.counter');
        counters.forEach((el, i) => {
          if (targets[i] != null) el.setAttribute('data-target', targets[i]);
        });
        // Re-run counter animation if already initialized
        counters.forEach(counter => {
          const target = +counter.getAttribute('data-target') || 0;
          counter.innerText = '0';
          const updateCounter = () => {
            const count = +counter.innerText;
            const increment = Math.max(1, Math.ceil(target / 100));
            if (count < target) {
              counter.innerText = Math.min(target, count + increment);
              setTimeout(updateCounter, 18);
            } else {
              counter.innerText = target;
            }
          };
          updateCounter();
        });
      })
      .catch(() => {});
  }

  // ── Admin dashboard ──────────────────────────────────────────────────────
  if (currentPage === 'dashboard') {
    fetch(backendBase + 'get_dashboard_data.php', { credentials: 'include' })
      .then(r => {
        if (!r.ok) throw new Error('unauthorized');
        return r.json();
      })
      .then(data => {
        if (data.error) return;

        const stats = document.querySelectorAll('.dashboard-stats .stat-value');
        if (stats[0]) stats[0].textContent = data.studentCount;
        if (stats[1]) stats[1].textContent = data.teacherCount;
        if (stats[2]) stats[2].textContent = data.classCount;
        if (stats[3]) stats[3].textContent = 'UGX ' + Number(data.feeCollected || 0).toLocaleString();

        const eventStats = document.querySelectorAll('.event-stats .stat-value');
        if (eventStats[0]) eventStats[0].textContent = data.totalEvents;
        if (eventStats[1]) eventStats[1].textContent = data.totalEventRegistrations;
        if (eventStats[2]) eventStats[2].textContent = data.upcomingEvents;

        const trashLink = document.querySelector('.recent-messages-title a[href*="trash_messages"]');
        if (trashLink && data.trashCount > 0) {
          trashLink.innerHTML = '<i class="fa-solid fa-trash"></i> Trash (' + data.trashCount + ')';
          trashLink.style.display = '';
        } else if (trashLink && !data.trashCount) {
          trashLink.style.display = 'none';
        }

        // Popular events table (second recent-messages-card table)
        const tables = document.querySelectorAll('.recent-messages-card table.recent-messages-table tbody');
        const popularBody = tables[0];
        if (popularBody) {
          popularBody.innerHTML = '';
          const events = data.popularEvents || [];
          if (!events.length) {
            popularBody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:#666;">No events found</td></tr>';
          } else {
            events.forEach(ev => {
              const isUpcoming = ev.date && new Date(ev.date) >= new Date();
              const status = isUpcoming
                ? '<span style="color:#27ae60;font-weight:600;">Upcoming</span>'
                : '<span style="color:#e74c3c;font-weight:600;">Past</span>';
              popularBody.innerHTML += '<tr>' +
                '<td>' + esc(ev.title) + '</td>' +
                '<td>' + esc(fmtDate(ev.date)) + '</td>' +
                '<td><span style="background:linear-gradient(135deg,#667eea,#764ba2);color:white;padding:4px 12px;border-radius:12px;font-weight:600;">' + esc(ev.registration_count) + '</span></td>' +
                '<td>' + status + '</td>' +
                '<td><a href="announcements.html" class="reply-btn"><i class="fa-solid fa-edit"></i> Manage</a></td>' +
                '</tr>';
            });
          }
        }

        // Recent registrations (third table)
        const regBody = tables[1];
        if (regBody) {
          regBody.innerHTML = '';
          const regs = data.recentRegistrations || [];
          if (!regs.length) {
            regBody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:#666;">No event registrations yet</td></tr>';
          } else {
            regs.forEach(reg => {
              regBody.innerHTML += '<tr>' +
                '<td>' + esc(reg.id) + '</td>' +
                '<td>' + esc(reg.event_title || 'Unknown Event') + '</td>' +
                '<td>' + esc(reg.name) + '</td>' +
                '<td>' + esc(reg.email) + '</td>' +
                '<td>' + esc(reg.phone) + '</td>' +
                '<td>' + esc(fmtDateTime(reg.created_at)) + '</td>' +
                '<td><a href="view_event_registrations.html?event_id=' + encodeURIComponent(reg.event_id) + '" class="reply-btn"><i class="fa-solid fa-eye"></i> View Details</a></td>' +
                '</tr>';
            });
          }
        }

        // Recent messages (fourth table)
        const msgBody = tables[2];
        if (msgBody) {
          msgBody.innerHTML = '';
          const msgs = data.recentMessages || [];
          if (!msgs.length) {
            msgBody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:#666;">No contact messages yet</td></tr>';
          } else {
            msgs.forEach(row => {
              const msg = row.message ? (row.message.length > 40 ? row.message.substring(0, 40) + '...' : row.message) : '';
              msgBody.innerHTML += '<tr>' +
                '<td>' + esc(row.id) + '</td>' +
                '<td>' + esc(row.first_name) + '</td>' +
                '<td>' + esc(row.last_name) + '</td>' +
                '<td>' + esc(row.email) + '</td>' +
                '<td>' + esc(msg) + '</td>' +
                '<td>' + esc(row.submitted_at) + '</td>' +
                '<td><button type="button" class="reply-btn" data-id="' + esc(row.id) + '" data-firstname="' + esc(row.first_name) + '" data-email="' + esc(row.email) + '"><i class="fa-solid fa-reply"></i> Reply</button> ' +
                '<button type="button" class="remove-msg-btn" data-id="' + esc(row.id) + '" style="background:linear-gradient(135deg,#ff6b6b,#ee5a52);color:#fff;border:none;border-radius:8px;padding:7px 14px;font-size:0.85rem;font-weight:600;cursor:pointer;margin-left:6px;"><i class="fa-solid fa-trash"></i> Remove</button></td>' +
                '</tr>';
            });
          }
          bindDashboardMessageHandlers(backendBase);
        }

        // Chart
        if (window.Chart) {
          const canvas = document.getElementById('dashboardChart');
          if (window.dashboardChartRef) {
            window.dashboardChartRef.destroy();
          }
          if (canvas) {
            const chartData = [
              data.studentCount, data.teacherCount, data.classCount,
              data.totalEvents, data.totalEventRegistrations
            ];
            window.dashboardChartRef = new Chart(canvas.getContext('2d'), {
              type: 'bar',
              data: {
                labels: ['Students', 'Teachers', 'Classes', 'Events', 'Registrations'],
                datasets: [{
                  label: 'Statistics',
                  data: chartData,
                  backgroundColor: [
                    'rgba(102, 126, 234, 0.8)',
                    'rgba(240, 147, 251, 0.8)',
                    'rgba(79, 172, 254, 0.8)',
                    'rgba(255, 154, 158, 0.8)',
                    'rgba(168, 237, 234, 0.8)'
                  ],
                  borderColor: ['#667eea', '#f093fb', '#4facfe', '#ff9a9e', '#a8edea'],
                  borderWidth: 2,
                  borderRadius: 12,
                  borderSkipped: false,
                  maxBarThickness: 60
                }]
              },
              options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                  y: {
                    beginAtZero: true,
                    ticks: { color: '#2d3748', font: { size: 14, weight: '600' } },
                    grid: { color: 'rgba(102, 126, 234, 0.1)', drawBorder: false }
                  },
                  x: {
                    ticks: { color: '#2d3748', font: { size: 14, weight: '600' } },
                    grid: { display: false }
                  }
                }
              }
            });
          }
        }
      })
      .catch(() => {});
  }

  function bindDashboardMessageHandlers(backend) {
    document.querySelectorAll('.reply-btn').forEach(btn => {
      btn.addEventListener('click', function () {
        const modal = document.getElementById('replyModal');
        if (!modal) return;
        modal.style.display = 'flex';
        document.getElementById('replyMessageId').value = btn.getAttribute('data-id');
        document.getElementById('replyTo').value = btn.getAttribute('data-email');
        document.getElementById('modalRecipient').textContent = btn.getAttribute('data-email') + ' (' + btn.getAttribute('data-firstname') + ')';
        document.getElementById('replyMessage').value = '';
        const success = document.getElementById('replySuccessMsg');
        if (success) success.style.display = 'none';
      });
    });

    const closeBtn = document.getElementById('closeReplyModal');
    const modal = document.getElementById('replyModal');
    if (closeBtn && modal) {
      closeBtn.onclick = () => { modal.style.display = 'none'; };
      modal.addEventListener('click', e => { if (e.target === modal) modal.style.display = 'none'; });
    }

    let removeId = null;
    const removeModal = document.getElementById('removeModal');
    document.querySelectorAll('.remove-msg-btn').forEach(btn => {
      btn.addEventListener('click', function () {
        removeId = this.dataset.id;
        if (removeModal) removeModal.style.display = 'flex';
      });
    });
    const cancelRemove = document.getElementById('cancelRemove');
    const confirmRemove = document.getElementById('confirmRemove');
    if (cancelRemove) {
      cancelRemove.onclick = () => {
        if (removeModal) removeModal.style.display = 'none';
        removeId = null;
      };
    }
    if (confirmRemove) {
      confirmRemove.onclick = function () {
        if (!removeId) return;
        this.textContent = 'Removing...';
        this.disabled = true;
        fetch(backend + 'delete_message_ajax.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'id=' + encodeURIComponent(removeId)
        })
          .then(r => r.json())
          .then(d => {
            if (d.success) {
              const btn = document.querySelector('.remove-msg-btn[data-id="' + removeId + '"]');
              if (btn) {
                const row = btn.closest('tr');
                if (row) {
                  row.style.transition = 'opacity 0.4s';
                  row.style.opacity = '0';
                  setTimeout(() => row.remove(), 400);
                }
              }
            }
            if (removeModal) removeModal.style.display = 'none';
            removeId = null;
            this.textContent = 'Yes, Remove';
            this.disabled = false;
          })
          .catch(() => {
            if (removeModal) removeModal.style.display = 'none';
            removeId = null;
            this.textContent = 'Yes, Remove';
            this.disabled = false;
          });
      };
    }
  }
})();
