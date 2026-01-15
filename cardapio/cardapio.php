<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hambúrguer | BRUTUS</title>

    <link rel="icon" href="../img/favicon.svg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../geral.css">
    <link rel="stylesheet" href="cardapio.css">

    <style>
        .comp {
            background-color: saddlebrown;
        }
    </style>
</head>

<body>
<?php
session_start();

include_once "conexao.php";
include_once "../cabecalho.html";
//mock
$categorias = [
    1 => "Hámburguer Artesanal",
    2 => "Kids",
    3 => "Combos",
    4 => "Acompanhamento",
    5 => "Bebidas",
    6 => "Sobremesa"
];
?>

<?php foreach ($categorias as $idCategoria => $titulo): ?>
<?php
$query = "SELECT cod_item, nome, descricao, preco, imagem 
          FROM itens 
          WHERE fk_Categoria_cod_categoria = :categoria";

$stmt = $conn->prepare($query);
$stmt->bindParam(":categoria", $idCategoria);
$stmt->execute();
?>

<div class="container my-5">
    <h2 class="text-center mb-4"><?= $titulo ?></h2>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-3">
        <?php while ($produto = $stmt->fetch()): ?>
        <div class="col">
            <div class="card">
                <img src="../produtos/<?= $produto['imagem'] ?>" class="card-img-top" alt="<?= $produto['nome'] ?>">

                <div class="card-body">
                    <h5 class="card-title"><?= $produto['nome'] ?></h5>
                    <p class="card-text">R$ <?= $produto['preco'] ?></p>
                    <p class="card-text"><?= $produto['descricao'] ?></p>

                    <a href="../carrinho/carrinho.php?id=<?= $produto['cod_item'] ?>&acao=add" class="bt_comprar">
                        <button class="comp btn w-100 comprar">Comprar</button>
                    </a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<?php include_once "../rodape.html"; ?>
</body>
</html>
