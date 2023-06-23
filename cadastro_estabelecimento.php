<?php
require_once '../conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    try {
        $query = $conn->prepare("INSERT INTO Estabelecimentos (nome, email, senha) VALUES (?, ?, ?)");
        $query->execute([$nome, $email, $senha]);
        $msg = "Cadastro realizado com sucesso!";
    } catch (PDOException $e) {
        $msg = "Erro no cadastro: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="cadastro_estabelecimento.css">
    <title>Cadastro de Estabelecimento</title>
</head>

<body>
    <div class="container">
        <h1>Cadastro de Estabelecimento</h1>
        <form action="" method="post">
            <input type="text" name="nome" placeholder="Nome do Estabelecimento" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <input type="submit" value="Cadastrar">
        </form>
        <?php if (isset($msg))
            echo $msg; ?>
    </div>
</body>

</html>