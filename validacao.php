<?php
function campo(array $dados, string $chave): string
{
    return isset($dados[$chave]) && is_string($dados[$chave]) ? trim($dados[$chave]) : '';
}
function digitos(string $valor): string { return preg_replace('/\D/', '', $valor); }
function cpfValido(string $valor): bool
{
    $cpf = digitos($valor);
    if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) return false;
    for ($t = 9; $t < 11; $t++) {
        $soma = 0;
        for ($i = 0; $i < $t; $i++) $soma += (int) $cpf[$i] * ($t + 1 - $i);
        $resto = $soma % 11;
        $digito = $resto < 2 ? 0 : 11 - $resto;
        if ($digito !== (int) $cpf[$t]) return false;
    }
    return true;
}
function validarTexto(string $valor, string $rotulo, int $maximo, bool $obrigatorio = true): array
{
    if ($obrigatorio && $valor === '') return ["$rotulo é obrigatório."];
    if (mb_strlen($valor) > $maximo) return ["$rotulo deve ter no máximo $maximo caracteres."];
    return [];
}
function validarPessoa(array $dados, bool $comTelefone = true): array
{
    $nome = campo($dados, 'nome');
    $email = campo($dados, 'email');
    $erros = validarTexto($nome, 'Nome', 100);
    if ($nome !== '' && (mb_strlen($nome) < 3 || !preg_match('/\p{L}/u', $nome))) $erros[] = 'Informe um nome válido com pelo menos 3 caracteres.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) $erros[] = 'Informe um e-mail válido de até 254 caracteres.';
    $cpf = campo($dados, 'cpf');
    if (!preg_match('/^[\d.\-\s]+$/', $cpf) || !cpfValido($cpf)) $erros[] = 'Informe um CPF válido com 11 dígitos.';
    if ($comTelefone) {
        $telefone = campo($dados, 'telefone');
        if (!preg_match('/^[\d()\-\s]+$/', $telefone) || !preg_match('/^[1-9]{2}(?:[2-5]\d{7}|9\d{8})$/', digitos($telefone))) $erros[] = 'Informe um telefone com DDD: 10 dígitos para fixo ou 11 para celular.';
    }
    return $erros;
}
function validarEndereco(array $dados): array
{
    $erros = [];
    foreach (['rua'=>['Rua',120], 'bairro'=>['Bairro',100], 'cidade'=>['Cidade',100]] as $campo=>[$rotulo,$limite]) {
        $erros = array_merge($erros, validarTexto(campo($dados,$campo),$rotulo,$limite));
    }
    $cep = campo($dados,'cep') ?: campo($dados,'CEP');
    if (!preg_match('/^\d{5}-?\d{3}$/', $cep)) $erros[] = 'Informe um CEP com 8 dígitos.';
    if (!preg_match('/^[1-9]\d{0,5}$/', campo($dados,'numero'))) $erros[] = 'O número do endereço deve estar entre 1 e 999999.';
    $erros = array_merge($erros, validarTexto(campo($dados,'complemento'),'Complemento',120,false));
    if (isset($dados['identificacao'])) $erros = array_merge($erros, validarTexto(campo($dados,'identificacao'),'Identificação',50));
    return $erros;
}
function validarSenha(string $senha, string $confirmacao): array
{
    $erros = [];
    if (mb_strlen($senha) < 8) $erros[] = 'A senha deve ter pelo menos 8 caracteres.';
    if (strlen($senha) > 72) $erros[] = 'A senha é muito longa. Use uma senha menor.';
    if ($senha !== $confirmacao) $erros[] = 'As senhas não coincidem.';
    return $erros;
}
function emailEmUso(PDO $db, string $email, int $exceto = 0): bool
{
    $q = $db->prepare('SELECT codigo FROM usuario WHERE LOWER(email) = LOWER(?) AND codigo <> ? LIMIT 1');
    $q->execute([$email,$exceto]);
    return (bool) $q->fetchColumn();
}
function senhaCorreta(string $senha, string $hash): bool
{
    return preg_match('/^[a-f0-9]{32}$/i', $hash) ? hash_equals(strtolower($hash),md5($senha)) : password_verify($senha,$hash);
}
function formularioFeedback(string $chave): void
{
    foreach ($_SESSION[$chave] ?? [] as $erro) echo '<div class="alert alert-danger" role="alert">'.htmlspecialchars($erro,ENT_QUOTES,'UTF-8').'</div>';
    unset($_SESSION[$chave]);
}
