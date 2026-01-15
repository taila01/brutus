<?php
session_start();

include_once "conexao.php";

$mensagem_sucesso = '';
$erros = [];
$nome = '';
$email = '';
$telefone = '';
$cpf = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (empty($_SESSION['id_logado'])) {
        header('Location: login.php');
        exit;
    }

    $usuario_id = (int) $_SESSION['id_logado'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $novo_nome = $_POST['nome'] ?? '';
        $novo_email = $_POST['email'] ?? '';
        $novo_telefone = $_POST['telefone'] ?? '';
        $novo_cpf = $_POST['cpf'] ?? '';
        $senha_atual = $_POST['senha_atual'] ?? '';
        $nova_senha = $_POST['nova_senha'] ?? '';
        $confirmar_nova_senha = $_POST['confirmar_nova_senha'] ?? '';

        $alterar_senha = !empty($senha_atual);

        if ($alterar_senha) {
            $stmt = $conn->prepare('SELECT senha FROM usuario WHERE codigo = :id');
            $stmt->bindParam(':id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                $erros[] = 'Usuário não encontrado';
            } else {
                $senha_bd = $stmt->fetch(PDO::FETCH_ASSOC)['senha'];

                if (md5($senha_atual) !== $senha_bd) {
                    $erros[] = 'Senha atual incorreta';
                }

                if (empty($nova_senha)) {
                    $erros[] = 'Nova senha é obrigatória';
                } elseif ($nova_senha !== $confirmar_nova_senha) {
                    $erros[] = 'As senhas não coincidem';
                }
            }
        }

        if (empty($erros)) {
            if ($alterar_senha) {
                $sql = 'UPDATE usuario 
                        SET nome = :nome, email = :email, telefone = :telefone, cpf = :cpf, senha = :senha 
                        WHERE codigo = :id';
                $stmt = $conn->prepare($sql);
                $stmt->bindValue(':senha', md5($nova_senha));
            } else {
                $sql = 'UPDATE usuario 
                        SET nome = :nome, email = :email, telefone = :telefone, cpf = :cpf 
                        WHERE codigo = :id';
                $stmt = $conn->prepare($sql);
            }

            $stmt->bindValue(':nome', $novo_nome);
            $stmt->bindValue(':email', $novo_email);
            $stmt->bindValue(':telefone', $novo_telefone);
            $stmt->bindValue(':cpf', $novo_cpf);
            $stmt->bindValue(':id', $usuario_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $mensagem_sucesso = 'Dados atualizados com sucesso!';
                $nome = $novo_nome;
                $email = $novo_email;
                $telefone = $novo_telefone;
                $cpf = $novo_cpf;
            } else {
                $erros[] = 'Erro ao atualizar os dados';
            }
        }
    }

    if (empty($nome) || empty($email)) {
        $stmt = $conn->prepare('SELECT nome, email, telefone, cpf FROM usuario WHERE codigo = :id');
        $stmt->bindParam(':id', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            $nome = $usuario['nome'] ?? '';
            $email = $usuario['email'] ?? '';
            $telefone = $usuario['telefone'] ?? '';
            $cpf = $usuario['cpf'] ?? '';
        } else {
            $erros[] = 'Usuário não encontrado no banco de dados';
        }
    }

    $stmt_enderecos = $conn->prepare('SELECT * FROM endereco WHERE fk_Usuario_codigo = :id');
    $stmt_enderecos->bindParam(':id', $usuario_id, PDO::PARAM_INT);
    $stmt_enderecos->execute();

    $stmt_pedidos = $conn->prepare(
        'SELECT p.cod_pedido, p.datahora_pedido, p.total_pedidos, p.tipo_pagamento, p.quant_itens,
            (
                SELECT s.status_pedidos
                FROM hist_status_ped h
                JOIN status_pedidos s ON s.cod_status_pedidos = h.cod_status
                WHERE h.cod_pedido = p.cod_pedido
                ORDER BY h.data_hora DESC
                LIMIT 1
            ) AS status_atual
         FROM pedidos p
         WHERE p.fk_Usuario_codigo = :id
         ORDER BY p.datahora_pedido DESC'
    );
    $stmt_pedidos->bindParam(':id', $usuario_id, PDO::PARAM_INT);
    $stmt_pedidos->execute();
    $pedidos = $stmt_pedidos->fetchAll(PDO::FETCH_ASSOC);

    foreach ($pedidos as &$pedido) {
        $stmt_hist = $conn->prepare(
            'SELECT h.data_hora, s.status_pedidos
             FROM hist_status_ped h
             JOIN status_pedidos s ON s.cod_status_pedidos = h.cod_status
             WHERE h.cod_pedido = :id
             ORDER BY h.data_hora'
        );
        $stmt_hist->bindParam(':id', $pedido['cod_pedido'], PDO::PARAM_INT);
        $stmt_hist->execute();
        $pedido['historico'] = $stmt_hist->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    $erros[] = 'Erro: ' . $e->getMessage();
}
?>
