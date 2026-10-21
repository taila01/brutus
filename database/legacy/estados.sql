INSERT INTO status_pedidos (status_pedidos)
SELECT 'Pagamento Pendente'
WHERE NOT EXISTS (
    SELECT 1 FROM status_pedidos WHERE status_pedidos = 'Pagamento Pendente'
);

INSERT INTO status_pedidos (status_pedidos)
SELECT 'Preparando'
WHERE NOT EXISTS (
    SELECT 1 FROM status_pedidos WHERE status_pedidos = 'Preparando'
);

INSERT INTO status_pedidos (status_pedidos)
SELECT 'Em Rota de Entrega'
WHERE NOT EXISTS (
    SELECT 1 FROM status_pedidos WHERE status_pedidos = 'Em Rota de Entrega'
);

INSERT INTO status_pedidos (status_pedidos)
SELECT 'Entregue'
WHERE NOT EXISTS (
    SELECT 1 FROM status_pedidos WHERE status_pedidos = 'Entregue'
);
