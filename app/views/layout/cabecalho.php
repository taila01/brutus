<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="/brutus/assets/css/geral.css">
<link rel="stylesheet" href="/brutus/assets/css/interface.css?v=3">
<link rel="stylesheet" href="/brutus/assets/css/urban.css?v=3">
<link rel="stylesheet" href="/brutus/assets/css/tema.css?v=1">
<div class="urban-topline"><span>FEITO NA CHAPA. COMIDO COM AS MÃOS.</span><span aria-hidden="true">✦</span><span>SEM CERIMÔNIA. COM MUITO MOLHO.</span></div>
<header class="site-header">
  <nav class="site-nav" aria-label="Navegação principal">
    <a class="wordmark" href="/brutus/" aria-label="Brutus início">BRUTUS<span>BURGER HOUSE</span></a>
    <div class="nav-main">
      <a href="/brutus/cardapio/cardapio.php" <?= str_contains($_SERVER['SCRIPT_NAME'] ?? '', 'cardapio/') ? 'aria-current="page"' : '' ?>>Cardápio</a>
      <a href="/brutus/combos.php" <?= str_contains($_SERVER['SCRIPT_NAME'] ?? '', 'combos.php') ? 'aria-current="page"' : '' ?>>Combos</a>
      <a href="/brutus/promocoes.php" <?= str_contains($_SERVER['SCRIPT_NAME'] ?? '', 'promocoes.php') ? 'aria-current="page"' : '' ?>>Promoções</a>
      <a href="/brutus/sobre/aboutus.php">Nossa história</a>
      <a href="#contato">Contato</a>
    </div>
    <div class="nav-actions">
      <button type="button" class="theme-toggle" data-theme-toggle aria-label="Ativar modo noturno" title="Ativar modo noturno" aria-pressed="false">
        <svg class="theme-moon" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.9 13.1A9 9 0 0 1 10.9 3.1 9 9 0 1 0 20.9 13.1Z"/></svg>
        <svg class="theme-sun" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M2 12h2m16 0h2M4.9 4.9l1.4 1.4m11.4 11.4 1.4 1.4M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
      </button>
      <?php if (isset($_SESSION['id_logado'])): ?>
      <a class="account-link" href="/brutus/login/redirecionamento.php" aria-label="Minha conta"><i class="fas fa-user" aria-hidden="true"></i><span>Minha conta</span></a>
      <?php else: ?>
      <div class="account-links"><a class="account-link" href="/brutus/login/login.php"><i class="fas fa-user" aria-hidden="true"></i><span>Entrar</span></a><a class="signup-link" href="/brutus/cadastro/cadastrar.php">Criar conta</a></div>
      <?php endif; ?>
      <a class="bag-link" href="/brutus/carrinho/carrinho.php"><i class="fas fa-shopping-bag" aria-hidden="true"></i><span>Meu pedido</span><span class="cart-count" data-cart-count aria-label="Itens no pedido" <?= empty($_SESSION['carrinho']) ? 'hidden' : '' ?>><?= array_sum($_SESSION['carrinho'] ?? []) ?></span></a>
    </div>
  </nav>
</header>
