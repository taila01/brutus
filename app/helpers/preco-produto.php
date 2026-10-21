<?php
function precoProduto(array $produto): float
{
    $original = (float) str_replace(',', '.', $produto['preco']);
    static $ofertas;
    $ofertas ??= require __DIR__ . '/../../config/promocoes.php';
    $hoje = (new DateTimeImmutable('now', new DateTimeZone('America/Sao_Paulo')))->format('Y-m-d');
    foreach ($ofertas as $oferta) {
        if ((int) $oferta['item_id'] !== (int) ($produto['cod_item'] ?? 0)) continue;
        if (isset($oferta['ate']) && $oferta['ate'] < $hoje) continue;
        $preco = (float) $oferta['preco'];
        if ($preco > 0 && $preco < $original) return $preco;
    }
    return $original;
}
