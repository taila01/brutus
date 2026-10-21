<?php
session_start();
require_once __DIR__ . '/../../app/database.php';
require_once __DIR__ . '/../../app/helpers/validacao.php';
if (empty($_SESSION['id_logado'])) {header('Location: /brutus/login/login.php');exit;}
if ($_SERVER['REQUEST_METHOD']!=='POST') {header('Location: endereco.php');exit;}
$db=brutusPDO();$usuario=(int)$_SESSION['id_logado'];
$id=filter_var(campo($_POST,'endereco_id'),FILTER_VALIDATE_INT) ?: 0;
$editar=isset($_POST['btn_atualizar_endereco']);$selecionar=isset($_POST['btn_usar_endereco']);
if ($editar || $selecionar) {
    $q=$db->prepare('SELECT cod_endereco FROM endereco WHERE cod_endereco=? AND fk_Usuario_codigo=?');$q->execute([$id,$usuario]);
    if (!$q->fetchColumn()) {$_SESSION['erro']='Selecione um endereço da sua conta.';header('Location: endereco.php?modo=selecionar');exit;}
}
if ($selecionar) {$_SESSION['endereco_entrega']=$id;header('Location: pagamento.php');exit;}
if (!$editar && !isset($_POST['btn_novo_endereco'])) {header('Location: endereco.php');exit;}
$dados=[];foreach(['CEP','cidade','bairro','rua','numero','complemento'] as $chave)$dados[$chave]=campo($_POST,$chave);
$erros=validarEndereco($dados);
if ($erros) {
    $_SESSION['checkout_erros']=$erros;$_SESSION['endereco_old']=$dados;
    header('Location: endereco.php?modo='.($editar?'editar&endereco_id='.$id:'novo'));exit;
}
try {
    $db->beginTransaction();$principal=isset($_POST['endereco_principal'])?1:0;
    if ($principal) {$q=$db->prepare('UPDATE endereco SET principal=0 WHERE fk_Usuario_codigo=?');$q->execute([$usuario]);}
    $valores=[digitos($dados['CEP']),$dados['cidade'],$dados['bairro'],$dados['rua'],(int)$dados['numero'],$dados['complemento'],$principal,$usuario];
    if ($editar) {
        $q=$db->prepare('UPDATE endereco SET cep=?,cidade=?,bairro=?,rua=?,numero=?,complemento=?,principal=? WHERE fk_Usuario_codigo=? AND cod_endereco=?');$valores[]=$id;
    } else $q=$db->prepare('INSERT INTO endereco (cep,cidade,bairro,rua,numero,complemento,principal,fk_Usuario_codigo,identificacao) VALUES (?,?,?,?,?,?,?,?,\'Entrega\')');
    $q->execute($valores);if (!$editar)$id=(int)$db->lastInsertId();
    $db->commit();$_SESSION['endereco_entrega']=$id;
    header('Location: pagamento.php');exit;
} catch (Throwable $e) {
    if ($db->inTransaction())$db->rollBack();error_log($e->getMessage());
    $_SESSION['erro']='Não foi possível salvar o endereço. Tente novamente.';header('Location: endereco.php?modo=selecionar');exit;
}
