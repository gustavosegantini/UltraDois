<?php
session_start();
// Conexão com o banco de dados

//Verificar o login

include 'session_verification.php';

verify_session('email', 'login.php');

// Restante do código

if (isset($_POST['senha_atual']) && isset($_POST['nova_senha'])) {
    include '../conexao.php';

    $email = $_SESSION['email'];
    $senha_atual = $_POST['senha_atual'];
    $nova_senha = $_POST['nova_senha'];

    $query = "SELECT senha FROM perfil_cliente WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    if (password_verify($senha_atual, $row['senha'])) {
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        $query = "UPDATE perfil_cliente SET senha = '$senha_hash' WHERE email = '$email'";
        $result = mysqli_query($conn, $query);

        if ($result) {
            $mensagem = "Senha atualizada com sucesso!";
            $erro = false;
        } else {
            $mensagem = "Erro ao atualizar a senha!";
            $erro = true;
        }
    } else {
        $mensagem = "Senha atual incorreta!";
        $erro = true;
    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Criar Nova Senha</title>
    <link rel="stylesheet" type="text/css" href="cadastro_style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>

<body>
    <div class="container">
        <div class="modal">
            <header>
                <h1>Criar Nova Senha</h1> <br>
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
            <form action="criar_nova_senha.php" method="post">
                <label for="senha_atual">Senha atual:</label>
                <input type="password" id="senha_atual" name="senha_atual" required>
                <label for="nova_senha">Nova senha:</label>
                <input type="password" id="nova_senha" name="nova_senha" required>
                <input type="submit" value="Atualizar Senha">
            </form><br>
            <a href="perfil.php">Voltar ao Perfil</a>


        </div>
    </div>
</body>

</html>