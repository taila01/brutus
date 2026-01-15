# 🍔 Brutus - Sistema de Pedidos Online

## Descrição do Projeto

O projeto **Brutus** é um sistema de pedidos online (e-commerce) robusto, desenvolvido para o segmento de alimentação, como hamburguerias ou restaurantes. Ele foi construído utilizando **PHP 8.2.12** e banco de dados **MariaDB 10.4.32** [1].

O sistema oferece uma experiência completa, desde a navegação no cardápio até a finalização do pedido, contando com uma interface moderna e responsiva baseada no **Bootstrap 5.3**.

---

## 🚀 Funcionalidades Principais

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

## 🛠️ Tecnologias Utilizadas

* **Linguagem:** PHP (v8.2.12)
* **Banco de Dados:** MariaDB / MySQL  (v10.4.32)
* **Front-end:** HTML5 , CSS3 , JavaScript
* **Framework UI:** Bootstrap 5.3
* **Ícones:** Font Awesome

---

## 📂 Estrutura de Pastas

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

## ⚙️ Configuração e Instalação

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
