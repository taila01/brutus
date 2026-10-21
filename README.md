# Brutus - Sistema de Pedidos Online

## Descrição do Projeto

O projeto **Brutus** é um sistema de pedidos online (e-commerce) robusto, desenvolvido para o segmento de alimentação, como hamburguerias ou restaurantes. Ele foi construído utilizando **PHP 8.2.12** e banco de dados **MariaDB 10.4.32** [1].

O sistema oferece uma experiência completa, desde a navegação no cardápio até a finalização do pedido, contando com uma interface moderna e responsiva baseada no **Bootstrap 5.3**.

---

## Funcionalidades Principais

O sistema é dividido em dois módulos principais para garantir uma gestão eficiente e uma ótima experiência ao usuário:

| Módulo | Funcionalidades | Arquivos Chave |
| :--- | :--- | :--- |
| **Área do Cliente** | - Cadastro e Login seguro. | `cadastro/cadastro.php` |
| | - Cardápio dinâmico por categorias. | `cardapio/cardapio.php` |
| | - Carrinho de compras interativo. | `carrinho/carrinho.php` |
| | - Gestão de endereços e perfil. | `usuario/perfil.php` |
| | - Checkout simplificado. | `comprar/identificacao.php` |
| **Painel Adm** | - Gestão completa de produtos (CRUD). | `admproduto/painel.php` |
| | - Relatórios detalhados de itens. | `admproduto/relatorio.php` |
| | - Rastreamento de pedidos em tempo real. | `admproduto/painel.php` |

---

## Tecnologias Utilizadas

* **Linguagem:** PHP (v8.2.12)
* **Banco de Dados:** MariaDB / MySQL  (v10.4.32)
* **Front-end:** HTML5 , CSS3 , JavaScript
* **Framework UI:** Bootstrap 5.3
* **Ícones:** Font Awesome

---

## Estrutura de Pastas

```bash
brutus-main/
├── admproduto/      # Gestão administrativa
├── backup/          # Backups do banco de dados (.sql)
├── borcer/          # Fontes personalizadas
├── cadastro/        # Módulo de novos usuários
├── cardapio/        # Listagem de produtos
├── carrinho/        # Lógica de compras
├── comprar/         # Processo de checkout
├── img/             # Assets visuais e logos
├── login/           # Autenticação
├── produtos/        # Fotos dos itens
├── sobre/           # Informações institucionais
├── usuario/         # Painel do cliente
└── index.php        # Porta de entrada do sistema
```

---

## Configuração e Instalação

### 1️Pré-requisitos

Ambiente de servidor local (ex: **XAMPP**, **WAMP** ou **Laragon**) com:

* PHP 8.2+
* MySQL/MariaDB

### 2️Banco de Dados

1. Crie um banco chamado `brutus`.
2. Importe o arquivo `backup/brutus_CERTO.sql`.

### 3️Conexão

Ajuste as credenciais no arquivo `userdata.php`:

```php
$servername = "localhost";
$username = "root"; 
$password = "";     
$dbname = "brutus";
```

---

## Referências

[1] phpMyAdmin. *Versão do PHP: 8.2.12*. Metadados extraídos do arquivo de exportação SQL. Disponível em: `/home/ubuntu/brutus_project/brutus-main/backup/brutus_CERTO.sql`.

---
*Desenvolvido por Caroline, Paulo e Taila.*

## Prévia visual local

- Página inicial: `/brutus/`
- Combos: `/brutus/combos.php`
- Promoções: `/brutus/promocoes.php`
- As três ofertas locais de demonstração ficam em `promocoes-config.php`. Os IDs correspondem ao backup `brutus_CERTO.sql`; revisar ao trocar o catálogo no Aiven.
- `preco-produto.php` mantém os valores consistentes entre catálogo, carrinho e checkout.
- O backup antigo precisa de ajustes de estrutura para as telas mais recentes. O script CLI `scripts/atualizar-banco-local.php` adiciona os campos ausentes sem apagar os registros. Ele foi aplicado somente no banco local; não executa pelo navegador.
- Antes de apontar para o Aiven, revisar a configuração de conexão e os descontos de demonstração. O histórico guarda o preço unitário dos novos pedidos em `itens_pedido.preco_unitario`.

### Importação no Aiven

Para banco **vazio**, usar `backup/brutus_AIVEN.sql`. Esse arquivo inclui as chaves primárias na própria criação das tabelas e os campos das telas atuais. Contém somente a estrutura e o catálogo inicial, sem clientes, senhas, endereços ou pedidos.

No cliente MySQL já conectado ao Aiven:

```sql
USE brutus;
SOURCE /home/taila/brutus/backup/brutus_AIVEN.sql;
SHOW TABLES;
SELECT COUNT(*) FROM itens;
```

A carga contém 10 tabelas e 21 produtos. As fotos padronizadas e as ofertas de demonstração ficam nos arquivos do projeto, não no SQL. Não repetir essa carga em banco já populado.

### Conectar o site ao Aiven

Todas as páginas usam `database.php`. As credenciais ficam em `.env.local.php`, ignorado pelo Git. Em uma instalação nova, copie `.env.example.php` para `.env.local.php`.

1. Baixe o certificado CA na página Overview do serviço Aiven e salve em `certs/aiven-ca.pem`.
2. Em `.env.local.php`, preencha `aiven.password` e altere `connection` de `local` para `aiven`. Host, porta 16678, usuário `avnadmin` e banco `brutus` já estão preenchidos.
3. Execute `php scripts/testar-conexao.php` na pasta do projeto. O comando valida PDO, MySQLi, TLS e a leitura dos produtos, sem alterar dados.
4. Atualize a página do site. O arquivo de configuração é lido a cada requisição.

Para voltar ao banco local, altere `connection` para `local`. A conexão Aiven exige um certificado CA legível e não faz fallback silencioso para o banco local.

### Validações dos formulários

CPF com verificação dos dígitos, telefone fixo/celular com DDD, CEP de oito dígitos e número de endereço entre 1 e 999999 são validados no navegador e no servidor. Nome até 100 caracteres, e-mail até 254, rua/complemento até 120, bairro/cidade até 100. Cadastro preserva dados após erros e recusa e-mail duplicado. As senhas novas usam `password_hash`; senhas antigas continuam aceitas pelo login. Produtos limitam nome a 50, descrição a 200 e preço a duas casas decimais entre 0,01 e 99999,99. Carrinho limita a 99 unidades por item.

O banco local já recebeu a ampliação dos campos. Para o banco Aiven já importado, execute somente a atualização incremental, sem reimportar o catálogo:

```sql
SOURCE /home/taila/brutus/backup/melhorias-campos.sql;
```

Testes de validação: `php tests/validacao.php`.

### Consulta de CEP

O script `cadastro/cep.js` é compartilhado por cadastro, endereços do perfil e checkout. Consulta automaticamente ao completar oito dígitos e também pelo botão Buscar CEP. Prioriza o endpoint documentado do BrasilCEP (`https://api.brasilcep.dev/v1/{cep}`); em falhas de conexão, timeout, resposta inválida ou HTTP, tenta a BrasilAPI (`https://brasilapi.com.br/api/cep/v1/{cep}`). Cada tentativa tem timeout de quatro segundos. O BrasilCEP estava sem resolução DNS na verificação; a BrasilAPI respondeu à consulta real.

Rua, bairro, cidade e UF (quando o formulário tiver o campo) são preenchidos. Alterações manuais são preservadas; número e complemento não são alterados. Respostas de CEPs anteriores são descartadas e resultados válidos são mantidos em cache na página. A indisponibilidade dos serviços permite preenchimento manual.

Teste: `node --test tests/cep.test.cjs`.

Referências: https://brasilcep.github.io/ e https://brasilapi.com.br/docs#tag/CEP.

### Adicionar ao pedido no cardápio

O botão `+` dos cards atualiza a sacola por `POST /brutus/carrinho/itens.php`, sem navegar para o carrinho. O card mostra Adicionado e a quantidade, com botões para aumentar e diminuir. O cabeçalho exibe o total de unidades. O mesmo comportamento está na página inicial, cardápio, combos e promoções. A sacola é mantida na sessão, inclusive enquanto o visitante ainda não entrou na conta; o login continua necessário para acessar o carrinho e finalizar a compra. Quantidades permanecem limitadas a 99 por produto, verificadas no servidor.

### Identidade urbana e contato

O tema aprovado está em `assets/urban.css`, aplicado pela classe `brutus-urban` nas páginas. Usa papel creme, tomate, cheddar e verde de alface, com títulos grandes, adesivos e molduras. Cada página pode variar a cor via `data-flavor`.

Os elementos de WhatsApp no rodapé e no atalho fixo são apenas visuais e ficam desativados. Nenhum número ou link de conversa está configurado.

### Modo claro e noturno

O botão de lua/sol ao lado da conta alterna o tema em todas as páginas. `assets/tema.js` recupera a preferência antes de renderizar e a salva na chave `brutus-theme` do localStorage; `assets/tema.css` fornece paletas noturnas por ingrediente e adapta textos, cards, formulários e checkout. O modo inicial é claro quando não há preferência salva. O botão funciona mesmo se o navegador bloquear o armazenamento, e as abas sincronizam mudanças da preferência.

Testes: `node --test tests/tema.test.cjs`.
