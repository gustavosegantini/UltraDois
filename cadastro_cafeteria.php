<?php
session_start();
// Conexão com o banco de dados
include '../conexao.php';
include 'session_verification.php';

verify_session('email_cafeteria', 'login_cafeteria.php');

// Restante do código
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Funcionário</title>
    <link rel="stylesheet" href="cadastro_style.css">
</head>
<body>
    <h1>Cadastro de Funcionário</h1>
    <form action="cadastrar_cafeteria.php" method="post">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required>

        <label for="sobrenome">Sobrenome:</label>
        <input type="text" id="sobrenome" name="sobrenome" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>

        <label for="confirmar_senha">Confirmar Senha:</label>
        <input type="password" id="confirmar_senha" name="confirmar_senha" required>

        <input type="submit" value="Cadastrar">
    </form>
</body>
</html>
