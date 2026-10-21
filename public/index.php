<?php
session_start();
require_once __DIR__ . '/../app/conexoes/pdo.php';
$produtos = $conn->query('SELECT cod_item, nome, descricao, preco, imagem FROM itens WHERE fk_Categoria_cod_categoria = 3 LIMIT 4')->fetchAll();
require_once __DIR__ . '/../app/helpers/imagem-produto.php';
$hero = $conn->query('SELECT nome, imagem FROM itens WHERE fk_Categoria_cod_categoria = 1 LIMIT 1')->fetch();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<script src="/brutus/assets/js/tema.js?v=1"></script>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Brutus — Respeita essa fome.</title>
<link rel="icon" href="/brutus/assets/img/favicon.svg" type="image/svg+xml">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="/brutus/assets/css/geral.css"><link rel="stylesheet" href="/brutus/assets/css/cardapio/cardapio.css?v=3">
<link rel="stylesheet" href="/brutus/assets/css/interface.css?v=3">
</head>
<body class=" brutus-urban" data-flavor="original">

<?php include __DIR__ . '/../app/views/layout/cabecalho.php'; ?>

<main>
<section class="urban-hero" aria-labelledby="urban-title">
  <div class="urban-hero-copy">
    <p class="urban-kicker"><span aria-hidden="true">✦</span> HAMBÚRGUER DE RESPEITO. FRESCURA ZERO.</p>
    <h1 id="urban-title">RESPEITA<br>ESSA <span class="urban-scribble">FOME.<svg viewBox="0 0 500 35" aria-hidden="true"><path d="M6 23Q170 0 490 16M34 31Q260 13 460 28"/></svg></span></h1>
    <p class="urban-description">Pão na mão. Molho no dedo. Sorriso na cara.<br>O resto a gente resolve na chapa.</p>
    <div class="urban-hero-buttons"><a class="primary-button" href="/brutus/cardapio/cardapio.php">BORA MATAR ESSA FOME <span aria-hidden="true">↗</span></a><a class="urban-text-link" href="#favoritos">Me mostra os combos <span aria-hidden="true">↓</span></a></div>
    <div class="urban-handnote">a dieta que lute.<svg viewBox="0 0 100 50" aria-hidden="true"><path d="M3 40Q60 48 90 5M73 9L91 3 95 23"/></svg></div>
  </div>
  <div class="urban-hero-visual">
    <div class="urban-photo"><span class="urban-tape" aria-hidden="true"></span><img src="<?= imagemProduto($hero ?: []) ?>" alt="Hambúrguer artesanal Brutus com queijo e bacon em prato escuro" fetchpriority="high" width="960" height="720"><div class="urban-photo-caption"><span>BRUTUS ORIGINAL</span><span>100% ATITUDE ↗</span></div></div>
    <div class="urban-star-sticker" aria-label="Sem medo de ser feliz">SEM MEDO<br>DE SER<br><strong>FELIZ.</strong></div>
    <span class="urban-small-sticker">AMASSA<br>SEM DÓ.</span>
    <svg class="urban-doodle" viewBox="0 0 170 110" aria-hidden="true"><path d="M9 78Q7 7 99 17M83 4L104 16 83 33M127 45L139 28M136 60L164 53M132 78L155 91"/></svg>
  </div>
</section>
<div class="urban-marquee" aria-label="Burger de verdade. Molho no dedo. Fome resolvida."><div><span>BURGER DE VERDADE</span><b aria-hidden="true">✦</b><span>MOLHO NO DEDO</span><b aria-hidden="true">✦</b><span>FOME RESOLVIDA</span><b aria-hidden="true">✦</b><span>ZERO FRESCURA</span><b aria-hidden="true">✦</b><span aria-hidden="true">BURGER DE VERDADE</span></div></div>
<section class="urban-favorites" id="favoritos" aria-labelledby="favorites-title"><div class="urban-section-inner">
  <div class="urban-section-header"><div><p class="urban-kicker">CHEGA JUNTO. ESCOLHE O SEU.</p><h2 id="favorites-title">O BONDE DOS<br><em>BRUTOS.</em></h2></div><div class="urban-section-aside"><span class="urban-handnote">é covardia de tão bom.</span><a class="secondary-button" href="/brutus/combos.php">VER TODOS OS COMBOS ↗</a></div></div>
  <div class="product-grid"><?php foreach ($produtos as $produto) { include __DIR__ . '/../app/views/components/produto-card.php'; } ?></div>
  <p class="urban-menu-note">Escolhe, aperta o + e já era. Seu pedido vai ganhando forma por aqui.</p>
</div></section>
<section class="urban-manifesto" aria-labelledby="manifesto-title"><span class="urban-manifesto-label">O NOSSO JEITO</span><h2 id="manifesto-title">A GENTE LEVA<br>O BURGER A SÉRIO.<br><span>O RESTO, NEM TANTO.</span></h2><div class="urban-manifesto-bottom"><p>Ingrediente de verdade, combinação sem timidez<br>e um bom motivo pra juntar a galera.</p><a class="urban-text-link" href="/brutus/sobre/aboutus.php">Conhece a nossa história ↗</a></div><span class="urban-manifesto-star" aria-hidden="true">✳</span></section>
<section class="urban-deals"><div><p class="urban-kicker">ALÔ, FOME GRANDE.</p><h2>O PRECINHO<br>TAMBÉM <em>AMASSA.</em></h2><p>As ofertas da casa estão te chamando.<br>E seria falta de educação não dar uma olhada.</p><a class="primary-button" href="/brutus/promocoes.php">ME LEVA PRAS OFERTAS ↗</a></div><div class="urban-deal-sticker"><span>SEU PRÓXIMO</span><strong>“SÓ MAIS<br>UMA MORDIDA”</strong><span>COMEÇA AQUI. ↙</span></div></section>
</main>
<?php include __DIR__ . '/../app/views/layout/rodape.php'; ?>
</body></html>
