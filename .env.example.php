<?php
// Troque para 'aiven' depois de preencher a senha e baixar o certificado.
return [
    'connection' => 'local',
    'aiven' => [
        'host' => 'mysql-378e2950-tailacristtina-f1c3.e.aivencloud.com',
        'port' => 16678,
        'database' => 'brutus',
        'username' => 'avnadmin',
        'password' => '', // Coloque a senha do Aiven entre as aspas.
        'ssl_ca' => __DIR__ . '/certs/aiven-ca.pem',
    ],
];
