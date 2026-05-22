# Frontend routing: HTML vs PHP

The site uses **static `.html` shells** plus shared `js/includes.js` and **backend JSON APIs**. PHP pages remain as fallbacks where needed.

## Use `.html` (ready)

| Area | Pages |
|------|--------|
| **Auth** | `login.html`, `logout.html` |
| **Public site** | `index.html`, `about.html`, `Academics.html`, `anthem.html`, `staff.html`, `nonstaff.html`, `olevel.html`, `alevel.html`, `clubs.html`, `events.html`, `event_details.html`, `gallery.html`, `viewgallery.html`, `dynamic_gallery.html`, `contactus.html` |
| **Admin (HTML + API)** | `dashboard.html`, `view_student.html`, `view_teacher.html`, `manage_gallery.html`, `dynamic_gallery_admin.html`, `view_event_registrations.html`, `announcements.html`, `manage_classes.html`, `manage_subjects.html`, `manage_admins.html`, `attendance.html`, `admission.html`, `exam_schedule.html`, `report_cards.html`, `settings.html`, `user.html`, `trash.html`, `trash_messages.html`, `view_payment_details.html` |
| **Student** | `studenthome.html` |

## Shared JS

| File | Role |
|------|------|
| `js/includes.js` | Navbar, sidebar, footer, session guard, delete modal |
| `js/page-data.js` | `index.html`, `dashboard.html` stats/charts |
| `js/view-student.js` | `view_student.html` |
| `js/view-teacher.js` | `view_teacher.html` |
| `js/admin-data.js` | All admin pages listed in `ADMIN_DATA_PAGES` inside `includes.js` |

## Still `.php` (backend tools or not migrated)

- Fee tools: `../backend/fee_collection.php`, `fee_status.php`
- Marks: `../backend/enter_marks.php`
- SMS / newsletter: `../backend/send_sms.php`, `send_newsletter.php`
- Matching `.php` files in `frontend/` still work if opened directly

## Entry points

- Public home: `http://localhost/school-project/frontend/index.html`
- Admin login: `login.html` → `dashboard.html`
- Student login: `login.html` → `studenthome.html`

## Adding a new HTML page

1. Add `backend/get_*_ajax.php` (and mutations as needed).
2. Extend `admin-data.js` or a dedicated loader.
3. Add the page name to `ADMIN_DATA_PAGES` in `includes.js` if using `admin-data.js`.
4. Point `admin_sidebar.html` and dashboard links to `.html`.
