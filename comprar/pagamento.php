<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" type="imagex/png" href="../img/logo/logo.png">
  <link rel="stylesheet" type="text/css" href="../geral.css" />
  <link rel="stylesheet" type="text/css" href="checkout_padronizado.css" />
  <link href='http://fonts.googleapis.com/css?family=Lato:400,700' rel='stylesheet' type='text/css'>
  <title>Brutus - Comprar</title>
</head>
<body>
<?php 
session_start(); // abre a sessão

    include_once "../cabecalho.html";
    include_once "conecta.php";
    if(!isset ($_SESSION['id_logado']) == true){ 
        header ('location: ../login/login.php');
    } 

    else{
        $cliente= $_SESSION['id_logado'];
        
        // Verificar se o endereço de entrega foi selecionado
        if(!isset($_SESSION['endereco_entrega']) || empty($_SESSION['endereco_entrega'])) {
            // Se não tiver endereço selecionado, redireciona para a página de endereço
            $_SESSION['erro'] = "Por favor, selecione um endereço de entrega antes de prosseguir para o pagamento.";
            header('location: endereco.php?modo=selecionar');
            exit;
        }
        
        // Buscar informações do endereço selecionado
        $endereco_id = $_SESSION['endereco_entrega'];
        $query_endereco = "SELECT * FROM endereco WHERE cod_endereco = $endereco_id AND fk_Usuario_codigo = $cliente";
        $resultado_endereco = mysqli_query($conn, $query_endereco);
        
        if(mysqli_num_rows($resultado_endereco) == 0) {
            // Se o endereço não for encontrado, redireciona para a página de endereço
            $_SESSION['erro'] = "Endereço não encontrado. Por favor, selecione um endereço válido.";
            header('location: endereco.php?modo=selecionar');
            exit;
        }
        
        $endereco = mysqli_fetch_assoc($resultado_endereco);
    
?>
    <div class="fin_comp">
        <h2 class="titu">Finalizar Compra</h2>
        <div class="pedido">
            <p class="resum">Resumo do pedido<p>
            <?php
                $total_carrinho=0;
                foreach ( $_SESSION['carrinho'] as $id => $qtd)
                {
                    $sql = "SELECT cod_item, nome, preco, imagem
                    FROM itens
                    WHERE cod_item = '$id'";
                    $resultado = mysqli_query($conn,$sql) or die (mysqli_error());
                    $linha = mysqli_fetch_array($resultado);
                    
                    //0 id, 1 nome, 2 preco e 3 imagem
                    $nome = $linha[1];
                    $preco = str_replace("," , "" , $linha[2] );
                    $subtota = $preco * $qtd;
                    $subtotal =  number_format($subtota, 2, ',', '.');
                    $valor = number_format($preco, 2, ',', '.');
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
                                <div class= 'linha_2'> 
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
                    <p class="subtot">R$ <?=$total_carrinh = number_format($total_carrinho,2,",",".") ?></p>
                </div>
                <div class="ent">
                    <p class="entr">Entrega</p>
                    <p class="gratis">Grátis</p>
                </div>
                <div class="tot">
                    <p class="tota">Total</p>
                    <p class="total">R$<?=$total_carrinh?></p>
                </div>
                <div class="vol">
                    <a class="voltar" href="../carrinho/carrinho.php">Voltar a sacola</a>
                </div>
            </div>
        </div>
<?php
        //parcelas
        $par1=  number_format($total_carrinho,2,",",".");
        $par2=  number_format($total_carrinho/2,2,",",".");
        $par3=  number_format($total_carrinho/3,2,",",".");
        $par4=  number_format($total_carrinho/4,2,",",".");
        $par5=  number_format($total_carrinho/5,2,",",".");
        $par6=  number_format($total_carrinho/6,2,",",".");
        $par7=  number_format($total_carrinho/7,2,",",".");
        $par8=  number_format($total_carrinho/8,2,",",".");
        $par9=  number_format($total_carrinho/9,2,",",".");
        $par10=  number_format($total_carrinho/10,2,",",".");
    
?>

    
<div class="pagamento-card">
    <p class="titulo">Pagamento</p>

    <!-- Exibir informações do endereço de entrega -->
    <div class="endereco-resumo">
        <p class="subtitulo">Endereço de entrega:</p>
        <div class="endereco-detalhe">
            <p><?php echo $endereco['rua']; ?>, <?php echo $endereco['numero']; ?></p>
            <p><?php echo $endereco['bairro']; ?>, <?php echo $endereco['cidade']; ?></p>
            <p>CEP: <?php echo $endereco['cep']; ?></p>
            <?php if(!empty($endereco['complemento'])): ?>
                <p>Complemento: <?php echo $endereco['complemento']; ?></p>
            <?php endif; ?>
            <p><a href="endereco.php?modo=selecionar" class="link">Alterar endereço</a></p>
        </div>
    </div>

    <form action="pag_compra.php" method="POST">
        <div class="tipo_pagamento">
            <p class="subtitulo">Selecione o tipo de pagamento:</p>

            <div class="opcao">
                <input type="radio" value="dinheiro" id="dinheiro" name="pagamento" required>
                <label for="dinheiro">Dinheiro</label>
            </div>

            <div class="opcao">
                <input type="radio" value="cartao" id="cartao" name="pagamento">
                <label for="cartao">Cartão (crédito/débito)</label>
            </div>

            <div class="opcao">
                <input type="radio" value="pix" id="pix" name="pagamento">
                <label for="pix">Pix</label>
            </div>
        </div>

        <p class="info_entrega">O pagamento será realizado no momento da entrega.</p>

        <!-- Campo oculto para passar o ID do endereço -->
        <input type="hidden" name="endereco_id" value="<?php echo $endereco_id; ?>">

        <div class="confirma">
            <button type="submit" name="btn_pag" class="btn-finalizar">Finalizar compra</button>
        </div>
    </form>
</div>

        <div class="etapas">
            <a href="identificacao.php" class="link">
                <div class="identificacao etapa-concluida">
                    <p class="etap"><img src="img/icone/perfil.png">Identificação<p>
                    <p class="dad_ver" >Dados confirmados</p>
                    <img class="verificado" src="icone/verificacao.png">
                </div>
            </a>
            <a href="endereco.php" class="link">
                <div class="pagamento etapa-concluida">
                    <p class="etap"><img src="icone/entrega.png">Endereco<p>
                    <p class="dad_ver" >Dados confirmados</p>
                    <img class="verificado" src="icone/verificacao.png">
                </div>
            </a>
        </div>
    </div>  

    <?php
    }
    include_once "../rodape.html";
?>
</body>
</html>

<script type="text/javascript">
/* Máscaras ER */
function mascara(o,f){
    v_obj=o
    v_fun=f
    setTimeout("execmascara()",1)
}
function execmascara(){
    v_obj.value=v_fun(v_obj.value)
}
function mcc(v){
    v=v.replace(/\D/g,"");
    v=v.replace(/^(\d{4})(\d)/g,"$1 $2");
    v=v.replace(/^(\d{4})\s(\d{4})(\d)/g,"$1 $2 $3");
    v=v.replace(/^(\d{4})\s(\d{4})\s(\d{4})(\d)/g,"$1 $2 $3 $4");
    return v;
}
function id( el ){
	return document.getElementById( el );
}
window.onload = function(){
	id('cc').onkeypress = function(){
		mascara( this, mcc );
	}
}
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.0/jquery.mask.js"></script>
<script>
 $(document).ready(function () { 
        var $CPF = $("#CPF");
        $CPF.mask('000.000.000-00', {reverse: true});
});

</script>
