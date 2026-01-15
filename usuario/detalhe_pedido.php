<?php
session_start();
include_once "conexao.php";
$erros = [];


try {
    $conn = new PDO("mysql:host=$host;dbname=" . $database, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if (!isset($_SESSION['id_logado']) || empty($_SESSION['id_logado'])) {
        header("Location: login.php");
        exit;
    }
    
    $usuario_id = $_SESSION['id_logado'];
    
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        $_SESSION['erro'] = "ID do pedido não informado.";
        header("Location: perfil.php#historico-pedidos");
        exit;
    }
    
    $pedido_id = intval($_GET['id']);
    
    $query_pedido = "SELECT p.*, 
       (SELECT s.status_pedidos 
        FROM hist_status_ped h
        INNER JOIN status_pedidos s ON h.cod_status = s.cod_status_pedidos
        WHERE h.cod_pedido = p.cod_pedido
        ORDER BY h.data_hora DESC 
        LIMIT 1) AS status_pedidos,
       u.nome as nome_usuario, 
       u.email, 
       u.telefone
FROM pedidos p
INNER JOIN usuario u ON p.fk_Usuario_codigo = u.codigo
WHERE p.cod_pedido = :pedido_id AND p.fk_Usuario_codigo = :usuario_id";
    
    $stmt_pedido = $conn->prepare($query_pedido);
    $stmt_pedido->bindParam(':pedido_id', $pedido_id, PDO::PARAM_INT);
    $stmt_pedido->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt_pedido->execute();
    
    if ($stmt_pedido->rowCount() == 0) {
        $_SESSION['erro'] = "Pedido não encontrado ou não pertence ao usuário.";
        header("Location: perfil.php#historico-pedidos");
        exit;
    }
    
    $pedido = $stmt_pedido->fetch(PDO::FETCH_ASSOC);
    
    $data_pedido = date('d/m/Y', strtotime($pedido['datahora_pedido']));
    $hora_pedido = date('H:i', strtotime($pedido['datahora_pedido']));
    
    $query_endereco = "SELECT e.*
                      FROM endereco e
                      INNER JOIN pedidos p ON e.cod_endereco = p.cod_endereco
                      WHERE p.cod_pedido = :pedido_id";
    
    $stmt_endereco = $conn->prepare($query_endereco);
    $stmt_endereco->bindParam(':pedido_id', $pedido_id, PDO::PARAM_INT);
    $stmt_endereco->execute();
    
    $endereco = null;
    
    if ($stmt_endereco->rowCount() > 0) {
        $endereco = $stmt_endereco->fetch(PDO::FETCH_ASSOC);
    }
    
    $query_itens = "SELECT ip.*, i.nome, i.descricao, i.preco, i.imagem, ip.quantidade
                   FROM itens_pedido ip 
                   INNER JOIN itens i ON ip.cod_item = i.cod_item
                   WHERE ip.codigo_pedido = :pedido_id
                   ORDER BY i.nome";
    
    $stmt_itens = $conn->prepare($query_itens);
    $stmt_itens->bindParam(':pedido_id', $pedido_id, PDO::PARAM_INT);
    $stmt_itens->execute();
    
    $itens = [];
    
    if ($stmt_itens->rowCount() > 0) {
        $itens = $stmt_itens->fetchAll(PDO::FETCH_ASSOC);
    }
    
    $query_historico = "SELECT h.*, s.status_pedidos
                       FROM hist_status_ped h
                       INNER JOIN status_pedidos s ON h.cod_status = s.cod_status_pedidos
                       WHERE h.cod_pedido = :pedido_id
                       ORDER BY h.data_hora ASC";
    
    $stmt_historico = $conn->prepare($query_historico);
    $stmt_historico->bindParam(':pedido_id', $pedido_id, PDO::PARAM_INT);
    $stmt_historico->execute();
    
    $historico = [];
    
    if ($stmt_historico->rowCount() > 0) {
        $historico = $stmt_historico->fetchAll(PDO::FETCH_ASSOC);
    }
    
} catch (PDOException $e) {
    $erros[] = "Erro de conexão: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Pedido #<?php echo $pedido_id; ?> | BRUTUS</title>
    <link rel="icon" href="../img/favicon.svg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../geral.css"> 
    <link rel="stylesheet" href="perfil.css">
    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
            list-style: none;
            border-left: 1px solid #ccc;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }
        .timeline-item:before {
            content: '';
            position: absolute;
            left: -36px;
            top: 0;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #007bff;
            border: 3px solid #fff;
        }
        .timeline-item.active:before {
            background-color: #28a745;
        }
        .timeline-item.pending:before {
            background-color: #6c757d;
        }
        .timeline-date {
            font-size: 0.8rem;
            color: #6c757d;
        }
        .item-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        .order-summary {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
        }
        .order-summary hr {
            margin: 10px 0;
        }
        .order-total {
            font-size: 1.2rem;
            font-weight: bold;
        }
        .status-badge {
            font-size: 1rem;
            padding: 5px 10px;
        }
    </style>
</head>
<body>

<?php include_once "../cabecalho.html"; ?>

<div class="container my-3">
    <div class="row">
        <div class="col-md-3">
            <div class="list-group profile-menu">
                <a href="perfil.php#editar-dados" class="list-group-item list-group-item-action"><i class="fas fa-user-edit me-2"></i>Editar Dados</a>
                <a href="perfil.php#meus-enderecos" class="list-group-item list-group-item-action"><i class="fas fa-map-marker-alt me-2"></i>Meus Endereços</a>
                <a href="perfil.php#historico-pedidos" class="list-group-item list-group-item-action active"><i class="fas fa-history me-2"></i>Histórico de Pedidos</a>
                <a href="logout.php" class="list-group-item list-group-item-action"><i class="fas fa-sign-out-alt me-2"></i>Sair</a>
                <a href="#" class="list-group-item list-group-item-action text-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"><i class="fas fa-trash-alt me-2"></i>Excluir Conta</a>
            </div>
        </div>

        <div class="col-md-9">
            <?php if (!empty($erros)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        <?php foreach ($erros as $erro): ?>
                            <li><?php echo $erro; ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php else: ?>
                <div class="order-details-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="profile-title mb-0">
                            <i class="fas fa-receipt me-2"></i>Pedido #<?php echo $pedido_id; ?>
                        </h2>
                        <span class="badge status-badge <?php 
                            switch (strtolower($pedido['status_pedidos'])) {
                                case 'entregue': echo 'bg-success'; break;
                                case 'em preparo': case 'preparando': echo 'bg-warning text-dark'; break;
                                case 'em entrega': case 'saiu para entrega': echo 'bg-info text-dark'; break;
                                case 'cancelado': echo 'bg-danger'; break;
                                default: echo 'bg-secondary';
                            }
                        ?>">
                            <?php echo ucfirst($pedido['status_pedidos']); ?>
                        </span>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <i class="fas fa-info-circle me-2"></i>Informações do Pedido
                                </div>
                                <div class="card-body">
                                    <p class="mb-2"><strong>Data:</strong> <?php echo $data_pedido; ?> às <?php echo $hora_pedido; ?></p>
                                    <p class="mb-2"><strong>Cliente:</strong> <?php echo htmlspecialchars($pedido['nome_usuario']); ?></p>
                                    <p class="mb-2"><strong>Forma de Pagamento:</strong> <?php echo ucfirst($pedido['tipo_pagamento']); ?></p>
                                    <?php if (isset($pedido['observacoes']) && !empty($pedido['observacoes'])): ?>
                                        <p class="mb-2"><strong>Observações:</strong> <?php echo htmlspecialchars($pedido['observacoes']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <?php if ($endereco): ?>
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <i class="fas fa-map-marker-alt me-2"></i>Endereço de Entrega
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-1"><?php echo htmlspecialchars($endereco['identificacao'] ?? ''); ?></p>
                                        <p class="mb-1">
                                            <?php echo htmlspecialchars($endereco['rua']) . ', ' . htmlspecialchars($endereco['numero']); ?>
                                            <?php if (!empty($endereco['complemento'])): ?>
                                                , <?php echo htmlspecialchars($endereco['complemento']); ?>
                                            <?php endif; ?>
                                        </p>
                                        <p class="mb-1"><?php echo htmlspecialchars($endereco['bairro']); ?></p>
                                        <p class="mb-2">
                                            <?php echo htmlspecialchars($endereco['cidade']) . ' - SP'; ?>, 
                                            CEP <?php echo htmlspecialchars($endereco['cep']); ?>
                                        </p>
                                        <?php if (!empty($endereco['referencia'])): ?>
                                            <p class="mb-0"><small class="text-muted">Referência: <?php echo htmlspecialchars($endereco['referencia']); ?></small></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-shopping-cart me-2"></i>Itens do Pedido
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 80px"></th>
                                            <th>Item</th>
                                            <th class="text-center">Qtd</th>
                                            <th class="text-end">Preço Unit.</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $subtotal = 0;
                                        foreach ($itens as $item): 
                                            $item_subtotal = floatval($item['quantidade']) * floatval($item['preco']);
                                            $subtotal += $item_subtotal;
                                        ?>
                                            <tr>
                                                <td>
                                                    <?php if (!empty($item['imagem'])): ?>
                                                        <img src="../produtos/<?php echo htmlspecialchars($item['imagem']); ?>" class="item-image">
                                                    <?php else: ?>
                                                        <div class="item-image bg-light d-flex align-items-center justify-content-center">
                                                            <i class="fas fa-hamburger text-secondary"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($item['nome']); ?></strong>
                                                    <?php if (!empty($item['descricao'])): ?>
                                                        <br><small class="text-muted"><?php echo htmlspecialchars($item['descricao']); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center"><?php echo $item['quantidade']; ?></td>
                                                <td class="text-end">R$ <?php echo number_format(floatval($item['preco']), 2, ',', '.'); ?></td>
                                                <td class="text-end">R$ <?php echo number_format($item_subtotal, 2, ',', '.'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="order-summary ms-auto" style="max-width: 300px;">
                                <div class="d-flex justify-content-between">
                                    <span>Subtotal:</span>
                                    <span>R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></span>
                                </div>
                                
                                <?php if (isset($pedido['taxa_entrega']) && $pedido['taxa_entrega'] > 0): ?>
                                <div class="d-flex justify-content-between">
                                    <span>Taxa de entrega:</span>
                                    <span>R$ <?php echo number_format($pedido['taxa_entrega'], 2, ',', '.'); ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (isset($pedido['desconto']) && $pedido['desconto'] > 0): ?>
                                <div class="d-flex justify-content-between text-success">
                                    <span>Desconto:</span>
                                    <span>- R$ <?php echo number_format($pedido['desconto'], 2, ',', '.'); ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <hr>
                                <div class="d-flex justify-content-between order-total">
                                    <span>Total:</span>
                                    <span>R$ <?php echo number_format($pedido['total_pedidos'], 2, ',', '.'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($historico)): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-history me-2"></i>Acompanhamento do Pedido
                        </div>
                        <div class="card-body">
                            <ul class="timeline">
                                <?php foreach ($historico as $index => $status): 
                                    $status_class = ($index == count($historico) - 1) ? 'active' : '';
                                    $status_date = date('d/m/Y', strtotime($status['data_hora']));
                                    $status_time = date('H:i', strtotime($status['data_hora']));
                                ?>
                                <li class="timeline-item <?php echo $status_class; ?>">
                                    <div>
                                        <strong><?php echo ucfirst($status['status_pedidos']); ?></strong>
                                        <div class="timeline-date"><?php echo $status_date; ?> às <?php echo $status_time; ?></div>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-between">
                        <a href="perfil.php#historico-pedidos" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Voltar
                        </a>
                        <div>
                            <button class="btn btn-outline-secondary me-2" onclick="window.print()">
                                <i class="fas fa-print me-2"></i>Imprimir Recibo
                            </button>
                            <?php if (strtolower($pedido['status_pedidos']) == 'entregue'): ?>
                            <a href="repetir_pedido.php?id=<?php echo $pedido_id; ?>" class="btn btn-primary">
                                <i class="fas fa-redo me-2"></i>Pedir Novamente
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Exclusão de Conta</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Tem certeza de que deseja excluir sua conta permanentemente? Esta ação não pode ser desfeita.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger">Confirmar Exclusão</button> 
      </div>
    </div>
  </div>
</div>

<?php include_once "../rodape.html"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
