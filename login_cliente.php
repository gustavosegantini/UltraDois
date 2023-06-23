<?php
require_once '../conexao.php';

if (isset($_POST['email'], $_POST['senha'])) {
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $query = $conn->prepare("SELECT * FROM Clientes WHERE email = ?");
    $query->execute([$email]);
    $cliente = $query->fetch(PDO::FETCH_ASSOC);
    if ($cliente && password_verify($senha, $cliente['senha'])) {
        session_start();
        $_SESSION['cliente_id'] = $cliente['id'];
        header('Location: dashboard_cliente.php');
    } else {
        $erro = "Email ou senha incorretos";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Login de Cliente</title>
    <link rel="stylesheet" href="login_cliente.css">
</head>

<body>
    <h1>Login de Cliente</h1>
    <?php if (isset($erro))
        echo "<p>$erro</p>"; ?>
    <form action="login_cliente.php" method="post">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>

        <button type="submit">Login</button>
    </form>
</body>

</html>