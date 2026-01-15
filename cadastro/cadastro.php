<?php
session_start();

include_once "conexao.php";

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST["btnCadastrar"])) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $cpf = $_POST['cpf'];
    $telefone = $_POST['telefone'];
    $senha = md5($_POST['senha']); 
    
    $bairro = $_POST['bairro'];
    $rua = $_POST['rua'];
    $numero = $_POST['numero'];
    $cep = $_POST['cep'];
    $complemento = $_POST['complemento'];
  
    $stmt = $conn->prepare("INSERT INTO usuario (nome, email, cpf, telefone, senha, fk_tipos_usuario_codigo) 
                           VALUES (?, ?, ?, ?, ?, '2')");
    $stmt->bind_param("sssss", $nome, $email, $cpf, $telefone, $senha);
    
    if ($stmt->execute()) {
        $id_usuario = $stmt->insert_id;
        $stmt->close();
        
        $stmt2 = $conn->prepare("INSERT INTO endereco (cep, rua, bairro, numero, complemento, cidade, fk_Usuario_codigo) 
                                VALUES (?, ?, ?, ?, ?, 'Ourinhos', ?)");
        $stmt2->bind_param("sssssi", $cep, $rua, $bairro, $numero, $complemento, $id_usuario);
        
        if ($stmt2->execute()) {
            $_SESSION['id_logado'] = $id_usuario;
            
            header('Location: /brutus/index.php');
            exit(); 
        } else {
            echo "Erro ao cadastrar endereço: " . $stmt2->error;
        }
        $stmt2->close();
    } else {
        echo "Erro ao cadastrar usuário: " . $stmt->error;
    }
}
?>
