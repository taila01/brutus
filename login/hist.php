<?php
// Iniciar a sessão
session_start();


// Verificar se o cliente está logado
if (!isset($_SESSION['id_logado'])) {
    header("Location: login.php"); // Redireciona para a tela de login se não estiver logado
    exit();
}

// Conexão com o banco de dados
$servername = "localhost";
$username = "root"; // Ajuste conforme seu ambiente
$password = "";     // Ajuste conforme seu ambiente
$dbname = "brutus";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// ID do cliente logado
$usuario_id = $_SESSION['id_logado'];

// Consulta para buscar o histórico de pedidos do cliente
$sql = "
    SELECT 
        p.cod_pedido, 
        p.datahora_pedido, 
        p.total_pedidos, 
        sp.status_pedidos
    FROM pedidos p
    INNER JOIN status_pedidos sp ON p.fk_Status_Pedidos_cod_status_pedidos = sp.cod_status_pedidos
    WHERE p.fk_Usuario_codigo = ?
    ORDER BY p.datahora_pedido DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Pedidos | BRUTUS</title>
    <link rel="icon" href="../img/favicon.svg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../geral.css">
</head>
<body>

<?php include_once "../cabecalho.html"; ?>

<div class="container my-5">
    <h2 class="text-center logo">Histórico de Pedidos</h2>
    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-primary text-center ">
                    <tr>
                        <th>Código do Pedido</th>
                        <th>Data e Hora</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="text-center">
                            <td><?php echo $row['cod_pedido']; ?></td>
                            <td><?php echo date("d/m/Y H:i:s", strtotime($row['datahora_pedido'])); ?></td>
                            <td class="text-success fw-bold">
                                R$ <?php echo number_format($row['total_pedidos'], 2, ',', '.'); ?>
                            </td>
                            <td><?php echo $row['status_pedidos']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center mt-4" role="alert">
            Você ainda não realizou nenhum pedido.
        </div>
    <?php endif; ?>

    <div class="text-center my-4">
        <a href="../index.php" class="btn btn-warning">
            <i class="fas fa-arrow-left"></i> Voltar para o Início
        </a>
    </div>
</div>

<?php include_once "../rodape.html"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
