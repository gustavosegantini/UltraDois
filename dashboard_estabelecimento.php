<?php
session_start();
require_once '../conexao.php';

function gerarCodigo()
{
    $caracteres = '0123456789abcdefghijklmnopqrstuvwxyz';
    $codigo = '';
    for ($i = 0; $i < 6; $i++) {
        $codigo .= $caracteres[rand(0, strlen($caracteres) - 1)];
    }
    return $codigo;
}

function codigoUnico(PDO $conn)
{
    while (true) {
        $codigo = gerarCodigo();
        $query = $conn->prepare("SELECT * FROM Pontos WHERE codigo = ?");
        $query->execute([$codigo]);
        if ($query->rowCount() == 0) {
            return strtoupper($codigo);
        }
    }
}

// Gerando o código
if (isset($_POST['gerar_codigo'])) {
    $codigo = codigoUnico($conn);
    $query = $conn->prepare("INSERT INTO Pontos (id_estabelecimento, codigo, pontos) VALUES (?, ?, 0)");
    $query->execute([$_SESSION['estabelecimento_id'], $codigo]);
}

// Buscando os códigos
$query = $conn->prepare("SELECT * FROM Pontos WHERE id_estabelecimento = ?");
$query->execute([$_SESSION['estabelecimento_id']]);
$codigos = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Dashboard do Estabelecimento</title>
    <link rel="stylesheet" href="dashboard_estabelecimento.css">
</head>

<body>
    <h1>Dashboard do Estabelecimento</h1>
    <form action="dashboard_estabelecimento.php" method="post">
        <button type="submit" name="gerar_codigo">Gerar Código</button>
    </form>
    <h2>Códigos gerados:</h2>
    <ul>
        <?php foreach ($codigos as $codigo): ?>
            <li>
                <?php echo $codigo['codigo']; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</body>

</html>