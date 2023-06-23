<?php
require_once '../conexao.php';

session_start();

if (!isset($_SESSION['estabelecimento_id'])) {
    header("Location: login_estabelecimento.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo = bin2hex(random_bytes(5)); // Gera um código único

    try {
        $query = $conn->prepare("INSERT INTO Pontos (id_estabelecimento, codigo, pontos) VALUES (?, ?, 0)");
        $query->execute([$_SESSION['estabelecimento_id'], $codigo]);
        $msg = "Código gerado com sucesso: " . $codigo;
    } catch (PDOException $e) {
        $msg = "Erro ao gerar o código: " . $e->getMessage();
    }
}

try {
    $query = $conn->prepare("SELECT * FROM Pontos WHERE id_estabelecimento = ?");
    $query->execute([$_SESSION['estabelecimento_id']]);
    $codigos = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $msg = "Erro ao recuperar os códigos: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="dashboard_estabelecimento.css">
    <title>Dashboard do Estabelecimento</title>
</head>
<body>
    <div class="container">
        <h1>Bem-vindo, <?php echo $_SESSION['estabelecimento_nome']; ?>!</h1>
        <form action="" method="post">
            <input type="submit" value="Gerar Código">
        </form>
        <?php if(isset($msg)) echo $msg; ?>
        <h2>Seus Códigos:</h2>
        <ul>
            <?php foreach($codigos as $codigo) echo "<li>".$codigo['codigo']."</li>"; ?>
        </ul>
    </div>
</body>
</html>
