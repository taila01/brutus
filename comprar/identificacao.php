<?php
session_start();

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

    $cpf = $row_dados['cpf'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="imagex/png" href="../img/logo/logo.png">
    <link rel="stylesheet" type="text/css" href="../geral.css" />
    <link rel="stylesheet" type="text/css" href="checkout_padronizado.css" />
    <title>Brutus - Comprar</title>
</head>
<body>
    <div class="fin_comp">
        <h2 class="titu">Finalizar Compra</h2>
        <div class="identificacao">
            <p class="dado"><img src="icone/perfil.png">Dados Pessoais<p>
            <form action="dad_pes.php" method="POST">
                <div class="dados">
                    <label class="dados_label">Email</label></br>
                    <input type="email" value="<?= $row_dados['email'] ?>" name="email" class="insere_dados" required>
                </div>
                <div class="dados">
                    <label class="dados_label">Nome</label></br>
                    <input type="text" value="<?= $row_dados['nome'] ?>" name="nome" class="insere_dados" required>
                </div>
                <div class="dados">
                    <label class="dados_label">CPF</label></br>
                    <input type="text" id="cpf" value="<?= $cpf ?>" name="cpf" class="insere_dados" required>
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
                    $preco = str_replace(",", "", $linha[2]);
                    $subtotal = $preco * $qtd;
                    $subtota = $subtotal;
                    $subtotal = number_format($subtotal, 2, ",", ".");
                    $valor = number_format($preco, 2, ",", ".");
                    $total_carrinho += $subtota;

                    $caminhoImagem = "../produtos/" . $linha[3];
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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.0/jquery.mask.js"></script>
    <script>
        $(document).ready(function () {
            var $CPF = $("#cpf");
            $CPF.mask('000.000.000-00', {reverse: true});
        });
    </script>

<?php
    }
    include_once "../rodape.html";
?>

</body>
</html>
