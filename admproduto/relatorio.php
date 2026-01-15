<?php
session_start();

include_once "conexao.php";

$categorias = [];
$cat_sql = "SELECT cod_categoria, nome FROM categoria";
$cat_result = $conn->query($cat_sql);
if ($cat_result->num_rows > 0) {
    while ($row = $cat_result->fetch_assoc()) {
        $categorias[] = $row;
    }
}

$filtro_categoria = isset($_GET['categoria']) ? intval($_GET['categoria']) : 0;
$data_inicial = isset($_GET['data_inicial']) ? $_GET['data_inicial'] : '';
$data_final = isset($_GET['data_final']) ? $_GET['data_final'] : '';

$sql = "SELECT 
            i.nome AS nome_produto,
            REPLACE(i.preco, ',', '.') AS preco_unitario,
            SUM(ip.quantidade) AS total_vendido
        FROM 
            itens_pedido AS ip
        JOIN 
            itens AS i ON ip.cod_item = i.cod_item
        JOIN
            pedidos AS p ON ip.codigo_pedido = p.cod_pedido
        ";

$condicoes = [];

if ($filtro_categoria > 0) {
    $condicoes[] = "i.fk_Categoria_cod_categoria = $filtro_categoria";
}
if (!empty($data_inicial) && !empty($data_final)) {
    $condicoes[] = "DATE(p.datahora_pedido) BETWEEN '$data_inicial' AND '$data_final'";
} elseif (!empty($data_inicial)) {
    $condicoes[] = "DATE(p.datahora_pedido) >= '$data_inicial'";
} elseif (!empty($data_final)) {
    $condicoes[] = "DATE(p.datahora_pedido) <= '$data_final'";
}

if (count($condicoes) > 0) {
    $sql .= " WHERE " . implode(' AND ', $condicoes);
}
$sql .= " GROUP BY i.nome, i.preco
          ORDER BY total_vendido DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Produtos Mais Vendidos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: white;
            margin: 20px;
        }

        h2 {
            color: rgb(211, 108, 35);
            text-align: center;
        }

        form {
            text-align: center;
            margin-bottom: 20px;
        }

        select, input[type="submit"], input[type="date"], form button {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin: 5px;
            background-color: white;
            color: rgb(211, 108, 35);
            cursor: pointer;
            transition: 0.3s;
        }

        input[type="submit"]:hover, form button:hover {
            background-color: rgb(211, 108, 35);
            color: white;
        }

        table {
            width: 90%;
            margin: 0 auto;
            background: white;
            border-collapse: collapse;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: rgb(211, 108, 35);
            color: white;
        }

        button#print {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: rgb(211, 108, 35);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button#print:hover {
            background-color: saddlebrown;
        }
    </style>
</head>
<body>
<h2>Relatório de Produtos Mais Vendidos</h2>

<form method="GET">
    <label for="categoria">Categoria:</label>
    <select name="categoria" id="categoria">
        <option value="0">Todas</option>
        <?php foreach($categorias as $cat): ?>
            <option value="<?= $cat['cod_categoria'] ?>" <?= ($cat['cod_categoria'] == $filtro_categoria) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['nome']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>De:</label>
    <input type="date" name="data_inicial" value="<?= htmlspecialchars($data_inicial) ?>">
    <label>Até:</label>
    <input type="date" name="data_final" value="<?= htmlspecialchars($data_final) ?>">

    <input type="submit" value="Filtrar">

    <button type="button" onclick="window.location.href='painel.php'">
    Volta admin
    </button>

</form>

<table>
    <tr>
        <th>Produto</th>
        <th>Quantidade Vendida</th>
        <th>Preço Unitário (R$)</th>
        <th>Valor Total (R$)</th>
    </tr>

    <?php
    if ($result->num_rows > 0) {
        mysqli_data_seek($result, 0);
        while($row = $result->fetch_assoc()) {
            $preco_unitario = floatval($row['preco_unitario']);
            $total_vendido = intval($row['total_vendido']);
            $valor_total = number_format($preco_unitario * $total_vendido, 2, ',', '.');
            $preco_formatado = number_format($preco_unitario, 2, ',', '.');

            echo "<tr>
                    <td>{$row['nome_produto']}</td>
                    <td>{$total_vendido}</td>
                    <td>R$ {$preco_formatado}</td>
                    <td>R$ {$valor_total}</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='4'>Nenhum registro encontrado.</td></tr>";
    }
    ?>
</table>

<br>
<button id="print" onclick="window.print()">Imprimir / Salvar em PDF</button>

<canvas id="grafico" width="800" height="400"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('grafico').getContext('2d');
    const grafico = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [
                <?php
                mysqli_data_seek($result, 0);
                while($row = $result->fetch_assoc()) {
                    echo "'{$row['nome_produto']}',";
                }
                ?>
            ],
            datasets: [{
                label: 'Quantidade Vendida',
                data: [
                    <?php
                    mysqli_data_seek($result, 0);
                    while($row = $result->fetch_assoc()) {
                        echo "{$row['total_vendido']},";
                    }
                    ?>
                ],
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
</body>
</html>
