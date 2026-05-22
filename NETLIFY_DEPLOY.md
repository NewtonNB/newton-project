# Deploy public website to Netlify (no backend)

This build publishes **only the public school website** — home, about, gallery, events, contact, etc. No PHP server required.

Admin login, dashboard, and database features stay on XAMPP / your PHP host when you need them locally.

## Build

From the **project root** (not `frontend/`):

```powershell
cd C:\xampp\htdocs\school-project
npm run build
```

Output: `dist/` folder.

## Netlify settings

| Setting | Value |
|---------|--------|
| Build command | `npm run build` |
| Publish directory | `dist` |
| Environment variables | *(none required)* |

## What works on Netlify

- Homepage with animated statistics (demo numbers)
- About, academics, staff, anthem, clubs, O/A level pages
- Photo gallery and dynamic gallery (from `gallery-api.json`)
- Events list and event details (demo events 1–9)
- Contact page (shows message to email/call the school)
- Navbar, footer, images, CSS

## What does not work (by design)

- Admin login / dashboard
- Form submissions to PHP (admission apply, contact DB, event registration to server)
- Live data from MySQL

## Deploy steps

1. Push repo to GitHub, or drag-and-drop the `dist` folder to Netlify.
2. Set publish directory to `dist` if using Git.
3. Open your `*.netlify.app` URL.

## Local preview

```powershell
npm run build
npm run preview
```

Open `http://localhost:4173`

## Full stack later (optional)

If you host PHP elsewhere, build with API proxy:

```powershell
$env:NETLIFY_BACKEND_URL="https://your-php-server.com"
$env:STATIC_ONLY="0"
npm run build
```
