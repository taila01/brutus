<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

// Versões corrigidas para CVE-2026-109618 e CVE-2026-109619.
$minimas = ['8.2' => '8.2.35', '8.3' => '8.3.36', '8.4' => '8.4.27', '8.5' => '8.5.12'];
$serie = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
$minima = $minimas[$serie] ?? null;
if ($minima === null || version_compare(PHP_VERSION, $minima, '<') || preg_match('/(?:dev|alpha|beta|RC)/i', PHP_VERSION)) {
    fwrite(STDERR, 'PHP ' . PHP_VERSION . ': utilize uma versão estável corrigida: ' . implode(', ', $minimas) . " ou posterior da mesma série.\n");
    exit(1);
}
$ausentes = array_filter(['pdo_mysql', 'mysqli', 'mbstring'], static fn($ext) => !extension_loaded($ext));
if ($ausentes) {
    fwrite(STDERR, 'Extensões ausentes: ' . implode(', ', $ausentes) . ".\n");
    exit(1);
}
echo 'PHP ' . PHP_VERSION . ': versão mínima corrigida e extensões conferidas.' . PHP_EOL;
