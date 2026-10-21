<?php
// Compatibilidade entre o backup antigo e as telas da versão mais recente.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../cardapio/conexao.php';
$colunas = static function ($table) use ($conn) {
    return $conn->query("SHOW COLUMNS FROM `$table`")->fetchAll(PDO::FETCH_COLUMN);
};
if (!in_array('identificacao', $colunas('endereco'))) $conn->exec("ALTER TABLE endereco ADD identificacao VARCHAR(50) NOT NULL DEFAULT 'Entrega'");
if (!in_array('principal', $colunas('endereco'))) $conn->exec('ALTER TABLE endereco ADD principal TINYINT(1) DEFAULT 0');
if (!in_array('cod_endereco', $colunas('pedidos'))) $conn->exec('ALTER TABLE pedidos ADD cod_endereco INT NULL');
$conn->exec('ALTER TABLE pedidos MODIFY tipo_pagamento VARCHAR(20) NOT NULL');
if (in_array('cod_pedido', $colunas('itens_pedido'))) $conn->exec('ALTER TABLE itens_pedido CHANGE cod_pedido codigo_pedido INT NOT NULL');
$conn->exec('CREATE TABLE IF NOT EXISTS hist_status_ped (cod_hist INT AUTO_INCREMENT PRIMARY KEY, cod_pedido INT NOT NULL, cod_status INT NOT NULL, data_hora DATETIME NOT NULL)');
if (!in_array('preco_unitario', $colunas('itens_pedido'))) $conn->exec('ALTER TABLE itens_pedido ADD preco_unitario DECIMAL(10,2) NULL');
echo "Estrutura local atualizada, preservando os registros.\n";
