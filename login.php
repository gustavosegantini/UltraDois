<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="cadastro_style.css">
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('senha');
            const toggleButton = document.getElementById('toggle-password');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.innerText = 'Ocultar';
            } else {
                passwordInput.type = 'password';
                toggleButton.innerText = 'Mostrar';
            }
        }
    </script>
</head>

<body>
    <div class="container">
        <div class="modal">
            <header>
                <h1>UltraFidelidade</h1>
            </header>
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

            <form method="POST" action="autenticar_login.php">
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" required>

                <label for="senha">Senha:</label>
                <div class="password-container">
                    <input type="password" id="senha" name="senha" required>
                    <button type="button" id="toggle-password" class="show-password-button"
                        onclick="togglePasswordVisibility()">Mostrar</button>
                </div>

                <input type="submit" value="Entrar">
            </form><br>
            <p>Ainda não possui uma conta? <a href="cadastro.php">Cadastre-se</a></p><br>
            <p>Esqueceu sua senha? <a href="esqueci_senha.php">Clique Aqui</a></p>

        </div>
    </div>
    <footer>
        <div class="credit-banner">
            <p>Criado por Gustavo Segantini</p>
        </div>
    </footer>
</body>

</html>