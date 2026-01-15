<?php
session_start();

include_once "conecta.php";

if (!isset($_SESSION['id_logado']) || $_SESSION['id_logado'] != true) {
    header('location: ../login/login.php');
    exit;
}

$cliente = $_SESSION['id_logado'];

if (isset($_POST['btn_usar_endereco']) && isset($_POST['endereco_id'])) {
    $endereco_id = intval($_POST['endereco_id']);
    //endereco ∈ usuario
    if ($endereco_id > 0) {
        $query_check = "SELECT cod_endereco FROM endereco WHERE cod_endereco = $endereco_id AND fk_Usuario_codigo = $cliente";
        $result_check = mysqli_query($conn, $query_check);

        if (mysqli_num_rows($result_check) > 0) {
            $_SESSION['endereco_entrega'] = $endereco_id;
            header('location: pagamento.php');
            exit;
        } else {
            $_SESSION['erro'] = "Endereço não encontrado ou não pertence ao usuário.";
            header('location: endereco.php?modo=selecionar');
            exit;
        }
    } else {
        $_SESSION['erro'] = "Selecione um endereço para continuar.";
        header('location: endereco.php?modo=selecionar');
        exit;
    }
}

if (isset($_POST['btn_atualizar_endereco']) && isset($_POST['endereco_id'])) {
    $endereco_id = intval($_POST['endereco_id']);

    $query_check = "SELECT cod_endereco FROM endereco WHERE cod_endereco = $endereco_id AND fk_Usuario_codigo = $cliente";
    $result_check = mysqli_query($conn, $query_check);

    if (mysqli_num_rows($result_check) > 0) {
        $cep = mysqli_real_escape_string($conn, $_POST['CEP']);
        $cidade = mysqli_real_escape_string($conn, $_POST['cidade']);
        $bairro = mysqli_real_escape_string($conn, $_POST['bairro']);
        $rua = mysqli_real_escape_string($conn, $_POST['rua']);
        $numero = intval($_POST['numero']);
        $complemento = isset($_POST['complemento']) ? mysqli_real_escape_string($conn, $_POST['complemento']) : '';
        $principal = isset($_POST['endereco_principal']) ? 1 : 0;

        if ($principal) {
            $query_reset = "UPDATE endereco SET principal = 0 WHERE fk_Usuario_codigo = $cliente";
            mysqli_query($conn, $query_reset);
        }

        $query_update = "
            UPDATE endereco SET 
                cep = '$cep', 
                cidade = '$cidade', 
                bairro = '$bairro', 
                rua = '$rua', 
                numero = $numero, 
                complemento = '$complemento', 
                principal = $principal 
            WHERE cod_endereco = $endereco_id
        ";

        if (mysqli_query($conn, $query_update)) {
            $_SESSION['sucesso'] = "Endereço atualizado com sucesso!";
            header('location: pagamento.php');
            exit;
        } else {
            $_SESSION['erro'] = "Erro ao atualizar endereço: " . mysqli_error($conn);
            header('location: endereco.php?modo=editar&endereco_id=' . $endereco_id);
            exit;
        }
    } else {
        $_SESSION['erro'] = "Endereço não encontrado ou não pertence ao usuário.";
        header('location: endereco.php?modo=selecionar');
        exit;
    }
}

if (isset($_POST['btn_novo_endereco'])) {
    $cep = mysqli_real_escape_string($conn, $_POST['CEP']);
    $cidade = mysqli_real_escape_string($conn, $_POST['cidade']);
    $bairro = mysqli_real_escape_string($conn, $_POST['bairro']);
    $rua = mysqli_real_escape_string($conn, $_POST['rua']);
    $numero = intval($_POST['numero']);
    $complemento = isset($_POST['complemento']) ? mysqli_real_escape_string($conn, $_POST['complemento']) : '';
    $principal = isset($_POST['endereco_principal']) ? 1 : 0;

    if ($principal) {
        $query_reset = "UPDATE endereco SET principal = 0 WHERE fk_Usuario_codigo = $cliente";
        mysqli_query($conn, $query_reset);
    }

    $query_insert = "
        INSERT INTO endereco (fk_Usuario_codigo, cep, cidade, bairro, rua, numero, complemento, principal) 
        VALUES ($cliente, '$cep', '$cidade', '$bairro', '$rua', $numero, '$complemento', $principal)
    ";

    if (mysqli_query($conn, $query_insert)) {
        $novo_endereco_id = mysqli_insert_id($conn);
        $_SESSION['sucesso'] = "Endereço cadastrado com sucesso!";
        header('location: endereco.php?modo=selecionar');
        exit;
    } else {
        $_SESSION['erro'] = "Erro ao cadastrar endereço: " . mysqli_error($conn);
        header('location: endereco.php?modo=novo');
        exit;
    }
}

header('location: pagamento.php');
exit;
?>
