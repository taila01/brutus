<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../app/database.php';
try {
    $config = brutusDatabaseConfig();
    $pdo = brutusPDO();
    $mysqli = brutusMysqli();
    $produtos = (int) $pdo->query('SELECT COUNT(*) FROM itens')->fetchColumn();
    $outroTotal = (int) $mysqli->query('SELECT COUNT(*) FROM itens')->fetch_row()[0];
    if ($produtos !== $outroTotal) throw new RuntimeException('Resultados de conexão divergentes.');
    echo 'Conexão ' . ($config['ssl'] ? 'Aiven com TLS' : 'local') . " OK em PDO e MySQLi. Produtos: $produtos.\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'Falha na conexão: ' . $e->getMessage() . "\n");
    exit(1);
}
