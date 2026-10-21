<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
function responderCarrinho(array $extra = [], int $status = 200): never
{
    http_response_code($status);
    $itens = $_SESSION['carrinho'] ?? [];
    echo json_encode(array_merge(['ok'=>true,'items'=>(object)$itens,'total_count'=>array_sum($itens)],$extra),JSON_UNESCAPED_UNICODE);
    exit;
}
$_SESSION['carrinho'] = is_array($_SESSION['carrinho'] ?? null) ? $_SESSION['carrinho'] : [];
if ($_SERVER['REQUEST_METHOD']==='GET') responderCarrinho();
if ($_SERVER['REQUEST_METHOD']!=='POST') { header('Allow: GET, POST'); responderCarrinho(['ok'=>false,'message'=>'Método não permitido.'],405); }
$origin=$_SERVER['HTTP_ORIGIN']??'';
if ($origin!=='' && parse_url($origin,PHP_URL_HOST)!==explode(':',$_SERVER['HTTP_HOST']??'')[0]) responderCarrinho(['ok'=>false,'message'=>'Origem inválida.'],403);
try { $dados=json_decode(file_get_contents('php://input'),true,32,JSON_THROW_ON_ERROR); }
catch (JsonException $e) { responderCarrinho(['ok'=>false,'message'=>'Pedido inválido.'],400); }
if (!is_array($dados)) responderCarrinho(['ok'=>false,'message'=>'Pedido inválido.'],400);
$id=filter_var($dados['id']??null,FILTER_VALIDATE_INT);
$acao=$dados['action']??null;
if (!$id || $id<1 || !in_array($acao,['add','remove'],true)) responderCarrinho(['ok'=>false,'message'=>'Produto ou ação inválidos.'],422);
try {
    require_once __DIR__ . '/../../app/database.php';
    $q=brutusPDO()->prepare('SELECT cod_item FROM itens WHERE cod_item=?');$q->execute([$id]);
    if (!$q->fetchColumn()) responderCarrinho(['ok'=>false,'message'=>'Este produto não está mais disponível.'],404);
    $quantidade=(int)($_SESSION['carrinho'][$id]??0);
    if ($acao==='add' && $quantidade>=99) responderCarrinho(['ok'=>false,'message'=>'O limite é de 99 unidades por produto.'],422);
    $quantidade=$acao==='add'?$quantidade+1:max(0,$quantidade-1);
    if ($quantidade===0) unset($_SESSION['carrinho'][$id]); else $_SESSION['carrinho'][$id]=$quantidade;
    responderCarrinho(['product_id'=>$id,'quantity'=>$quantidade]);
} catch (Throwable $e) {
    error_log('Falha ao atualizar sacola Brutus: '.$e->getMessage());
    responderCarrinho(['ok'=>false,'message'=>'Não foi possível atualizar o pedido. Tente novamente.'],500);
}
