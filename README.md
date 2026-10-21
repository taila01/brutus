# Brutus — Burger House

Sistema de pedidos em PHP 8.2+ e MySQL/MariaDB, com cardápio, combos, promoções, cadastro, sacola, checkout e painel administrativo.

## Estrutura

```text
brutus/
├── app/
│   ├── database.php         # Fábricas de conexão PDO e MySQLi
│   ├── conexoes/            # Adaptadores utilizados pelas páginas
│   ├── helpers/             # Validação, preços e imagens dos produtos
│   └── views/
│       ├── layout/         # Cabeçalho e rodapé compartilhados
│       └── components/     # Cards reutilizáveis
├── config/                 # Ofertas e mapeamento das fotos do catálogo
├── database/
│   ├── schema.sql          # Estrutura atual e catálogo inicial, sem clientes
│   ├── migrations/         # Atualizações para bancos existentes
│   └── legacy/             # Exportações históricas; não usar em instalações novas
├── public/                 # Única pasta exposta pelo servidor web
│   ├── assets/
│   │   ├── css/            # Estilos globais, temas e estilos por módulo
│   │   ├── js/             # Tema, formulários, CEP e sacola
│   │   ├── img/            # Logos, banners e ícones do checkout
│   │   └── fonts/          # Fontes locais
│   ├── produtos/           # Fotos do catálogo e uploads do administrador
│   ├── admproduto/         # Produtos, pedidos e relatórios administrativos
│   ├── cadastro/           # Cadastro do cliente
│   ├── cardapio/           # Listagem por categoria
│   ├── carrinho/           # Sacola e API de quantidades
│   ├── comprar/            # Identificação, endereço e pagamento
│   ├── login/              # Autenticação e histórico
│   ├── sobre/              # História da hamburgueria
│   ├── usuario/            # Perfil, endereços e detalhes do pedido
│   ├── index.php           # Página inicial
│   ├── combos.php
│   └── promocoes.php
├── scripts/                # Servidor local e manutenção por linha de comando
├── tests/                  # Testes de comportamento e estrutura
├── certs/                  # Certificado CA privado, ignorado pelo Git
├── .env.example.php        # Modelo de configuração
└── .env.local.php          # Configuração privada, ignorada pelo Git
```

As URLs continuam usando o prefixo `/brutus/`. Os includes PHP usam `__DIR__`, para funcionar independentemente da pasta de execução. Código compartilhado fica em `app/`, regras configuráveis em `config/` e arquivos estáticos em `public/assets/`. O CEP tem uma única implementação compartilhada.

## Rodar localmente

Requisitos: PHP 8.2+ com `pdo_mysql`, `mysqli` e `mbstring`, MySQL/MariaDB e Node.js para os testes de JavaScript.

1. Copie `.env.example.php` para `.env.local.php` e configure a conexão.
2. Em um banco vazio chamado `brutus`, importe `database/schema.sql`. A carga inclui 10 tabelas e 21 produtos, sem usuários, senhas, endereços ou pedidos. Crie sua conta pelo cadastro.
3. Confira a conexão e inicie o servidor na raiz do projeto:

```sh
php scripts/testar-conexao.php
php -S 127.0.0.1:8091 -t public scripts/router.php
```

Acesse **http://localhost:8091/brutus/**. O roteador de desenvolvimento mantém esse prefixo e entrega somente arquivos de `public/`.

Para Apache, configure `Alias /brutus` apontando para o caminho absoluto de `public/`, com acesso permitido a esse diretório. No Nginx, configure a localização `/brutus/` e o PHP-FPM para essa mesma pasta. A raiz do repositório não deve ser usada como raiz pública. A pasta `public/produtos/` precisa permitir gravação para os uploads do painel.

## Banco e Aiven

As conexões são centralizadas em `app/database.php`. A senha fica em `.env.local.php` e o certificado em `certs/aiven-ca.pem`; ambos permanecem fora do Git.

Para um banco Aiven **vazio**, no cliente MySQL conectado:

```sql
USE brutus;
SOURCE /home/taila/brutus/database/schema.sql;
SHOW TABLES;
SELECT COUNT(*) FROM itens;
```

Para um banco **já importado**, use apenas a atualização incremental necessária:

```sql
SOURCE /home/taila/brutus/database/migrations/001-melhorias-campos.sql;
```

Não reimporte a carga inicial em banco populado. Os arquivos em `database/legacy/` são exportações anteriores e ficam separados do SQL atual.

Em `.env.local.php`, preencha `aiven.password`, baixe o CA e altere `connection` para `aiven`. O modelo já contém host, porta 16678, usuário `avnadmin` e banco `brutus`. Execute `php scripts/testar-conexao.php` para conferir PDO, MySQLi, TLS e a leitura do catálogo. Para voltar ao ambiente local, use `connection => local`.

O script `scripts/atualizar-banco-local.php` adapta bancos antigos às colunas usadas pelas telas atuais e deve ser executado manualmente apenas no banco pretendido.

## Manutenção

- Promoções: `config/promocoes.php`; revise os IDs e valores ao alterar o catálogo. `app/helpers/preco-produto.php` compartilha os preços entre catálogo e checkout.
- Fotos: `config/fotos-catalogo.php` e `public/produtos/padronizados/`. Novos uploads têm prioridade. Os prompts estão em `docs/fotografia-catalogo.md`.
- Interface: `public/assets/css/urban.css` e `tema.css`. As páginas escolhem a paleta com `data-flavor`; o botão lua/sol guarda a preferência em `brutus-theme` no navegador.
- CEP: `public/assets/js/cep.js`, compartilhado por cadastro, endereços e checkout. Consulta BrasilCEP e usa BrasilAPI como alternativa; preserva alterações manuais e permite preenchimento manual quando indisponível.
- Sacola: `public/assets/js/pedido.js` e `public/carrinho/itens.php`. Adiciona e remove sem redirecionar, mostra quantidade e limita a 99 unidades por produto.
- Validação: `app/helpers/validacao.php` e `public/assets/js/formularios.js`. CPF, telefone, CEP, endereços e limites são verificados no servidor e navegador. Senhas novas usam `password_hash`; o login mantém compatibilidade com hashes antigos.
- WhatsApp: os botões são apenas visuais e estão desativados, sem telefone ou link configurado.

## Verificação

Na raiz do projeto:

```sh
php tests/estrutura.php
php tests/validacao.php
node --test tests/*.cjs
```

Desenvolvido por Caroline, Paulo e Taila.
