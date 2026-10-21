<?php
session_start();
require __DIR__ . '/../app/conexoes/pdo.php';
$ofertas = require __DIR__ . '/../config/promocoes.php';
$ativas = [];
$hoje = (new DateTimeImmutable('now', new DateTimeZone('America/Sao_Paulo')))->format('Y-m-d');
foreach ($ofertas as $oferta) {
    if (isset($oferta['ate']) && $oferta['ate'] < $hoje || !is_numeric($oferta['preco'] ?? null) || $oferta['preco'] <= 0) continue;
    $stmt = $conn->prepare('SELECT cod_item, nome, descricao, preco, imagem FROM itens WHERE cod_item = ?');
    $stmt->execute([(int) $oferta['item_id']]);
    $item = $stmt->fetch();
    if ($item && (float) $oferta['preco'] < (float) str_replace(',', '.', $item['preco'])) {
        $item['preco_oferta'] = $oferta['preco'];
        $item['oferta_ate'] = $oferta['ate'] ?? null;
        $ativas[] = $item;
    }
}
?>
<!DOCTYPE html><html lang="pt-BR"><head>
<script src="/brutus/assets/js/tema.js?v=1"></script><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Promoções | Brutus</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="/brutus/assets/css/geral.css"><link rel="stylesheet" href="/brutus/assets/css/cardapio/cardapio.css?v=3"><link rel="stylesheet" href="/brutus/assets/css/interface.css?v=3"></head><body class="brutus-urban" data-flavor="lima">
<?php include __DIR__ . '/../app/views/layout/cabecalho.php'; ?>
<main><section class="menu-intro"><p class="eyebrow">PREÇO BOM. FOME GRANDE.</p><h1>O PRECINHO<br>TAMBÉM AMASSA.</h1><span class="urban-page-sticker" aria-hidden="true">FOME GRANDE.<br>CONTA LEVE.</span><p>As ofertas da casa pra quem sabe aproveitar cada mordida.</p></section><section class="menu-section">
<?php if ($ativas): ?><div class="product-grid promo-grid"><?php foreach ($ativas as $produto): ?><div><?php include __DIR__ . '/../app/views/components/produto-card.php'; ?></div><?php endforeach; ?></div>
<?php else: ?><div class="empty-state"><span class="empty-symbol" aria-hidden="true">✦</span><p class="eyebrow">Novidades a caminho</p><h2>Uma pausa nas ofertas.<br>O sabor continua.</h2><p>No momento, não há promoções ativas. Enquanto isso, explore os combos da casa pelos preços do cardápio.</p><a class="primary-button" href="/brutus/combos.php">Conhecer os combos ↗</a></div><?php endif; ?>
</section></main><?php include __DIR__ . '/../app/views/layout/rodape.php'; ?></body></html>
