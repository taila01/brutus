<?php
session_start();

include_once "conexao.php";
require_once __DIR__ . "/../validacao.php";

$mensagem_sucesso = '';
$erros = [];
$nome = '';
$email = '';
$telefone = '';
$cpf = '';
$pedidos = [];
$enderecos = [];

try {
    $conn = brutusPDO();
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (empty($_SESSION['id_logado'])) {
        header('Location: /brutus/login/login.php');
        exit;
    }

    $usuario_id = (int) $_SESSION['id_logado'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $novo_nome = campo($_POST,'nome');
        $novo_email = campo($_POST,'email');
        $novo_telefone = campo($_POST,'telefone');
        $novo_cpf = campo($_POST,'cpf');
        $senha_atual = (is_string($_POST['senha_atual'] ?? null) ? $_POST['senha_atual'] : '');
        $nova_senha = (is_string($_POST['nova_senha'] ?? null) ? $_POST['nova_senha'] : '');
        $confirmar_nova_senha = (is_string($_POST['confirmar_nova_senha'] ?? null) ? $_POST['confirmar_nova_senha'] : '');

        $erros = validarPessoa(['nome'=>$novo_nome,'email'=>$novo_email,'telefone'=>$novo_telefone,'cpf'=>$novo_cpf]);
        if (emailEmUso($conn,$novo_email,$usuario_id)) $erros[] = 'Este e-mail já está em uso por outra conta.';
        $novo_cpf = digitos($novo_cpf);
        $novo_telefone = digitos($novo_telefone);
        $nome=$novo_nome; $email=$novo_email; $telefone=$novo_telefone; $cpf=$novo_cpf;
        $alterar_senha = $senha_atual !== '' || $nova_senha !== '' || $confirmar_nova_senha !== '';
        if ($alterar_senha) $erros = array_merge($erros,validarSenha($nova_senha,$confirmar_nova_senha));

        if ($alterar_senha) {
            $stmt = $conn->prepare('SELECT senha FROM usuario WHERE codigo = :id');
            $stmt->bindParam(':id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                $erros[] = 'Usuário não encontrado';
            } else {
                $senha_bd = $stmt->fetch(PDO::FETCH_ASSOC)['senha'];

                if (!senhaCorreta($senha_atual,$senha_bd)) {
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
                $stmt->bindValue(':senha', password_hash($nova_senha,PASSWORD_DEFAULT));
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
    $enderecos = $stmt_enderecos->fetchAll(PDO::FETCH_ASSOC);

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

<?php $esc = static fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); unset($pedido); ?>
<!DOCTYPE html><html lang="pt-BR"><head>
<script src="/brutus/assets/tema.js?v=1"></script><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Minha conta | Brutus</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="/brutus/geral.css"><link rel="stylesheet" href="/brutus/interface.css?v=3"></head><body class="brutus-urban" data-flavor="mostarda">
<?php include __DIR__ . '/../cabecalho.html'; ?>
<main class="container profile-shell"><p class="eyebrow">Seu lugar na casa</p><h1 class="page-title">Olá, <?= $esc(explode(' ', trim($nome))[0] ?: 'brutal') ?>.</h1><p class="page-description">Seus dados, endereços e pedidos, do seu jeito.</p>
<?php if ($mensagem_sucesso): ?><div class="alert alert-success" role="status"><?= $esc($mensagem_sucesso) ?></div><?php endif; ?>
<?php foreach ($erros as $erro): ?><div class="alert alert-danger" role="alert"><?= $esc($erro) ?></div><?php endforeach; ?>
<div class="profile-layout"><nav class="profile-sidebar" aria-label="Minha conta"><a href="#editar-dados">Meus dados</a><a href="#meus-enderecos">Endereços de entrega</a><a href="#historico-pedidos">Meus pedidos</a><a href="/brutus/login/sair.php">Sair da conta ↗</a></nav><div class="profile-content">
<section id="editar-dados" class="card"><div class="card-body"><h2 class="profile-title">Meus dados</h2><form method="post"><div class="profile-fields">
<?php foreach (['nome'=>['Nome completo',$nome,'text'], 'email'=>['E-mail',$email,'email'], 'telefone'=>['Telefone',$telefone,'tel'], 'cpf'=>['CPF',$cpf,'text']] as $campo=>[$label,$valor,$tipo]): ?><div><label for="<?= $campo ?>"><?= $label ?></label><input class="form-control" id="<?= $campo ?>" name="<?= $campo ?>" type="<?= $tipo ?>" value="<?= $esc($valor) ?>" required></div><?php endforeach; ?>
<div><label for="senha_atual">Senha atual (para alterar a senha)</label><input class="form-control" type="password" id="senha_atual" name="senha_atual" autocomplete="current-password"></div><div><label for="nova_senha">Nova senha</label><input class="form-control" type="password" id="nova_senha" name="nova_senha" minlength="8" maxlength="72" autocomplete="new-password"></div><div><label for="confirmar_nova_senha">Confirme a nova senha</label><input class="form-control" type="password" id="confirmar_nova_senha" name="confirmar_nova_senha" minlength="8" maxlength="72" autocomplete="new-password"></div></div><button class="btn btn-warning mt-4" type="submit">Salvar alterações</button></form></div></section>
<section id="meus-enderecos"><div class="section-heading"><h2 class="profile-title">Endereços</h2><a class="btn btn-warning" href="cadastro-endereco.php">+ Novo endereço</a></div><div class="address-grid"><?php foreach ($enderecos as $endereco): ?><article class="card"><div class="card-body"><h3 class="h5"><?= $esc($endereco['identificacao'] ?? 'Endereço de entrega') ?></h3><p class="card-text"><?= $esc($endereco['rua']) ?>, <?= $esc($endereco['numero']) ?><br><?= $esc($endereco['bairro']) ?> · <?= $esc($endereco['cidade']) ?><br>CEP <?= $esc($endereco['cep']) ?></p><a class="btn btn-outline-secondary" href="cadastro-endereco.php?id=<?= (int) $endereco['cod_endereco'] ?>">Editar endereço</a></div></article><?php endforeach; ?></div><?php if (!$enderecos): ?><div class="empty-state"><h2>Onde entregamos?</h2><p>Adicione seu primeiro endereço para agilizar o próximo pedido.</p></div><?php endif; ?></section>
<section id="historico-pedidos"><h2 class="profile-title">Meus pedidos</h2><?php if (!$pedidos): ?><div class="empty-state"><h2>Todo favorito tem um começo.</h2><p>Seu histórico aparece aqui depois do primeiro pedido.</p><a class="primary-button" href="/brutus/cardapio/cardapio.php">Escolher meu Brutus ↗</a></div><?php endif; ?><?php foreach ($pedidos as $pedido): ?><article class="card mb-3"><div class="card-body"><div class="d-flex justify-content-between flex-wrap gap-3"><div><h3 class="h5">Pedido #<?= (int) $pedido['cod_pedido'] ?></h3><p class="card-text"><?= $esc($pedido['status_atual'] ?: 'Recebido') ?> · <?= (int) $pedido['quant_itens'] ?> itens</p></div><div><strong class="text-warning">R$ <?= number_format((float) $pedido['total_pedidos'],2,',','.') ?></strong><br><a href="detalhe_pedido.php?id=<?= (int) $pedido['cod_pedido'] ?>">Ver detalhes ↗</a></div></div></div></article><?php endforeach; ?></section>
</div></div></main><?php include __DIR__ . '/../rodape.html'; ?></body></html>
