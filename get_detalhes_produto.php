<?php
// Conexão com o banco de dados
// $conn = ...

include '../conexao.php';

$nomeProduto = $_GET['nome'] ?? '';

$sql = "SELECT ID, tamanho, preco FROM produtos WHERE nome = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $nomeProduto);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$detalhes = array();

while ($row = mysqli_fetch_assoc($result)) {
    $detalhe = array(
        "ID" => $row['ID'],
        "tamanho" => $row['tamanho'],
        "preco" => $row['preco']
    );
    array_push($detalhes, $detalhe);
}

mysqli_stmt_close($stmt);

header('Content-Type: application/json');
echo json_encode($detalhes);
?>
