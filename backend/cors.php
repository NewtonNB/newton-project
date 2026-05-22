<?php
/**
 * CORS for Netlify (or other) frontend calling PHP API on a different host.
 * Set NYABZ_ALLOWED_ORIGINS in server env, e.g.:
 *   https://your-site.netlify.app,https://www.yourschool.org
 */
function nyabz_cors_preflight(): void {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $allowed = getenv('NYABZ_ALLOWED_ORIGINS') ?: '';
    if (!$origin || !$allowed) {
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
        return;
    }
    $list = array_map('trim', explode(',', $allowed));
    if (!in_array($origin, $list, true) && !in_array('*', $list, true)) {
        return;
    }
    header('Access-Control-Allow-Origin: ' . ($list[0] === '*' ? '*' : $origin));
    if ($list[0] !== '*') {
        header('Access-Control-Allow-Credentials: true');
    }
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}
