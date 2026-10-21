<?php
include_once __DIR__ . '/../../app/conexoes/mysqli.php';
session_start();
require_once __DIR__ . '/../../app/helpers/preco-produto.php';

if (empty($_SESSION['id_logado'])) {header('Location: /brutus/login/login.php');exit;}
$cliente = (int)$_SESSION['id_logado'];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $forma=is_string($_POST['pagamento']??null)?$_POST['pagamento']:'';
    if (!in_array($forma,['dinheiro','cartao','pix'],true)) {
        $_SESSION['checkout_erros']=['Selecione uma forma de pagamento válida.'];header('Location: pagamento.php');exit;
    }
    if (empty($_SESSION['carrinho'])) {header('Location: /brutus/carrinho/carrinho.php');exit;}
    foreach ($_SESSION['carrinho'] as $item=>$quantidade) {
        if (!filter_var($item,FILTER_VALIDATE_INT) || !is_int($quantidade) || $quantidade<1 || $quantidade>99) {
            $_SESSION['checkout_erros']=['Revise as quantidades do pedido: o limite é de 99 por produto.'];header('Location: pagamento.php');exit;
        }
    }
}

if (isset($_POST["btn_pag"])) {
    // Verificar se o endereço foi selecionado
    if (!isset($_POST['endereco_id']) || empty($_POST['endereco_id'])) {
        $_SESSION['erro'] = "Endereço de entrega não selecionado. Por favor, escolha um endereço.";
        header('location: endereco.php?modo=selecionar');
        exit;
    }

    // Capturar o ID do endereço selecionado
    $endereco_id = intval($_POST['endereco_id']);

    // Verificar se o endereço pertence ao usuário
    $query_check = "SELECT cod_endereco FROM endereco WHERE cod_endereco = $endereco_id AND fk_Usuario_codigo = $cliente";
    $result_check = mysqli_query($conn, $query_check);

    if (mysqli_num_rows($result_check) == 0) {
        $_SESSION['erro'] = "Endereço inválido. Por favor, escolha um endereço válido.";
        header('location: endereco.php?modo=selecionar');
        exit;
    }

    $tipo_pag = mysqli_real_escape_string($conn, $_POST['pagamento']);
    $quant = 0;
    $total_carrinho = 0;
    $cod_status = 1; // Status inicial (ex: PREPARANDO)

    // Calcular total do carrinho
    foreach ($_SESSION['carrinho'] as $id => $qtd) {
        $sql = "SELECT cod_item, preco FROM itens WHERE cod_item = '$id'";
        $resultado = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        $linha = mysqli_fetch_assoc($resultado);

        $preco = precoProduto($linha);
        $subtotal = $preco * $qtd;
        $total_carrinho += $subtotal;
        $quant += $qtd;
    }

    // Inserir pedido com o código do endereço
    $sql_pedido = "INSERT INTO pedidos
        (quant_itens, fk_Usuario_codigo, total_pedidos, tipo_pagamento, cod_endereco, datahora_pedido)
        VALUES ('$quant', '$cliente', '$total_carrinho', '$tipo_pag', '$endereco_id', NOW())";

    if (!mysqli_query($conn, $sql_pedido)) {
        $_SESSION['erro'] = "Erro ao finalizar pedido: " . mysqli_error($conn);
        header('location: pagamento.php');
        exit;
    }

    $COD = mysqli_insert_id($conn); // ID do pedido recém-criado

    // Inserir itens no pedido
    foreach ($_SESSION['carrinho'] as $id => $qtd) {
        $consultaItem = $conn->prepare('SELECT cod_item, preco FROM itens WHERE cod_item = ?');
        $consultaItem->bind_param('i', $id);
        $consultaItem->execute();
        $precoUnitario = precoProduto($consultaItem->get_result()->fetch_assoc());
        $sql_item = "INSERT INTO itens_pedido (cod_item, codigo_pedido, quantidade, preco_unitario)
                     VALUES ('$id', '$COD', '$qtd', '$precoUnitario')";
        mysqli_query($conn, $sql_item) or die(mysqli_error($conn));

        // Limpar carrinho
        unset($_SESSION['carrinho'][$id]);
    }

    // Registrar o status inicial do pedido
    $data_hora = date('Y-m-d H:i:s');
    $sql_status = "INSERT INTO hist_status_ped (cod_pedido, cod_status, data_hora)
                  VALUES ('$COD', '$cod_status', '$data_hora')";
    mysqli_query($conn, $sql_status);

    // Limpar a sessão de endereço de entrega
    unset($_SESSION['endereco_entrega']);

    // Definir mensagem de sucesso
    $_SESSION['sucesso'] = "Pedido realizado com sucesso! Seu número de pedido é #$COD";

    // Redirecionar
    header("Location: ../usuario/detalhe_pedido.php?id=$COD");
    exit;
}

// Se chegou aqui sem POST, redireciona para a página de pagamento
header("Location: pagamento.php");
exit;
?>
