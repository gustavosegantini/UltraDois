<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
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

                <div class="input-wrapper">
                    <label for="email">E-mail:</label>
                    <input type="email" id="email" name="email" placeholder="exemple@mail.com" required>
                </div>

                <div class="input-wrapper">
                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" placeholder="Sua Senha" required>
                    <i class="far fa-eye password-icon" onclick="togglePasswordVisibility('senha')"></i>
                </div>

                <input type="submit" value="Entrar">
            </form><br>
            <p>Ainda não possui uma conta? <a href="cadastro.php">Cadastre-se</a></p><br>
            <p>Esqueceu sua senha? <a href="esqueci_senha.php">Clique Aqui</a></p><br>

        </div>
    </div>
    <footer>
        <div class="credit-banner">
            <p>Criado por Gustavo Segantini</p>
        </div>
    </footer>
</body>
<script>
    function togglePasswordVisibility(inputId) {
        const input = document.getElementById(inputId);
        const icon = input.nextElementSibling;

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>



</html>