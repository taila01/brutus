<!DOCTYPE html>
<html lang="pt-BR">
<head>
<script src="/brutus/assets/js/tema.js?v=1"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="imagex/png" href="/brutus/assets/img/favicon.svg">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/brutus/assets/css/geral.css">
    <link rel="stylesheet" type="text/css" href="/brutus/assets/css/carrinho/carrinho.css">
<link rel="stylesheet" href="/brutus/assets/css/interface.css?v=3">
</head>

<body class="brutus-urban" data-flavor="molho">

<?php
session_start();
require_once __DIR__ . '/../../app/helpers/preco-produto.php';
require_once __DIR__ . '/../../app/helpers/imagem-produto.php';
$_SESSION["carrinho"] = $_SESSION["carrinho"] ?? [];

if (!isset($_SESSION['id_logado'])) {
    header('location: ../login/login.php');
    exit;
} else {

    include_once __DIR__ . '/../../app/views/layout/cabecalho.php';
    $cliente = $_SESSION['id_logado'];
?>

<div class="sacola">
    <hr class="linha">

<?php
    include_once __DIR__ . '/../../app/conexoes/mysqli.php';

    if ($cliente == 41) {
        echo "<p class='n_pedo'>Entre como cliente!</p>";
    } else {

        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = array();
        }

        $avisoCarrinho='';
        $acao=is_string($_GET['acao']??null)?$_GET['acao']:'';
        $id=filter_var($_GET['id']??null,FILTER_VALIDATE_INT);
        if ($id && $id>0 && in_array($acao,['add','del','up'],true)) {
            if ($acao==='add') {
                $consulta=$conn->prepare('SELECT cod_item FROM itens WHERE cod_item=?');$consulta->bind_param('i',$id);$consulta->execute();
                if ($consulta->get_result()->fetch_row()) {
                    $atual=(int)($_SESSION['carrinho'][$id]??0);
                    if ($atual<99) $_SESSION['carrinho'][$id]=$atual+1;
                    else $avisoCarrinho='O limite é de 99 unidades por produto.';
                }
            } elseif ($acao==='del') unset($_SESSION['carrinho'][$id]);
            elseif (isset($_SESSION['carrinho'][$id])) {
                if ($_SESSION['carrinho'][$id]<=1) unset($_SESSION['carrinho'][$id]);
                else $_SESSION['carrinho'][$id]--;
            }
        }
?>

    <h1 class="titulo">SEU BONDE DE SABORES.</h1>
    <?php if ($avisoCarrinho): ?><p class="alert alert-warning" role="alert"><?= $avisoCarrinho ?></p><?php endif; ?>

    <div class="carrinho">
        <form action="carrinho.php?acao=up" method="post">

<?php
    $total_carrinho = 0;
    if (count($_SESSION['carrinho']) == 0) {
        echo "<div class='empty-state'><p class='eyebrow'>Sua próxima mordida começa aqui</p><h2>A sacola está esperando.</h2><p>Escolha seu favorito e monte um pedido com a sua cara.</p><a class='primary-button' href='/brutus/cardapio/cardapio.php'>Explorar cardápio ↗</a></div>";
    } else {

        $total_carrinho = 0;

        foreach ($_SESSION['carrinho'] as $id => $qtd) {

            $sql = "SELECT * FROM itens WHERE cod_item = '$id'";
            $resultado = mysqli_query($conn, $sql);
            $linha = mysqli_fetch_array($resultado);

            $preco = precoProduto($linha);
            $subtotal = $preco * $qtd;
            $total_carrinho += $subtotal;

            $valor_formatado = number_format($preco, 2, ',', '.');
            $subtotal_formatado = number_format($subtotal, 2, ',', '.');
?>

            <div class="produto">
                <div class="imagem">
                    <img src="<?= imagemProduto($linha) ?>" alt="<?= htmlspecialchars($linha[1], ENT_QUOTES, 'UTF-8') ?>" class="imagem_prod">
                </div>

                <div class="inf">
                    <div class="linha_1">
                        <p class="nome"><?= $linha[1] ?></p>
                        <a href="carrinho.php?acao=del&id=<?= $id ?>">
                            <img class="rem" src="/brutus/assets/img/lixeira.png">
                        </a>
                    </div>

                    <hr class="linha_nome">

                    <div class="linha_2">
                        <div class="quantidade">
                            <a href="carrinho.php?acao=up&id=<?= $id ?>">
                                <button type="button" class="add_prod">-</button>
                            </a>

                            <input type="text" value="<?= $qtd ?>" class="quant" disabled>

                            <a href="carrinho.php?acao=add&id=<?= $id ?>">
                                <button type="button" class="add_prod">+</button>
                            </a>
                        </div>

                        <p class="valor">R$ <?= $valor_formatado ?></p>
                        <p class="subtotal">Total: R$ <?= $subtotal_formatado ?></p>
                    </div>
                </div>
            </div>

<?php
        }
    }
?>

        </form>
    </div>

    <div class="pedido">
        <div class="resum_comp">
            <p class="resumo">Resumo do pedido</p>

            <p class="sub">Subtotal</p>
            <p class="sub_val">R$ <?= number_format($total_carrinho, 2, ',', '.') ?></p>

            <div class="entrega">
                <p class="sub">Entrega</p>
                <p class="sub_val">Grátis</p>
            </div>

            <p class="sub">Total</p>
            <p class="sub_val">R$ <?= number_format($total_carrinho, 2, ',', '.') ?></p>
        </div>

<?php if (count($_SESSION['carrinho']) != 0) { ?>
        <div class="fim_compra">
            <a class="finaliza" href="../comprar/identificacao.php">Finalizar Pedido</a>
        </div>
<?php } ?>

        <div class="cont">
            <a class="continuar" href="../cardapio/cardapio.php">Continuar comprando...</a>
        </div>
    </div>

<?php
    }
}
?>

</div>

<hr class="linha_hr">

<?php include_once __DIR__ . '/../../app/views/layout/rodape.php'; ?>

</body>
</html>
