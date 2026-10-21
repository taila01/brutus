<?php
session_start();
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/../validacao.php';
if (empty($_SESSION['id_logado'])) { header('Location: /brutus/login/login.php'); exit; }
$usuario_id=(int)$_SESSION['id_logado'];
$endereco_id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT) ?: 0;
$modo_edicao=$endereco_id>0;
$mensagem_sucesso='';$erros=[];
$identificacao=$cep=$rua=$numero=$complemento=$bairro=$cidade=$estado=$referencia=$telefone_contato='';$principal=0;
if ($modo_edicao) {
    $q=$conn->prepare('SELECT * FROM endereco WHERE cod_endereco=? AND fk_Usuario_codigo=?');
    $q->execute([$endereco_id,$usuario_id]);$endereco=$q->fetch();
    if (!$endereco) { header('Location: perfil.php#meus-enderecos'); exit; }
    foreach (['identificacao','cep','rua','numero','complemento','bairro','cidade'] as $chave) $$chave=$endereco[$chave] ?? '';
}
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $dados=[];
    foreach (['identificacao','cep','rua','numero','complemento','bairro','cidade'] as $chave) { $dados[$chave]=campo($_POST,$chave); $$chave=$dados[$chave]; }
    $erros=validarEndereco($dados);
    if (!$erros) {
        try {
            $valores=[$identificacao,digitos($cep),$rua,(int)$numero,$complemento,$bairro,$cidade,$usuario_id];
            if ($modo_edicao) {
                $q=$conn->prepare('UPDATE endereco SET identificacao=?,cep=?,rua=?,numero=?,complemento=?,bairro=?,cidade=? WHERE fk_Usuario_codigo=? AND cod_endereco=?');
                $valores[]=$endereco_id;
            } else $q=$conn->prepare('INSERT INTO endereco (identificacao,cep,rua,numero,complemento,bairro,cidade,fk_Usuario_codigo) VALUES (?,?,?,?,?,?,?,?)');
            $q->execute($valores);
            header('Location: perfil.php#meus-enderecos');exit;
        } catch (PDOException $e) {error_log($e->getMessage());$erros[]='Não foi possível salvar o endereço. Tente novamente.';}
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<script src="/brutus/assets/tema.js?v=1"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $modo_edicao ? 'Editar' : 'Cadastrar'; ?> Endereço | BRUTUS</title>
    <link rel="icon" href="img/favicon.svg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../geral.css">
    <link rel="stylesheet" href="perfil.css">
<link rel="stylesheet" href="/brutus/interface.css?v=3">
</head>
<body class="brutus-urban" data-flavor="cebola">

<?php include_once "../cabecalho.html"; ?>

<div class="container my-3">
    <div class="row">
        <div class="col-md-3">
            <div class="list-group profile-menu">
                <a href="perfil.php#editar-dados" class="list-group-item list-group-item-action"><i class="fas fa-user-edit me-2"></i>Editar Dados</a>
                <a href="perfil.php#meus-enderecos" class="list-group-item list-group-item-action active"><i class="fas fa-map-marker-alt me-2"></i>Meus Endereços</a>
                <a href="perfil.php#historico-pedidos" class="list-group-item list-group-item-action"><i class="fas fa-history me-2"></i>Histórico de Pedidos</a>
                <a href="/brutus/login/sair.php" class="list-group-item list-group-item-action"><i class="fas fa-sign-out-alt me-2"></i>Sair</a>
                <a href="#" class="list-group-item list-group-item-action text-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"><i class="fas fa-trash-alt me-2"></i>Excluir Conta</a>
            </div>
        </div>

        <div class="col-md-9">
            <div class="address-form-container">
                <h2 class="mb-4 profile-title">
                    <i class="fas <?php echo $modo_edicao ? 'fa-edit' : 'fa-plus-circle'; ?> me-2"></i>
                    <?php echo $modo_edicao ? 'Editar' : 'Cadastrar Novo'; ?> Endereço
                </h2>

                <?php if (!empty($mensagem_sucesso)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?php echo $mensagem_sucesso; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($erros)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            <?php foreach ($erros as $erro): ?>
                                <li><?php echo $erro; ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="identificacao" class="form-label">Identificação do Endereço*</label>
                            <input type="text" class="form-control" id="identificacao" name="identificacao" maxlength="50" value="<?php echo htmlspecialchars($identificacao); ?>" placeholder="Ex: Casa, Trabalho, Casa dos Pais" required>
                        </div>
                        <div class="col-md-6">
                            <label for="cep" class="form-label">CEP*</label>
                            <input type="text" class="form-control" id="cep" name="cep" maxlength="9" inputmode="numeric" value="<?php echo htmlspecialchars($cep); ?>" placeholder="00000-000" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-9">
                            <label for="rua" class="form-label">Rua/Avenida*</label>
                            <input type="text" class="form-control" id="rua" name="rua" maxlength="120" value="<?php echo htmlspecialchars($rua); ?>" placeholder="Nome da rua ou avenida" required>
                        </div>
                        <div class="col-md-3">
                            <label for="numero" class="form-label">Número*</label>
                            <input type="text" class="form-control" id="numero" name="numero" maxlength="6" inputmode="numeric" value="<?php echo htmlspecialchars($numero); ?>" placeholder="Nº" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="complemento" class="form-label">Complemento</label>
                            <input type="text" class="form-control" id="complemento" name="complemento" maxlength="120" value="<?php echo htmlspecialchars($complemento); ?>" placeholder="Apto, Bloco, Casa, etc.">
                        </div>
                        <div class="col-md-6">
                            <label for="bairro" class="form-label">Bairro*</label>
                            <input type="text" class="form-control" id="bairro" name="bairro" maxlength="100" value="<?php echo htmlspecialchars($bairro); ?>" placeholder="Nome do bairro" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <a href="perfil.php#meus-enderecos" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn profile-btn"><?php echo $modo_edicao ? 'Atualizar' : 'Salvar'; ?> Endereço</button>
                    </div>
                <div class="mb-3"><label for="cidade">Cidade</label><input type="text" class="form-control" id="cidade" name="cidade" maxlength="100" value="<?= htmlspecialchars($cidade, ENT_QUOTES, 'UTF-8') ?>" required></div>
</form>
            </div>
        </div>
    </div>
</div>

<?php include_once "../rodape.html"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="/brutus/cadastro/cep.js?v=2" defer></script>
</body>
</html>
