<?php
require_once '../conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    try {
        $query = $conn->prepare("SELECT * FROM Estabelecimentos WHERE email = ?");
        $query->execute([$email]);
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($senha, $user['senha'])) {
            session_start();
            $_SESSION['estabelecimento_id'] = $user['id'];
            $_SESSION['estabelecimento_nome'] = $user['nome'];
            header("Location: dashboard_estabelecimento.php");
        } else {
            $msg = "Email ou senha inválidos!";
        }
    } catch (PDOException $e) {
        $msg = "Erro no login: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="login_estabelecimento.css">
    <title>Login Estabelecimento</title>
</head>

<body>
    <div class="container">
        <h1>Login Estabelecimento</h1>
        <form action="" method="post">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <input type="submit" value="Entrar">
        </form>
        <?php if (isset($msg))
            echo $msg; ?>
    </div>
</body>

</html>