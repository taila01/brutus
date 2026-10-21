<?php
session_start();
require_once __DIR__ . '/../../app/conexoes/pdo.php';
$categorias = [1 => ['hamburguer', 'Hambúrgueres artesanais'], 2 => ['kids', 'Para os pequenos'], 3 => ['combo', 'Combos'], 4 => ['acompanhamento', 'Acompanhamentos'], 5 => ['bebidas', 'Bebidas'], 6 => ['sobremesa', 'Final feliz']];
$somenteCombos = $somenteCombos ?? false;
if ($somenteCombos) { $categorias = [3 => $categorias[3]]; }
?>
<!DOCTYPE html>
<html lang="pt-BR"><head>
<script src="/brutus/assets/js/tema.js?v=1"></script><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Cardápio | Brutus</title><link rel="icon" href="/brutus/assets/img/favicon.svg" type="image/svg+xml"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"><link rel="stylesheet" href="/brutus/assets/css/geral.css"><link rel="stylesheet" href="/brutus/assets/css/cardapio/cardapio.css?v=3"><link rel="stylesheet" href="/brutus/assets/css/interface.css?v=3">
</head>
<body class="brutus-urban" data-flavor="<?= $somenteCombos ? 'cheddar' : 'tomate' ?>">
<?php include __DIR__ . '/../../app/views/layout/cabecalho.php'; ?>
<main>
<section class="menu-intro"><p class="eyebrow">A CHAPA TÁ NO ESQUEMA.</p><h1><?= $somenteCombos ? "JUNTO É BRUTAL." : "ESCOLHE. AMASSA. REPETE." ?></h1><p><?= $somenteCombos ? "Burger, acompanhamento e parceria de respeito. O trio que não decepciona." : "Dos clássicos aos sem limites. Aqui ninguém passa vontade." ?></p><span class="urban-page-sticker" aria-hidden="true">FEITO PRA<br>SUJAR O DEDO.</span><nav class="category-nav" aria-label="Categorias do cardápio"><?php foreach ($categorias as [$slug, $titulo]): ?><a href="#<?= $slug ?>"><?= $titulo ?></a><?php endforeach; ?></nav></section>
<?php foreach ($categorias as $id => [$slug, $titulo]):
$stmt = $conn->prepare('SELECT cod_item, nome, descricao, preco, imagem FROM itens WHERE fk_Categoria_cod_categoria = :categoria');
$stmt->execute(['categoria' => $id]);
?>
<section class="menu-section" id="<?= $slug ?>" data-category="<?= $id ?>"><div class="section-heading"><div><p class="eyebrow">FEITO NA CHAPA. ESCOLHIDO POR VOCÊ.</p><h2><?= $titulo ?></h2></div></div><div class="product-grid"><?php while ($produto = $stmt->fetch()) { include __DIR__ . '/../../app/views/components/produto-card.php'; } ?></div></section>
<?php endforeach; ?>
</main>
<?php include __DIR__ . '/../../app/views/layout/rodape.php'; ?>
</body></html>
