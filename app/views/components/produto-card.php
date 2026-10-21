<?php
require_once __DIR__ . '/../../helpers/imagem-produto.php';
require_once __DIR__ . '/../../helpers/preco-produto.php';
$precoOriginal = (float) str_replace(',', '.', $produto['preco']);
$precoAtual = precoProduto($produto);
$quantidade = max(0, min(99, (int) ($_SESSION['carrinho'][$produto['cod_item']] ?? 0)));
$escape = static fn($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<article class="product-card">
  <div class="product-image"><img src="<?= imagemProduto($produto) ?>" alt="<?= $escape($produto['nome']) ?>" loading="lazy" decoding="async" width="960" height="720"></div>
  <div class="product-info">
    <?php if ($precoAtual < $precoOriginal): ?><span class="offer-badge">OFERTA DA CASA</span><?php endif; ?>
    <h3><?= $escape($produto['nome']) ?></h3>
    <p><?= $escape($produto['descricao']) ?></p>
    <div class="product-bottom"><span class="product-price"><?php if ($precoAtual < $precoOriginal): ?><span class="old-price">R$ <?= number_format($precoOriginal, 2, ',', '.') ?></span><?php endif; ?>R$ <?= number_format($precoAtual, 2, ',', '.') ?></span><div class="product-action <?= $quantidade ? 'is-added' : '' ?>" data-cart-product="<?= (int) $produto['cod_item'] ?>" data-product-name="<?= $escape($produto['nome']) ?>">
      <div class="quantity-controls" aria-label="Quantidade de <?= $escape($produto['nome']) ?>">
        <button type="button" class="quantity-minus" data-cart-action="remove" aria-label="Remover uma unidade de <?= $escape($produto['nome']) ?>" <?= $quantidade ? '' : 'hidden' ?>>−</button>
        <output class="product-quantity" aria-label="Quantidade no pedido" <?= $quantidade ? '' : 'hidden' ?>><?= $quantidade ?></output>
        <button type="button" class="add-button" data-cart-action="add" aria-label="Adicionar <?= $escape($produto['nome']) ?> ao pedido" <?= $quantidade>=99 ? 'disabled' : '' ?>>+</button>
      </div>
      <span class="product-added" <?= $quantidade ? '' : 'hidden' ?>>✓ Adicionado</span>
      <small class="cart-item-error" role="alert" hidden></small>
    </div></div>
  </div>
</article>
