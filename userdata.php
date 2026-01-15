<?php
// Iniciar a sessão
session_start();

// Verificar se o cliente está logado
if (!isset($_SESSION['id_logado'])) {
    header("Location: login/login.php"); // Redireciona para a tela de login se não estiver logado
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

// Consulta para buscar os dados do usuário logado
$sql = "SELECT nome, cpf, telefone FROM usuario WHERE codigo = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

// Verificar se encontrou o usuário
if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
} else {
    die("Usuário não encontrado.");
}

// Função de logout
if (isset($_POST['logout'])) {
    session_destroy(); // Destroi a sessão
    header("Location: login/login.php"); // Redireciona para a página de login
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dados do Usuário | BRUTUS</title>
    <link rel="icon" href="../img/favicon.svg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../custom.css">
</head>
<body>

<?php include_once "cabecalho.html"; ?>

<div class="container my-5">
    <h2 class="text-center logo">Meus Dados</h2>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center bg-warning text-black">
                    Informações Pessoais
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <strong>Nome:</strong> <?php echo htmlspecialchars($usuario['nome']); ?>
                        </li>
                        <li class="list-group-item">
                            <strong>CPF:</strong> <?php echo htmlspecialchars($usuario['cpf']); ?>
                        </li>
                        <li class="list-group-item">
                            <strong>Celular:</strong> <?php echo htmlspecialchars($usuario['telefone']); ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center my-4">
        <a href="./index.php" class="btn btn-warning">
            <i class="fas fa-arrow-left"></i> Voltar para o Início
        </a>
        <a href="login\hist.php" class="btn btn-secondary">
            <i class="fas fa-history"></i> Histórico de Pedidos
        </a>
    </div>

    <!-- Botão de logout -->
    <div class="text-center my-4">
        <form method="POST">
            <button type="submit" name="logout" class="btn btn-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</div>

<?php include_once "rodape.html"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
