<?php
include_once "conecta.php";
session_start();

$cliente = $_SESSION['id_logado'];

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
        $sql = "SELECT preco FROM itens WHERE cod_item = '$id'";
        $resultado = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        $linha = mysqli_fetch_assoc($resultado);

        $preco = str_replace(",", "", $linha['preco']);
        $subtotal = $preco * $qtd;
        $total_carrinho += $subtotal;
        $quant += $qtd;
    }

    // Inserir pedido com o código do endereço
    $sql_pedido = "INSERT INTO pedidos 
        (quant_itens, fk_Usuario_codigo, total_pedidos, tipo_pagamento, cod_endereco)
        VALUES ('$quant', '$cliente', '$total_carrinho', '$tipo_pag', '$endereco_id')";
    
    if (!mysqli_query($conn, $sql_pedido)) {
        $_SESSION['erro'] = "Erro ao finalizar pedido: " . mysqli_error($conn);
        header('location: pagamento.php');
        exit;
    }

    $COD = mysqli_insert_id($conn); // ID do pedido recém-criado

    // Inserir itens no pedido
    foreach ($_SESSION['carrinho'] as $id => $qtd) {
        $sql_item = "INSERT INTO itens_pedido (cod_item, codigo_pedido, quantidade)
                     VALUES ('$id', '$COD', '$qtd')";
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
    header("Location: ../usuario/detalhe_pedido.php?pedido=$COD");
    exit;
}

// Se chegou aqui sem POST, redireciona para a página de pagamento
header("Location: pagamento.php");
exit;
?>
