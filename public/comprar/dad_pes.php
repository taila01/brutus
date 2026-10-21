<?php
session_start();
require_once __DIR__ . '/../../app/database.php';
require_once __DIR__ . '/../../app/helpers/validacao.php';
if (empty($_SESSION['id_logado'])) {header('Location: /brutus/login/login.php');exit;}
if ($_SERVER['REQUEST_METHOD']!=='POST') {header('Location: identificacao.php');exit;}
$dados=[];foreach(['nome','email','cpf'] as $chave)$dados[$chave]=campo($_POST,$chave);
$db=brutusPDO();$erros=validarPessoa($dados,false);
if (emailEmUso($db,$dados['email'],(int)$_SESSION['id_logado'])) $erros[]='Este e-mail já está em uso por outra conta.';
if ($erros) {
    $_SESSION['checkout_erros']=$erros;$_SESSION['pessoa_old']=$dados;
    header('Location: identificacao.php');exit;
}
$q=$db->prepare('UPDATE usuario SET nome=?,cpf=?,email=? WHERE codigo=?');
$q->execute([$dados['nome'],digitos($dados['cpf']),$dados['email'],(int)$_SESSION['id_logado']]);
header('Location: endereco.php');exit;
