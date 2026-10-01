<?php
declare(strict_types=1);

$appRoot = dirname(__DIR__);
$requestedPath = (string) ($_GET['__path'] ?? '/');
$path = rawurldecode(parse_url($requestedPath, PHP_URL_PATH) ?: '/');

unset($_GET['__path'], $_REQUEST['__path']);

$query = [];
parse_str((string) ($_SERVER['QUERY_STRING'] ?? ''), $query);
unset($query['__path']);
$_SERVER['QUERY_STRING'] = http_build_query($query);

if ($path === '/fgck_joyland' || str_starts_with($path, '/fgck_joyland/')) {
    $path = substr($path, strlen('/fgck_joyland')) ?: '/';
}

$relativePath = trim($path, '/');
if ($relativePath === '') {
    $relativePath = 'index.php';
}

if (
    str_contains($relativePath, "\0") ||
    str_contains($relativePath, '\\') ||
    preg_match('#(^|/)\.\.?(/|$)#', $relativePath)
) {
    http_response_code(404);
    exit('Not Found');
}

$filePath = realpath($appRoot . DIRECTORY_SEPARATOR . $relativePath);
if ($filePath === false || !is_file($filePath)) {
    http_response_code(404);
    exit('Not Found');
}

$isPhp = strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) === 'php';
$phpRoot = explode('/', $relativePath, 2)[0];
$allowedPhpPath = $relativePath === 'index.php'
    || in_array($phpRoot, ['auth', 'member', 'staff', 'api'], true);

if ($isPhp && $allowedPhpPath) {
    $_SERVER['REQUEST_URI'] = $path . ($_SERVER['QUERY_STRING'] !== '' ? '?' . $_SERVER['QUERY_STRING'] : '');
    $_SERVER['SCRIPT_NAME'] = $path;
    $_SERVER['PHP_SELF'] = $path;
    $_SERVER['SCRIPT_FILENAME'] = $filePath;
    require $filePath;
    exit;
}

$isPublicAsset = str_starts_with($relativePath, 'assets/')
    || $relativePath === 'manifest.webmanifest';

if (!$isPublicAsset) {
    http_response_code(404);
    exit('Not Found');
}

$mimeTypes = [
    'css' => 'text/css; charset=utf-8',
    'gif' => 'image/gif',
    'ico' => 'image/x-icon',
    'jpeg' => 'image/jpeg',
    'jpg' => 'image/jpeg',
    'js' => 'application/javascript; charset=utf-8',
    'json' => 'application/json; charset=utf-8',
    'png' => 'image/png',
    'svg' => 'image/svg+xml',
    'webmanifest' => 'application/manifest+json',
    'webp' => 'image/webp',
    'woff' => 'font/woff',
    'woff2' => 'font/woff2',
];
$extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
header('Content-Type: ' . ($mimeTypes[$extension] ?? 'application/octet-stream'));
header('Content-Length: ' . (string) filesize($filePath));
readfile($filePath);