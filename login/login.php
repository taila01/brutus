<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-BR"><head>
<script src="/brutus/assets/tema.js?v=1"></script><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Minha conta | Brutus</title><link rel="icon" href="/brutus/img/favicon.svg" type="image/svg+xml"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"><link rel="stylesheet" href="/brutus/geral.css"><link rel="stylesheet" href="/brutus/interface.css?v=3">
</head><body class="brutus-urban" data-flavor="picles">
<?php include __DIR__ . '/../cabecalho.html'; ?>
<main class="auth-layout">
<section class="auth-story"><div><p class="eyebrow">SEU LUGAR NA MESA.</p><h1>CHEGA<br>MAIS,<br><em>BRUTAL.</em></h1><p>Entre na sua conta e deixe o próximo pedido com a gente.</p></div><p>SEM CERIMÔNIA. A CASA É SUA.</p></section>
<section class="auth-panel"><h2>DE VOLTA PRA CHAPA.</h2><p>Ainda não tem conta? <a href="/brutus/cadastro/cadastrar.php">Criar minha conta</a></p>
<?php if (isset($_SESSION['erro_login'])): ?><div class="auth-error" role="alert"><?= htmlspecialchars(strip_tags($_SESSION['erro_login']), ENT_QUOTES, 'UTF-8') ?></div><?php unset($_SESSION['erro_login']); endif; ?>
<form action="validacao.php" method="post"><label for="email">Seu e-mail</label><input type="email" id="email" name="email" maxlength="254" autocomplete="email" placeholder="voce@exemplo.com" autocomplete="email" required><label for="password">Sua senha</label><input type="password" id="password" name="password" placeholder="Digite sua senha" autocomplete="current-password" required><button class="primary-button" type="submit">Entrar na minha conta <span aria-hidden="true">↗</span></button></form><a class="secondary-button signup-button" href="/brutus/cadastro/cadastrar.php">Ainda não tenho conta · Criar conta</a><p class="auth-note">Só quer dar uma olhada? <a href="/brutus/cardapio/cardapio.php">Explore o cardápio</a></p></section>
</main>
<?php include __DIR__ . '/../rodape.html'; ?>
</body></html>
