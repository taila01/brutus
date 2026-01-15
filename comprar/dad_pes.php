<?php
    session_start(); // abre a sessão
    include_once "conecta.php"; 
    $cliente= $_SESSION['id_logado'];

    if ( isset($_POST["btn_dados"]) )
    {
        $nome= $_POST['nome'];
        $cpf= $_POST['cpf'];
        $email= $_POST['email'];

        $cpf = str_replace("." , "" , $cpf );
        $cpf = str_replace("-" , "" , $cpf );

        $sqldados = "UPDATE usuario
                      SET NOME='$nome', CPF='$cpf'
                      WHERE CODIGO=$cliente";
                    
        mysqli_query($conn, $sqldados)or die( mysqli_error($conn) );
    

    $sqldado = "UPDATE usuario
                      SET email ='$email'
                      WHERE CODIGO=$cliente";
                    
        mysqli_query($conn, $sqldado)or die( mysqli_error($conn) );
    }
?>

<?php header("Location: endereco.php"); ?>
