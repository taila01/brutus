<?php
session_start();

include_once "conexao.php";

$mensagem_sucesso = '';
$erros = [];

try {
    $conn = new PDO("mysql:host=$host;dbname=" . $database, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if (!isset($_SESSION['id_logado']) || empty($_SESSION['id_logado'])) {
        header("Location: login.php");
        exit;
    }
    
    $usuario_id = $_SESSION['id_logado'];
    
    $endereco_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $modo_edicao = ($endereco_id > 0);
    
    $identificacao = "";
    $cep = "";
    $rua = "";
    $numero = "";
    $complemento = "";
    $bairro = "";
    $cidade = "";
    $estado = "";
    $referencia = "";
    $telefone_contato = "";
    $principal = 0;
    
    if ($modo_edicao) {
        $query = "SELECT * FROM endereco WHERE cod_endereco = :id AND fk_Usuario_codigo  = :usuario_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $endereco_id, PDO::PARAM_INT);
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $endereco = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $identificacao = $endereco['identificacao'];
            $cep = $endereco['cep'];
            $rua = $endereco['rua'];
            $numero = $endereco['numero'];
            $complemento = $endereco['complemento'];
            $bairro = $endereco['bairro'];
        } else {
            $erros[] = "Endereço não encontrado.";
            $modo_edicao = false;
        }
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $identificacao = isset($_POST['identificacao']) ? trim($_POST['identificacao']) : '';
        $cep = isset($_POST['cep']) ? trim($_POST['cep']) : '';
        $rua = isset($_POST['rua']) ? trim($_POST['rua']) : '';
        $numero = isset($_POST['numero']) ? trim($_POST['numero']) : '';
        $complemento = isset($_POST['complemento']) ? trim($_POST['complemento']) : '';
        $bairro = isset($_POST['bairro']) ? trim($_POST['bairro']) : '';
        $cidade = isset($_POST['cidade']) ? trim($_POST['cidade']) : '';
        $estado = isset($_POST['estado']) ? trim($_POST['estado']) : '';
        $referencia = isset($_POST['referencia']) ? trim($_POST['referencia']) : '';
        $telefone_contato = isset($_POST['telefone_contato']) ? trim($_POST['telefone_contato']) : '';
        $principal = isset($_POST['principal']) ? 1 : 0;
        
        if (empty($identificacao)) {
            $erros[] = "A identificação do endereço é obrigatória.";
        }
        
        if (empty($cep)) {
            $erros[] = "O CEP é obrigatório.";
        }
        
        if (empty($rua)) {
            $erros[] = "A rua é obrigatória.";
        }
        
        if (empty($numero)) {
            $erros[] = "O número é obrigatório.";
        }
        
        if (empty($bairro)) {
            $erros[] = "O bairro é obrigatório.";
        }
        
        if (empty($erros)) {
            try {
                if ($modo_edicao) {
                    $query_update = "UPDATE endereco SET 
                        identificacao = :identificacao,
                        cep = :cep,
                        rua = :rua,
                        numero = :numero,
                        complemento = :complemento,
                        bairro = :bairro
                        WHERE cod_endereco = :id AND fk_Usuario_codigo  = :usuario_id";
                    
                    $stmt_update = $conn->prepare($query_update);
                    $stmt_update->bindParam(':id', $endereco_id, PDO::PARAM_INT);
                } else {
                    $query_update = "INSERT INTO endereco (
                        fk_Usuario_codigo,
                        identificacao,
                        cep,
                        rua,
                        numero,
                        complemento,
                        bairro
                    ) VALUES (
                        :usuario_id,
                        :identificacao,
                        :cep,
                        :rua,
                        :numero,
                        :complemento,
                        :bairro
                    )";
                    
                    $stmt_update = $conn->prepare($query_update);
                }
                
                $stmt_update->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                $stmt_update->bindParam(':identificacao', $identificacao);
                $stmt_update->bindParam(':cep', $cep);
                $stmt_update->bindParam(':rua', $rua);
                $stmt_update->bindParam(':numero', $numero);
                $stmt_update->bindParam(':complemento', $complemento);
                $stmt_update->bindParam(':bairro', $bairro);
                
                if ($stmt_update->execute()) {
                    $mensagem_sucesso = $modo_edicao ? "Endereço atualizado com sucesso!" : "Endereço cadastrado com sucesso!";
                    
                    header("Location: perfil.php#meus-enderecos");
                    exit;
                } else {
                    $erros[] = "Erro ao salvar o endereço. Tente novamente.";
                }
            } catch (PDOException $e) {
                $erros[] = "Erro no banco de dados: " . $e->getMessage();
            }
        }
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
    <title><?php echo $modo_edicao ? 'Editar' : 'Cadastrar'; ?> Endereço | BRUTUS</title>
    <link rel="icon" href="img/favicon.svg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../geral.css"> 
    <link rel="stylesheet" href="perfil.css"> 
</head>
<body>

<?php include_once "../cabecalho.html"; ?>

<div class="container my-3">
    <div class="row">
        <div class="col-md-3">
            <div class="list-group profile-menu">
                <a href="perfil.php#editar-dados" class="list-group-item list-group-item-action"><i class="fas fa-user-edit me-2"></i>Editar Dados</a>
                <a href="perfil.php#meus-enderecos" class="list-group-item list-group-item-action active"><i class="fas fa-map-marker-alt me-2"></i>Meus Endereços</a>
                <a href="perfil.php#historico-pedidos" class="list-group-item list-group-item-action"><i class="fas fa-history me-2"></i>Histórico de Pedidos</a>
                <a href="logout.php" class="list-group-item list-group-item-action"><i class="fas fa-sign-out-alt me-2"></i>Sair</a>
                <a href="#" class="list-group-item list-group-item-action text-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"><i class="fas fa-trash-alt me-2"></i>Excluir Conta</a>
            </div>
        </div>

        <div class="col-md-9">
            <div class="address-form-container">
                <h2 class="mb-4 profile-title">
                    <i class="fas <?php echo $modo_edicao ? 'fa-edit' : 'fa-plus-circle'; ?> me-2"></i>
                    <?php echo $modo_edicao ? 'Editar' : 'Cadastrar Novo'; ?> Endereço
                </h2>
                
                <?php if (!empty($mensagem_sucesso)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?php echo $mensagem_sucesso; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($erros)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            <?php foreach ($erros as $erro): ?>
                                <li><?php echo $erro; ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="identificacao" class="form-label">Identificação do Endereço*</label>
                            <input type="text" class="form-control" id="identificacao" name="identificacao" value="<?php echo htmlspecialchars($identificacao); ?>" placeholder="Ex: Casa, Trabalho, Casa dos Pais" required>
                        </div>
                        <div class="col-md-6">
                            <label for="cep" class="form-label">CEP*</label>
                            <input type="text" class="form-control" id="cep" name="cep" value="<?php echo htmlspecialchars($cep); ?>" placeholder="00000-000" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-9">
                            <label for="rua" class="form-label">Rua/Avenida*</label>
                            <input type="text" class="form-control" id="rua" name="rua" value="<?php echo htmlspecialchars($rua); ?>" placeholder="Nome da rua ou avenida" required>
                        </div>
                        <div class="col-md-3">
                            <label for="numero" class="form-label">Número*</label>
                            <input type="text" class="form-control" id="numero" name="numero" value="<?php echo htmlspecialchars($numero); ?>" placeholder="Nº" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="complemento" class="form-label">Complemento</label>
                            <input type="text" class="form-control" id="complemento" name="complemento" value="<?php echo htmlspecialchars($complemento); ?>" placeholder="Apto, Bloco, Casa, etc.">
                        </div>
                        <div class="col-md-6">
                            <label for="bairro" class="form-label">Bairro*</label>
                            <input type="text" class="form-control" id="bairro" name="bairro" value="<?php echo htmlspecialchars($bairro); ?>" placeholder="Nome do bairro" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <a href="perfil.php#meus-enderecos" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn profile-btn"><?php echo $modo_edicao ? 'Atualizar' : 'Salvar'; ?> Endereço</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once "../rodape.html"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('cep').addEventListener('blur', function() {
        const cep = this.value.replace(/\D/g, '');
        
        if (cep.length !== 8) {
            return;
        }
        
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(response => response.json())
            .then(data => {
                if (!data.erro) {
                    document.getElementById('rua').value = data.logradouro;
                    document.getElementById('bairro').value = data.bairro;
                    document.getElementById('cidade').value = data.localidade;
                    document.getElementById('estado').value = data.uf;
                    document.getElementById('numero').focus();
                }
            })
            .catch(error => console.error('Erro ao buscar CEP:', error));
    });
    
    document.getElementById('cep').addEventListener('input', function() {
        let cep = this.value.replace(/\D/g, '');
        cep = cep.slice(0, 8);
        
        if (cep.length > 5) {
            cep = cep.slice(0, 5) + '-' + cep.slice(5);
        }
        
        this.value = cep;
    });
    
    document.getElementById('telefone_contato').addEventListener('input', function() {
        let telefone = this.value.replace(/\D/g, '');
        telefone = telefone.slice(0, 11);
        
        if (telefone.length > 2) {
            telefone = '(' + telefone.slice(0, 2) + ')' + (telefone.length > 2 ? ' ' + telefone.slice(2) : '');
        }
        
        if (telefone.length > 10) {
            telefone = telefone.slice(0, 10) + '-' + telefone.slice(10);
        }
        
        this.value = telefone;
    });
    
    window.addEventListener('DOMContentLoaded', (event) => {
        const alerts = document.querySelectorAll('.alert');
        
        alerts.forEach(alert => {
            setTimeout(() => {
                const closeButton = alert.querySelector('.btn-close');
                if (closeButton) {
                    closeButton.click();
                }
            }, 5000);
        });
    });
</script>
</body>
</html>
