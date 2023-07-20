<?php session_start(); // Adicione esta linha para iniciar a sessão ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login da Cafeteria</title>
    <link rel="stylesheet" href="login_style.css">
</head>
<body>
    <h1>Login da Cafeteria</h1>
    <?php
            if (isset($_SESSION['loginErro'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['loginErro'] . '</div>';
                unset($_SESSION['loginErro']);
            }

            if (isset($_SESSION['logoutSucesso'])) {
                echo '<div class="alert alert-success">' . $_SESSION['logoutSucesso'] . '</div>';
                unset($_SESSION['logoutSucesso']);
            }

            if (isset($_SESSION['loginInvalido'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['loginInvalido'] . '</div>';
                unset($_SESSION['loginInvalido']);
            }
        ?>
    <div class="container">
        <form action="autenticar_login_cafeteria.php" method="post">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>

            <input type="submit" value="Entrar">
        </form><br>
        <p>Ainda não possui uma conta? <a href="cadastro_cafeteria.php">Cadastre-se</a></p>
    <div>
</body>
</html>
