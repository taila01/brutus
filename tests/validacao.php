<?php
require __DIR__.'/../validacao.php';
function verificar(bool $condicao,string $mensagem): void {if(!$condicao)throw new RuntimeException($mensagem);}
verificar(cpfValido('529.982.247-25'),'CPF válido formatado');
verificar(cpfValido('11144477735'),'CPF válido sem pontuação');
verificar(!cpfValido('52998224726'),'CPF com dígito errado');
verificar(!cpfValido('00000000000'),'CPF com dígitos repetidos');
verificar(!cpfValido('123'),'CPF incompleto');
$p=['nome'=>'João da Silva','email'=>'joao@example.test','cpf'=>'52998224725','telefone'=>'(11) 99999-9999'];
verificar(validarPessoa($p)===[],'Pessoa válida');
verificar(validarPessoa(array_replace($p,['telefone'=>'(11) 3333-4444']))===[],'Telefone fixo válido');
foreach(['123','119999999999','0012345678','abcdefghijk'] as $telefone) verificar(validarPessoa(array_replace($p,['telefone'=>$telefone]))!==[],'Telefone inválido: '.$telefone);
verificar(validarPessoa(array_replace($p,['nome'=>str_repeat('a',101)]))!==[],'Nome longo');
verificar(validarPessoa(array_replace($p,['email'=>'inválido']))!==[],'Email inválido');
verificar(campo(['nome'=>['array']],'nome')==='','Não aceitar arrays');
$e=['rua'=>"Rua D'Ávila",'bairro'=>'Jardim das Flores','cidade'=>'São José dos Campos','cep'=>'12200-000','numero'=>'123','complemento'=>''];
verificar(validarEndereco($e)===[],'Endereço válido com cidade longa e apóstrofo');
foreach(['0','-1','12a','1000000'] as $numero)verificar(validarEndereco(array_replace($e,['numero'=>$numero]))!==[],'Número inválido');
verificar(validarEndereco(array_replace($e,['cep'=>'123']))!==[],'CEP incompleto');
verificar(validarSenha('Teste!123','Teste!123')===[],'Senha válida');
verificar(validarSenha('123','123')!==[],'Senha curta');
verificar(validarSenha('Teste!123','diferente')!==[],'Confirmação inválida');
$hash=password_hash('Teste!123',PASSWORD_DEFAULT);
verificar(senhaCorreta('Teste!123',$hash),'Login com hash novo');
verificar(senhaCorreta('Teste!123',md5('Teste!123')),'Compatibilidade com senha antiga');
verificar(!senhaCorreta('errada',$hash),'Senha errada rejeitada');
echo "Validações PHP: OK.\n";
