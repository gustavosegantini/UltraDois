<?php
session_start();
// Conexão com o banco de dados

//Verificar o login

include 'session_verification.php';

verify_session('email', 'login.php');

// Restante do código

if (isset($_POST['email_atual']) && isset($_POST['novo_email'])) {
    include '../conexao.php';

    $email_atual = $_SESSION['email'];
    $novo_email = $_POST['novo_email'];

    $query = "UPDATE perfil_cliente SET email = '$novo_email' WHERE email = '$email_atual'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $_SESSION['email'] = $novo_email;
        $mensagem = "E-mail atualizado com sucesso!";
        $erro = false;
    } else {
        $mensagem = "Erro ao atualizar o e-mail!";
        $erro = true;
    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Editar E-mail</title>
    <link rel="stylesheet" type="text/css" href="cadastro_style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>

<body>
    <div class="container">
        <div class="modal">
            <header>
                <h1>Editar E-mail</h1>
            </header>
            <?php
            if (isset($mensagem)) {
                if ($erro) {
                    echo '<div class="alert error">' . $mensagem . '</div>';
                } else {
                    echo '<div class="alert success">' . $mensagem . '</div>';
                }
            }

            ?>
            <form action="editar_email.php" method="post">
                <label for="novo_email">Novo e-mail:</label>
                <input type="email" id="novo_email" name="novo_email" required>
                <input type="submit" value="Atualizar E-mail">
            </form><br>
            <a href="perfil.php">Voltar ao Perfil</a>
        </div>
    </div>
</body>

</html>