/**
 * Static Netlify build — public frontend only, no PHP backend required.
 */
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.join(__dirname, '..');
const srcDir = path.join(root, 'frontend');
const outDir = path.join(root, 'dist');

const backendUrl = (process.env.VITE_API_URL || process.env.NETLIFY_BACKEND_URL || '').replace(/\/$/, '');
const staticOnly = !backendUrl || process.env.STATIC_ONLY !== '0';

/** Public pages shipped to Netlify (no admin / login). */
const PUBLIC_HTML = new Set([
  'index.html', 'about.html', 'Academics.html', 'anthem.html',
  'staff.html', 'nonstaff.html', 'olevel.html', 'alevel.html',
  'clubs.html', 'events.html', 'event_details.html',
  'gallery.html', 'viewgallery.html', 'dynamic_gallery.html',
  'contactus.html', 'admission.html',
  'navbar.html', 'modern-footer.html'
]);

const SKIP_DIRS = new Set(['node_modules', '.git', 'public']);
const SKIP_EXT = new Set(['.php']);

function rmrf(dir) {
  if (fs.existsSync(dir)) fs.rmSync(dir, { recursive: true, force: true });
}

function shouldCopyFile(rel) {
  const ext = path.extname(rel).toLowerCase();
  if (SKIP_EXT.has(ext)) return false;
  const parts = rel.split(/[/\\]/);
  if (parts.some((p) => SKIP_DIRS.has(p))) return false;
  if (ext === '.html' && staticOnly && !PUBLIC_HTML.has(path.basename(rel))) return false;
  return true;
}

function copyRecursive(from, to, rel = '') {
  const entries = fs.readdirSync(from, { withFileTypes: true });
  for (const ent of entries) {
    const relPath = rel ? `${rel}${path.sep}${ent.name}` : ent.name;
    const src = path.join(from, ent.name);
    const dest = path.join(to, ent.name);
    if (ent.isDirectory()) {
      if (!shouldCopyFile(relPath + path.sep)) {
        fs.mkdirSync(dest, { recursive: true });
        copyRecursive(src, dest, relPath);
        continue;
      }
      fs.mkdirSync(dest, { recursive: true });
      copyRecursive(src, dest, relPath);
    } else {
      if (!shouldCopyFile(relPath)) continue;
      fs.mkdirSync(path.dirname(dest), { recursive: true });
      let content = fs.readFileSync(src);
      const ext = path.extname(ent.name).toLowerCase();
      if (['.html', '.js', '.css', '.json'].includes(ext)) {
        let text = content.toString('utf8');
        if (staticOnly) {
          text = text.replace(/\.\.\/backend\/get_gallery_images\.php/g, 'gallery-api.json');
          text = text.replace(/href="manage_gallery\.html"/g, 'href="gallery.html"');
          text = text.replace(/href='manage_gallery\.html'/g, "href='gallery.html'");
          const counterDefaults = ['1500', '120', '450', '12'];
          let ci = 0;
          text = text.replace(/data-target=""/g, () => {
            const v = counterDefaults[ci++] || '0';
            return `data-target="${v}"`;
          });
        } else {
          text = text.replace(/\.\.\/backend\//g, '/backend/');
        }
        content = Buffer.from(text, 'utf8');
      }
      fs.writeFileSync(dest, content);
    }
  }
}

function injectConfigScript(html) {
  const tag = '<script src="/config.js"></script>';
  if (html.includes('config.js')) return html;
  if (html.includes('<script src="js/includes.js"')) {
    return html.replace('<script src="js/includes.js"', `${tag}\n<script src="js/includes.js"`);
  }
  if (/<\/body>/i.test(html)) return html.replace(/<\/body>/i, `${tag}\n</body>`);
  return html + tag;
}

function patchHtmlFiles(dir) {
  for (const ent of fs.readdirSync(dir, { withFileTypes: true })) {
    const p = path.join(dir, ent.name);
    if (ent.isDirectory()) patchHtmlFiles(p);
    else if (ent.name.endsWith('.html')) {
      let html = fs.readFileSync(p, 'utf8');
      html = injectConfigScript(html);
      fs.writeFileSync(p, html, 'utf8');
    }
  }
}

function buildGalleryApi() {
  const capPath = path.join(srcDir, 'gallery_captions.json');
  if (!fs.existsSync(capPath)) return;
  const cap = JSON.parse(fs.readFileSync(capPath, 'utf8'));
  const images = (cap.images || [])
    .filter((img) => fs.existsSync(path.join(srcDir, 'nyabzgallery', img.filename)))
    .map((img) => ({
      ...img,
      caption: img.caption || img.filename,
      thumbnail: 'nyabzgallery/' + img.filename,
      full_url: 'nyabzgallery/' + img.filename,
      likes: img.likes || 0,
      views: img.views || 0,
      tags: []
    }));
  const categories = [...new Set(images.map((i) => i.category).filter(Boolean))];
  fs.writeFileSync(
    path.join(outDir, 'gallery-api.json'),
    JSON.stringify({ success: true, images, total: images.length, categories, stats: { total: images.length } })
  );
}

function writeRedirects() {
  const lines = [];
  if (!staticOnly && backendUrl) {
    lines.push(`/backend/*  ${backendUrl}/backend/:splat  200`);
  }
  lines.push('/  /index.html  200');
  fs.writeFileSync(path.join(outDir, '_redirects'), lines.join('\n') + '\n');
}

function writeConfigJs() {
  const body = staticOnly
    ? `window.NYABZ_CONFIG = {
  staticOnly: true,
  frontend: '/',
  stats: { totalStudents: 1500, totalTeachers: 120, graduatedStudents: 450, activityCount: 12 }
};
`
    : `window.NYABZ_CONFIG = {
  staticOnly: false,
  backend: ${JSON.stringify(backendUrl + '/backend/')},
  frontend: '/'
};
`;
  fs.writeFileSync(path.join(outDir, 'config.js'), body);
}

console.log('Building for Netlify...');
console.log('  Mode:', staticOnly ? 'STATIC (public site only)' : 'FULL (with API proxy)');
console.log('  Output:', outDir);

rmrf(outDir);
fs.mkdirSync(outDir, { recursive: true });
copyRecursive(srcDir, outDir);
patchHtmlFiles(outDir);
buildGalleryApi();
writeConfigJs();
writeRedirects();

const pages = fs.readdirSync(outDir).filter((f) => f.endsWith('.html')).length;
console.log(`Done — ${pages} public pages in dist/`);
if (staticOnly) {
  console.log('Deploy folder: dist  |  Build command: npm run build  |  Publish: dist');
}
