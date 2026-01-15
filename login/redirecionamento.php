<?php
session_start();

if (!isset($_SESSION['id_logado'])) {
    // Se o usuário não está logado, envie para a página de login
    header('Location: ../../brutus/login/login.php');
    exit;
}

// Verifica o tipo de usuário e redireciona
if ($_SESSION['tipos_usuario'] == 1) {
    // Administrador
    header('Location: ../admproduto/painel.php');
} else {
    // Cliente
    header('Location: ../usuario/perfil.php');
}
exit;
?>
