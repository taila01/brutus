<?php
// Roteador exclusivo do servidor de desenvolvimento do PHP.
if (PHP_SAPI !== 'cli-server') {
    http_response_code(404);
    exit;
}

$public = realpath(__DIR__ . '/../public');
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
if ($path === '/' || $path === '/brutus') {
    header('Location: /brutus/', true, 302);
    exit;
}
if (!str_starts_with($path, '/brutus/') || str_contains($path, "\0")) {
    http_response_code(404);
    exit('Página não encontrada.');
}
$relative = substr($path, strlen('/brutus/'));
$file = realpath($public . '/' . ($relative === '' ? 'index.php' : $relative));
if (!$file || !str_starts_with($file, $public . DIRECTORY_SEPARATOR) || !is_file($file)) {
    http_response_code(404);
    exit('Página não encontrada.');
}

$extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
if ($extension === 'php') {
    $_SERVER['SCRIPT_FILENAME'] = $file;
    $_SERVER['SCRIPT_NAME'] = '/brutus/' . ($relative ?: 'index.php');
    $_SERVER['PHP_SELF'] = $_SERVER['SCRIPT_NAME'];
    require $file;
    return;
}
$types = [
    'css' => 'text/css; charset=UTF-8', 'js' => 'text/javascript; charset=UTF-8',
    'svg' => 'image/svg+xml', 'png' => 'image/png', 'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg', 'webp' => 'image/webp', 'gif' => 'image/gif',
    'ico' => 'image/x-icon', 'otf' => 'font/otf', 'ttf' => 'font/ttf',
];
if (!isset($types[$extension])) {
    http_response_code(404);
    exit;
}
header('Content-Type: ' . $types[$extension]);
header('Content-Length: ' . filesize($file));
if ($_SERVER['REQUEST_METHOD'] !== 'HEAD') readfile($file);
