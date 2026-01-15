<?php
include "conexao.php";

if (isset($_POST['cod_pedido']) && isset($_POST['cod_status'])) {
    $cod_pedido = $_POST['cod_pedido'];
    $cod_status = $_POST['cod_status'];
    $data_hora = date('Y-m-d H:i:s');

    $sql = "INSERT INTO hist_status_ped (cod_pedido, cod_status, data_hora) 
            VALUES ('$cod_pedido', '$cod_status', '$data_hora')";

    if (mysqli_query($conn, $sql)) {
        header("Location: painel.php?status=ok");
        exit();
    } else {
        echo "Erro ao atualizar status: " . mysqli_error($conn);
    }
} else {
    echo "Dados incompletos.";
}
?>
