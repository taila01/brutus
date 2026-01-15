<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="imagex/png" href="../img/logo/logo.png">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../geral.css">
    <link rel="stylesheet" type="text/css" href="carrinho.css">
</head>

<body>

<?php
session_start();

if (!isset($_SESSION['id_logado'])) {
    header('location: ../login/login.php');
} else {

    include_once "../cabecalho.html";
    $cliente = $_SESSION['id_logado'];
?>

<div class="sacola">
    <hr class="linha">

<?php
    include_once "conecta.php";

    if ($cliente == 41) {
        echo "<p class='n_pedo'>Entre como cliente!</p>";
    } else {

        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = array();
        }

        if (isset($_GET['acao'])) {

            if ($_GET['acao'] == 'add') {
                $id = $_GET['id'];

                if (!isset($_SESSION['carrinho'][$id])) {
                    $_SESSION['carrinho'][$id] = 1;
                } else {
                    $_SESSION['carrinho'][$id]++;
                }
            }

            if ($_GET['acao'] == 'del') {
                $id = $_GET['id'];
                unset($_SESSION['carrinho'][$id]);
            }

            if ($_GET['acao'] == 'up') {
                $id = $_GET['id'];

                if ($_SESSION['carrinho'][$id] == 1) {
                    unset($_SESSION['carrinho'][$id]);
                } else {
                    $_SESSION['carrinho'][$id]--;
                }
            }
        }
?>

    <h1 class="titulo">Meu carrinho</h1>

    <div class="carrinho">
        <form action="carrinho.php?acao=up" method="post">

<?php
    if (count($_SESSION['carrinho']) == 0) {
        echo "<p class='n_ped'>Não há produto no carrinho!</p>";
    } else {

        $total_carrinho = 0;

        foreach ($_SESSION['carrinho'] as $id => $qtd) {

            $sql = "SELECT * FROM itens WHERE cod_item = '$id'";
            $resultado = mysqli_query($conn, $sql);
            $linha = mysqli_fetch_array($resultado);

            $preco = str_replace(",", "", $linha[3]);
            $subtotal = $preco * $qtd;
            $total_carrinho += $subtotal;

            $valor_formatado = number_format($preco, 2, ',', '.');
            $subtotal_formatado = number_format($subtotal, 2, ',', '.');
?>

            <div class="produto">
                <div class="imagem">
                    <img src="../produtos/<?= $linha[4] ?>" class="imagem_prod">
                </div>

                <div class="inf">
                    <div class="linha_1">
                        <p class="nome"><?= $linha[1] ?></p>
                        <a href="carrinho.php?acao=del&id=<?= $id ?>">
                            <img class="rem" src="../img/lixeira.png">
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

<?php include_once "../rodape.html"; ?>

</body>
</html>
