<?php
require_once '../conexao.php';

if (isset($_POST['nome'], $_POST['email'], $_POST['senha'])) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $query = $conn->prepare("INSERT INTO Clientes (nome, email, senha) VALUES (?, ?, ?)");
    $query->execute([$nome, $email, $senha]);
    header('Location: login_cliente.php');
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Cliente</title>
    <link rel="stylesheet" href="cadastro_estabelecimento.css">
</head>
<body>
    <h1>Cadastro de Cliente</h1>
    <form action="cadastro_cliente.php" method="post">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>

        <button type="submit">Cadastrar</button>
    </form>
</body>
</html>
