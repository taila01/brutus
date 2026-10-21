<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/../../app/helpers/validacao.php';
$old = $_SESSION['cadastro_old'] ?? [];
unset($_SESSION['cadastro_old']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<script src="/brutus/assets/js/tema.js?v=1"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro | BRUTUS</title>
    <link rel="icon" href="..\img\favicon.svg" type="image/x-icon">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/brutus/assets/css/geral.css">
    <link rel="stylesheet" href="/brutus/assets/css/cadastro/cadastro.css">
<link rel="stylesheet" href="/brutus/assets/css/interface.css?v=3">
</head>
<body class="brutus-urban" data-flavor="alface">

<?php include_once __DIR__ . '/../../app/views/layout/cabecalho.php'; ?>
    <div class="container mt-5">
        <p class="eyebrow">VEM PRO BONDE.</p><h1 class="page-title">A MESA TAMBÉM É SUA.</h1><p class="page-description">Um cadastro rapidinho. Muitos burgers pela frente.</p>
        <p class="signup-login">Já tem conta? <a href="/brutus/login/login.php">Entrar na minha conta</a></p>
        <?php formularioFeedback("cadastro_erros"); ?>
        <form action="cadastro.php" method="POST">
            <div class="form-group">
                <label for="nome">Nome* </label>
                <input type="text" class="form-control" id="nome" name="nome" maxlength="100" minlength="3" placeholder="Nome e sobrenome" required value="<?= htmlspecialchars($old['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

            </div>

            <div class="form-group">
                <label for="email">E-mail*</label>
                <input type="email" class="form-control" id="email" name="email" maxlength="254" autocomplete="email" placeholder="email@email.com" required value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label for="cpf">CPF*</label>
                <input type="text"  class="form-control" id="cpf" name="cpf" maxlength="14" inputmode="numeric" placeholder="123.456.789-00" required value="<?= htmlspecialchars($old['cpf'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label for="telefone">Telefone*</label>
                <input type="tel" class="form-control" id="telefone" name="telefone" maxlength="15" inputmode="numeric" placeholder="(14) 99899-9999" required value="<?= htmlspecialchars($old['telefone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <small class="form-text text-danger d-none" id="telefoneError">Telefone inválido.</small>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="cep">CEP*</label>
                    <input type="text" class="form-control" id="cep" name="cep" maxlength="9" inputmode="numeric" placeholder="00000-000" required value="<?= htmlspecialchars($old['cep'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="form-group col-md-8">
                    <label for="rua">Rua*</label>
                    <input type="text" class="form-control" id="rua" name="rua" maxlength="120" placeholder="Digite sua rua" required value="<?= htmlspecialchars($old['rua'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-9">
                    <label for="bairro">Bairro*</label>
                    <input type="text" class="form-control" id="bairro" name="bairro" maxlength="100" placeholder="Digite seu bairro" required value="<?= htmlspecialchars($old['bairro'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group col-md-3">
                    <label for="numero">Nº da Casa*</label>
                    <input type="text" class="form-control" id="numero" name="numero" maxlength="6" inputmode="numeric" placeholder="Número" required value="<?= htmlspecialchars($old['numero'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>

            <div class="form-group"><label for="cidade">Cidade*</label><input type="text" class="form-control" id="cidade" name="cidade" maxlength="100" required value="<?= htmlspecialchars($old['cidade'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></div>
            <div class="form-group">
                <label for="complemento">Complemento</label>
                <input type="text" class="form-control" id="complemento" name="complemento" maxlength="120" placeholder="Apartamento, bloco, etc." value="<?= htmlspecialchars($old['complemento'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label for="senha">Senha*</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="senha" name="senha" minlength="8" maxlength="72" autocomplete="new-password" placeholder="**********" required>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary"style="background-color:#ef890d; color: white;" type="button" onclick="togglePassword('senha')">Mostrar</button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="confirmarSenha">Confirmar Senha*</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="confirmarSenha" name="confirmarSenha" minlength="8" maxlength="72" autocomplete="new-password" placeholder="**********" required>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" style="background-color: #ef890d; color: white;" type="button" onclick="togglePassword('confirmarSenha')">Mostrar</button>
                    </div>
                </div>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="termos" name="termos" value="1" required>
                <label class="form-check-label" for="termos">
                    Aceito os termos e condições da Política de Privacidade. *
                </label>
            </div>

            <button type="submit" class="btn btn-block mt-4"  name="btnCadastrar" style="background-color: #ef890d; color: white;">Criar Conta</button>
        </form>
    </div>
    <?php include_once __DIR__ . '/../../app/views/layout/rodape.php'; ?>
<script src="/brutus/assets/js/cep.js?v=2" defer></script>
</body>
</html>
