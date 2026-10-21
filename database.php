<?php
// Configuração compartilhada por todas as conexões do projeto.
function brutusDatabaseConfig(): array
{
    static $config;
    if ($config !== null) return $config;
    $local = is_file(__DIR__ . '/.env.local.php') ? require __DIR__ . '/.env.local.php' : [];
    $connection = $local['connection'] ?? 'local';
    if ($connection === 'aiven') {
        $config = $local['aiven'] ?? [];
        if (empty($config['password'])) {
            throw new RuntimeException('Preencha a senha do Aiven em .env.local.php.');
        }
        if (empty($config['ssl_ca']) || !is_readable($config['ssl_ca'])) {
            throw new RuntimeException('Salve o certificado CA do Aiven em certs/aiven-ca.pem.');
        }
        $config['ssl'] = true;
        $config['socket'] = null;
    } elseif ($connection === 'local') {
        $config = [
            'host' => 'localhost', 'port' => 3306, 'database' => 'brutus',
            'username' => 'root', 'password' => '', 'ssl' => false,
            'socket' => is_file('/tmp/brutus-db-socket/mysqld.sock') || file_exists('/tmp/brutus-db-socket/mysqld.sock')
                ? '/tmp/brutus-db-socket/mysqld.sock' : null,
        ];
    } else {
        throw new RuntimeException('Conexão inválida: use local ou aiven em .env.local.php.');
    }
    return $config;
}

function brutusPDO(): PDO
{
    static $connection;
    if ($connection instanceof PDO) return $connection;
    $c = brutusDatabaseConfig();
    $dsn = $c['socket']
        ? 'mysql:unix_socket=' . $c['socket'] . ';dbname=' . $c['database'] . ';charset=utf8mb4'
        : 'mysql:host=' . $c['host'] . ';port=' . (int) $c['port'] . ';dbname=' . $c['database'] . ';charset=utf8mb4';
    $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false];
    if ($c['ssl']) {
        $options[PDO::MYSQL_ATTR_SSL_CA] = $c['ssl_ca'];
        $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
    }
    $connection = new PDO($dsn, $c['username'], $c['password'], $options);
    if ($c['ssl'] && !$connection->query("SHOW SESSION STATUS LIKE 'Ssl_cipher'")->fetchColumn(1)) {
        throw new RuntimeException('A conexão com o Aiven precisa usar TLS.');
    }
    return $connection;
}

function brutusMysqli(): mysqli
{
    static $connection;
    if ($connection instanceof mysqli) return $connection;
    $c = brutusDatabaseConfig();
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $connection = mysqli_init();
    $connection->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);
    if ($c['ssl']) {
        $connection->ssl_set(null, null, $c['ssl_ca'], null, null);
        $connection->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);
    }
    $connection->real_connect($c['host'], $c['username'], $c['password'], $c['database'], (int) $c['port'], $c['socket'], $c['ssl'] ? MYSQLI_CLIENT_SSL : 0);
    $connection->set_charset('utf8mb4');
    if ($c['ssl'] && !$connection->query("SHOW SESSION STATUS LIKE 'Ssl_cipher'")->fetch_row()[1]) {
        throw new RuntimeException('A conexão com o Aiven precisa usar TLS.');
    }
    return $connection;
}
