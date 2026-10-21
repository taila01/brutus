<?php
session_start();
require_once __DIR__."/../validacao.php";
require_once __DIR__ . "/../preco-produto.php";
require_once __DIR__ . "/../imagem-produto.php";
$_SESSION["carrinho"] = $_SESSION["carrinho"] ?? [];

include_once "../cabecalho.html";
include_once "conecta.php";
if (!isset($_SESSION['id_logado']) == true) {
    header('location: ../login/login.php');
}

else {
    $cliente = $_SESSION['id_logado'];

    $query_dados = "SELECT * FROM usuario WHERE CODIGO = $cliente";
    $dados = mysqli_query($conn, $query_dados);
    $row_dados = mysqli_fetch_assoc($dados);

    $row_dados = array_merge($row_dados, $_SESSION['pessoa_old'] ?? []);
    unset($_SESSION['pessoa_old']);
    $cpf = $row_dados['cpf'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<script src="/brutus/assets/tema.js?v=1"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="imagex/png" href="../img/logo/logo.png">
    <link rel="stylesheet" type="text/css" href="../geral.css" />
    <link rel="stylesheet" type="text/css" href="checkout_padronizado.css" />
    <title>Brutus - Comprar</title>
<link rel="stylesheet" href="/brutus/interface.css?v=3">
</head>
<body class="brutus-urban" data-flavor="gergelim">
    <div class="fin_comp">
<?php formularioFeedback("checkout_erros"); ?>
        <h2 class="titu">Finalizar Compra</h2>
        <div class="identificacao">
            <p class="dado"><img src="icone/perfil.png">Dados Pessoais<p>
            <form action="dad_pes.php" method="POST">
                <div class="dados">
                    <label class="dados_label">Email</label></br>
                    <input type="email" value="<?= htmlspecialchars($row_dados['email'], ENT_QUOTES, 'UTF-8') ?>" name="email" maxlength="254" autocomplete="email" class="insere_dados" required>
                </div>
                <div class="dados">
                    <label class="dados_label">Nome</label></br>
                    <input type="text" value="<?= htmlspecialchars($row_dados['nome'], ENT_QUOTES, 'UTF-8') ?>" name="nome" maxlength="100" minlength="3" class="insere_dados" required>
                </div>
                <div class="dados">
                    <label class="dados_label">CPF</label></br>
                    <input type="text" id="cpf" value="<?= htmlspecialchars($cpf, ENT_QUOTES, 'UTF-8') ?>" name="cpf" maxlength="14" inputmode="numeric" class="insere_dados" required>
                </div>
                <div class="confirma">
                    <button type="submit" name="btn_dados" class="salva_dados">Ir para entrega</button>
                </div>
            </form>
        </div>
        <div class="etapas">
            <div class="entrega">
                <p class="etap"><img src="icone/entrega.png">Entrega<p>
                <p>Aguardando confirmação dos dados</p>
            </div>
            <div class="pagamento">
                <p class="etap"><img src="icone/pagamento.png">Pagamento<p>
                <p>Aguardando preenchimento dos dados</p>
            </div>
        </div>
        <div class="pedido">
            <p class="resum">Resumo do pedido<p>
            <?php
                $total_carrinho = 0;
                foreach ($_SESSION['carrinho'] as $id => $qtd)
                {
                    $sql = "SELECT cod_item, nome, preco, imagem
                    FROM itens
                    WHERE cod_item = '$id'";
                    $resultado = mysqli_query($conn, $sql) or die (mysqli_error($conn));
                    $linha = mysqli_fetch_array($resultado);

                    $nome = $linha[1];
                    $preco = precoProduto($linha);
                    $subtotal = $preco * $qtd;
                    $subtota = $subtotal;
                    $subtotal = number_format($subtotal, 2, ",", ".");
                    $valor = number_format($preco, 2, ",", ".");
                    $total_carrinho += $subtota;

                    $caminhoImagem = imagemProduto($linha);
                    echo "<div class='produto'>
                                <div class='quantidade'>
                                    <p class='quant'> $qtd<p/>
                                </div>
                                <div class='imagem'>";
                            echo "<img src='$caminhoImagem' class='imagem_prod'>
                               </div>
                                <div class='inf'>
                                    <div class='linha_1'>
                                        <p class='nome'>$linha[1]</p>
                                    </div>
                                    <div class='linha_2'>
                                        <p class='valor'>R$ $valor</p>
                                        <p class='subtotal'>Total: R$ $subtotal</p>";?>
                                    </div>
                                </div>
                            </div>
                            <?php
                }
            ?>
            <hr class="linha">
            <div class="preco">
                <div class="sub">
                    <p class="subt">Subtotal</p>
                    <p class="subtot">R$ <?= $total_carrinho = number_format($total_carrinho, 2, ",", ".") ?></p>
                </div>
                <div class="ent">
                    <p class="entr">Entrega</p>
                    <p class="gratis">Grátis</p>
                </div>
                <div class="tot">
                    <p class="tota">Total</p>
                    <p class="total">R$<?= $total_carrinho ?></p>
                </div>
                <div class="vol">
                    <a class="voltar" href="../carrinho/carrinho.php">Voltar a sacola</a>
                </div>
            </div>
        </div>
    </div>

<?php
    }
    include_once "../rodape.html";
?>

</body>
</html>
