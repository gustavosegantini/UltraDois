<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir senha</title>
    <link rel="stylesheet" href="cadastro_style.css">
</head>

<body>
    <div class="container">
        <div class="modal">
            <header>
                <h1>Redefinir senha</h1>
            </header>
            <form method="POST" action="atualizar_senha.php">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
                <label for="senha">Nova senha:</label>
                <input type="password" id="senha" name="senha" required>
                <label for="confirmar_senha">Confirmar nova senha:</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" required>
                <input type="submit" value="Redefinir senha">
            </form>
        </div>
    </div>
</body>

</html>