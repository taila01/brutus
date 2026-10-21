<?php
session_start();
require_once __DIR__ . '/../../app/database.php';
require_once __DIR__ . '/../../app/helpers/validacao.php';
$email=campo($_POST,'email');
$senha=is_string($_POST['password']??null)?$_POST['password']:'';
$usuario=false;
if ($_SERVER['REQUEST_METHOD']==='POST' && filter_var($email,FILTER_VALIDATE_EMAIL) && strlen($email)<=254 && $senha!=='' && strlen($senha)<=256) {
    $db=brutusPDO();
    $q=$db->prepare('SELECT codigo, senha, fk_tipos_usuario_codigo FROM usuario WHERE LOWER(email)=LOWER(?) LIMIT 1');
    $q->execute([$email]);
    $usuario=$q->fetch();
}
if (!$usuario || !senhaCorreta($senha,$usuario['senha'])) {
    $_SESSION['erro_login']='E-mail ou senha incorretos.';
    header('Location: login.php'); exit;
}
session_regenerate_id(true);
$_SESSION['id_logado']=(int)$usuario['codigo'];
$_SESSION['tipos_usuario']=(int)$usuario['fk_tipos_usuario_codigo'];
header('Location: '.($_SESSION['tipos_usuario']===1?'../admproduto/painel.php':'../usuario/perfil.php'));
exit;
