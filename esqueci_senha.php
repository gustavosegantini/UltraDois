<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esqueci a senha</title>
    <link rel="stylesheet" href="login_style.css">
</head>

<body>
    <div class="container">
        <div class="modal">

            <h1>Esqueci a senha</h1>

            <?php
            if (isset($_SESSION['emailErro'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['emailErro'] . '</div>';
                unset($_SESSION['emailErro']);
            }

            if (isset($_SESSION['emailSucesso'])) {
                echo '<div class="alert alert-success">' . $_SESSION['emailSucesso'] . '</div>';
                unset($_SESSION['emailSucesso']);
            }
            ?>

            <form method="POST" action="enviar_email.php">
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" required>

                <input type="submit" value="Enviar">
            </form><br>
            <p><a href="login.php">Voltar ao Login</a></p>

        </div>
    </div>
</body>
<footer>
    <div class="credit-banner">
        <p>Criado por Gustavo Segantini</p>
    </div>
</footer>

</html>