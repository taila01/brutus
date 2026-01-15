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
<?php 
    session_start(); 

    include_once "../cabecalho.html";
    include_once "conecta.php";
    if(!isset ($_SESSION['id_logado']) == true){ 
        header ('location: ../login/login.php');
        exit;
    } 

    $cep = "";
    $cidade = "";
    $bairro = "";
    $rua = "";
    $numero = "";
    $complemento = "";
    $cliente = $_SESSION['id_logado'];
    $modo = isset($_GET['modo']) ? $_GET['modo'] : 'selecionar';
    $endereco_id = isset($_GET['endereco_id']) ? $_GET['endereco_id'] : 0;
    
    $query_enderecos = "SELECT * FROM endereco WHERE fk_Usuario_codigo = $cliente ORDER BY principal DESC";
    $enderecos = mysqli_query($conn, $query_enderecos);
    $tem_enderecos = mysqli_num_rows($enderecos) > 0;
    
    if ($modo == 'editar' && $endereco_id > 0) {
        $query_endereco = "SELECT * FROM endereco WHERE cod_endereco = $endereco_id AND fk_Usuario_codigo = $cliente";
        $resultado = mysqli_query($conn, $query_endereco);
        
        if (mysqli_num_rows($resultado) > 0) {
            $endereco = mysqli_fetch_assoc($resultado);
            $cep = $endereco['cep'];
            $cidade = $endereco['cidade'];
            $bairro = $endereco['bairro'];
            $rua = $endereco['rua'];
            $numero = $endereco['numero'];
            $complemento = isset($endereco['complemento']) ? $endereco['complemento'] : '';
        }
    }

    function mask($val, $mask)
    {
        if(empty($val)) return '';
        
        $maskared = '';
        $k = 0;
        for($i = 0; $i<=strlen($mask)-1; $i++)
        {
            if($mask[$i] == '#')
            {
                if(isset($val[$k]))
                    $maskared .= $val[$k++];
            }
            else
            {
                if(isset($mask[$i]))
                    $maskared .= $mask[$i];
            }
        }
        return $maskared;
    }
?>

    <div class="fin_comp">
        <h2 class="titu">Finalizar Compra</h2>
        
        <div class="endereco">
            <p class="ende"><img src="icone/entrega.png">Entrega<p>
            
            <?php if ($modo == 'selecionar' && $tem_enderecos): ?>
                <!-- Modo de seleção de endereço existente -->
                <div class="endereco-lista">
                    <h3>Selecione um endereço de entrega</h3>
                    
                    <?php while ($endereco = mysqli_fetch_assoc($enderecos)): ?>
                        <div class="endereco-item <?php echo $endereco['principal'] ? 'selecionado' : ''; ?>" 
                             onclick="selecionarEndereco(this, <?php echo $endereco['cod_endereco']; ?>)">
                            <div class="endereco-item-titulo">
                                <?php echo htmlspecialchars($endereco['rua']); ?>, <?php echo htmlspecialchars($endereco['numero']); ?>
                                <?php if ($endereco['principal']): ?>
                                    <span class="endereco-principal">Principal</span>
                                <?php endif; ?>
                            </div>
                            <div class="endereco-item-detalhes">
                                <?php echo htmlspecialchars($endereco['bairro']); ?>, <?php echo htmlspecialchars($endereco['cidade']); ?>
                                <br>CEP: <?php echo htmlspecialchars($endereco['cep']); ?>
                                <?php if (!empty($endereco['complemento'])): ?>
                                    <br>Complemento: <?php echo htmlspecialchars($endereco['complemento']); ?>
                                <?php endif; ?>
                            </div>
                            <div class="endereco-item-acoes" style="margin-top: 10px;">
                                <a href="endereco.php?modo=editar&endereco_id=<?php echo $endereco['cod_endereco']; ?>" class="link" style="display: inline-block; margin-right: 15px;">Editar</a>
                                <?php if (!$endereco['principal']): ?>
                                    <a href="definir_endereco_principal.php?id=<?php echo $endereco['cod_endereco']; ?>" class="link" style="display: inline-block;">Definir como principal</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <div class="confirma">
                    <form action="end_update.php" method="POST">
                        <input type="hidden" name="endereco_id" id="endereco_selecionado" value="">
                        <button type="submit" name="btn_usar_endereco" class="salva_v">Usar este endereço</button>
                    </form>
                    <a href="endereco.php?modo=novo" class="btn-novo-endereco btn-secundario">Cadastrar novo endereço</a>
                </div>
                
            <?php else: ?>
                <form action="end_update.php" method="POST" class="endereco-form ativo">
                    <div class="entrega">
                        <label class="end_label">CEP</label>
                        <input type="text" value="<?php echo htmlspecialchars($cep); ?>" name="CEP" id="CEP" class="insere_dados" required>
                    </div>
                    <div class="entrega">
                        <label class="end_label">Cidade</label>
                        <input type="text" value="<?php echo htmlspecialchars($cidade); ?>" id="cidade" name="cidade" class="insere_dados" required>
                    </div>
                    <div class="entrega">
                        <label class="end_label">Bairro</label>
                        <input type="text" value="<?php echo htmlspecialchars($bairro); ?>" id="bairro" name="bairro" class="insere_dados" required>
                    </div>
                    <div class="entrega">
                        <label class="end_label">Rua</label>
                        <input type="text" value="<?php echo htmlspecialchars($rua); ?>" id="rua" name="rua" class="insere_dados" required>
                    </div>
                    <div class="entrega">
                        <label class="end_label">Número</label>
                        <input type="number" value="<?php echo htmlspecialchars($numero); ?>" name="numero" class="insere_dados" required>
                    </div>
                    <div class="entrega">
                        <label class="end_label">Complemento (opcional)</label>
                        <input type="text" value="<?php echo htmlspecialchars($complemento); ?>" name="complemento" class="insere_dados">
                    </div>
                    <div class="entrega">
                        <label class="end_label">
                            <input type="checkbox" name="endereco_principal" value="1" <?php echo ($modo == 'novo' || (isset($endereco) && $endereco['principal'])) ? 'checked' : ''; ?>>
                            Definir como endereço principal
                        </label>
                    </div>
                    
                    <div class="confirma">
                        <?php if ($modo == 'editar'): ?>
                            <input type="hidden" name="endereco_id" value="<?php echo $endereco_id; ?>">
                            <button type="submit" name="btn_atualizar_endereco" class="salva_v">Atualizar endereço</button>
                        <?php else: ?>
                            <button type="submit" name="btn_novo_endereco" class="salva_v">Salvar endereço</button>
                        <?php endif; ?>
                        
                        <?php if ($tem_enderecos): ?>
                            <a href="endereco.php?modo=selecionar" class="btn-novo-endereco btn-secundario">Voltar para meus endereços</a>
                        <?php endif; ?>
                    </div>
                </form>
            <?php endif; ?>
        </div>
        
        <div class="etapas">
            <a href="identificacao.php" class="link"> 
                <div class="identificacao etapa-concluida">
                    <p class="etap"><img src="icone/perfil.png">Identificação<p>
                    <p class="dad_ver">Dados confirmados</p>
                    <img class="verificado" src="icone/verificacao.png">
                </div>
            </a>
            <div class="entrega etapa-ativa">
                <p class="etap"><img src="icone/entrega.png">Entrega<p>
                <p>Selecione ou cadastre um endereço</p>
            </div>
            <div class="pagamento etapa-pendente">
                <p class="etap"><img src="icone/pagamento.png">Pagamento<p>
                <p>Aguardando preenchimento dos dados</p>
            </div>
        </div>
        
        <div class="pedido">
            <p class="resum">Resumo do pedido<p>
            <?php
                $total_carrinho = 0;
                
                if(isset($_SESSION['carrinho']) && !empty($_SESSION['carrinho'])) {
                    foreach ($_SESSION['carrinho'] as $id => $qtd)
                    {
                        $sql = "SELECT cod_item, nome, preco, imagem
                        FROM itens
                        WHERE cod_item = '$id'";
                        $resultado = mysqli_query($conn, $sql) or die (mysqli_error($conn));
                        
                        if(mysqli_num_rows($resultado) > 0) {
                            $linha = mysqli_fetch_array($resultado);
                            
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
                                            <p class='nome'>$nome</p>
                                        </div>
                                        <div class='linha_2'> 
                                            <p class='valor'>R$ $valor</p>
                                            <p class='subtotal'>Total: R$ $subtotal</p>
                                        </div>
                                    </div>
                                </div>";
                        }
                    }
                } else {
                    echo "<p>Seu carrinho está vazio.</p>";
                }
            ?>
            <hr class="linha">
            <div class="preco">
                <div class="sub">
                    <p class="subt">Subtotal</p>
                    <p class="subtot">R$ <?php echo number_format($total_carrinho, 2, ',', '.'); ?></p>
                </div>
                <div class="ent">
                    <p class="entr">Entrega</p>
                    <p class="gratis">Grátis</p>
                </div>
                <div class="tot">
                    <p class="tota">Total</p>
                    <p class="total">R$ <?php echo number_format($total_carrinho, 2, ",", "."); ?></p>
                </div>
                <div class="vol">
                    <a class="voltar" href="../carrinho/carrinho.php">Voltar a sacola</a>
                </div>
            </div>
        </div> 
    </div>  

<?php 
    include_once "../rodape.html";
?>
</body>
</html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.0/jquery.mask.js"></script>

<script type="text/javascript">
  //validacao
$(document).ready(function(){
    var $CEP = $("#CEP");
    $("#CEP").mask("99999-999");
    
    function limpa_formulário_cep() {
        $("#rua").val("");
        $("#bairro").val("");
        $("#cidade").val("");
    }
    
    $("#CEP").blur(function() {
        var cep = $(this).val().replace(/\D/g, '');
        
        if (cep != "") {
            var validacep = /^[0-9]{8}$/;
            
            if(validacep.test(cep)) {
                $("#rua").val("...");
                $("#bairro").val("...");
                $("#cidade").val("...");
                
                $.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {
                    if (!("erro" in dados)) {
                        $("#rua").val(dados.logradouro);
                        $("#bairro").val(dados.bairro);
                        $("#cidade").val(dados.localidade);
                    } else {
                        limpa_formulário_cep();
                        alert("CEP não encontrado.");
                    }
                });
            } else {
                limpa_formulário_cep();
                alert("Formato de CEP inválido.");
            }
        } else {
            limpa_formulário_cep();
        }
    });
});

function selecionarEndereco(elemento, id) {
    document.querySelectorAll('.endereco-item').forEach(function(item) {
        item.classList.remove('selecionado');
    });
    elemento.classList.add('selecionado');
    document.getElementById('endereco_selecionado').value = id;
}
</script>
