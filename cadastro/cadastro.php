<?php
session_start();
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../validacao.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: cadastrar.php'); exit; }
$dados = [];
foreach (['nome','email','cpf','telefone','cep','rua','bairro','cidade','numero','complemento'] as $chave) $dados[$chave] = campo($_POST,$chave);
$senha = is_string($_POST['senha'] ?? null) ? $_POST['senha'] : '';
$confirmacao = is_string($_POST['confirmarSenha'] ?? null) ? $_POST['confirmarSenha'] : '';
$erros = array_merge(validarPessoa($dados),validarEndereco($dados),validarSenha($senha,$confirmacao));
if (campo($_POST,'termos') !== '1') $erros[] = 'Confirme a aceitação dos termos para continuar.';
$db = brutusPDO();
if (!$erros && emailEmUso($db,$dados['email'])) $erros[] = 'Já existe uma conta com este e-mail. Entre na sua conta.';
if ($erros) {
    $_SESSION['cadastro_erros'] = $erros;
    $_SESSION['cadastro_old'] = $dados;
    header('Location: cadastrar.php'); exit;
}
try {
    $db->beginTransaction();
    $q=$db->prepare('INSERT INTO usuario (nome,email,cpf,telefone,senha,fk_tipos_usuario_codigo) VALUES (?,?,?,?,?,2)');
    $q->execute([$dados['nome'],strtolower($dados['email']),digitos($dados['cpf']),digitos($dados['telefone']),password_hash($senha,PASSWORD_DEFAULT)]);
    $usuario=(int)$db->lastInsertId();
    $q=$db->prepare('INSERT INTO endereco (cep,rua,bairro,numero,complemento,cidade,fk_Usuario_codigo,identificacao,principal) VALUES (?,?,?,?,?,?,?,\'Entrega\',1)');
    $q->execute([digitos($dados['cep']),$dados['rua'],$dados['bairro'],(int)$dados['numero'],$dados['complemento'],$dados['cidade'],$usuario]);
    $db->commit();
    session_regenerate_id(true);
    $_SESSION['id_logado']=$usuario;
    $_SESSION['tipos_usuario']=2;
    unset($_SESSION['cadastro_old'],$_SESSION['cadastro_erros']);
    header('Location: /brutus/usuario/perfil.php'); exit;
} catch (Throwable $e) {
    if ($db->inTransaction()) $db->rollBack();
    error_log('Falha no cadastro Brutus: '.$e->getMessage());
    $_SESSION['cadastro_erros']=['Não foi possível criar sua conta. Confira os dados e tente novamente.'];
    $_SESSION['cadastro_old']=$dados;
    header('Location: cadastrar.php'); exit;
}
