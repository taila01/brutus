<?php

session_start();

include_once "conexao.php";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Erro na conexão: ' . $e->getMessage();
    exit;
}

if (!isset($_SESSION['id_logado'])) {
    header('Location: ../login/login.php');
    exit;
}
$usuario_id = $_SESSION['id_logado'];

try {
    $conn->beginTransaction();

    $sql_enderecos = "DELETE FROM endereco WHERE fk_Usuario_codigo = :usuario_id";
    $stmt_endereco = $conn->prepare($sql_enderecos);
    $stmt_endereco->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt_endereco->execute();

    $sql_usuario = "DELETE FROM usuario WHERE codigo = :usuario_id";
    $stmt_usuario = $conn->prepare($sql_usuario);
    $stmt_usuario->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt_usuario->execute();

    $conn->commit();

    session_destroy();
    header('Location: ../login/login.php');
    exit;
} catch (Exception $e) {
    $conn->rollBack();
    echo "Erro ao excluir o usuário: " . $e->getMessage();
}
?>
