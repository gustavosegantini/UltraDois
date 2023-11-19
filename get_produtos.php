<?php
include '..\conexao.php'

// Buscar produtos
$sql = "SELECT * FROM produtos";
$result = $conn->query($sql);

$produtos = [];

if ($result->num_rows > 0) {
    // Colocar cada linha do resultado em um array associativo
    while($row = $result->fetch_assoc()) {
        $produtos[] = $row;
    }
} else {
    echo "0 resultados";
}

$conn->close();

// Retornar os dados em formato JSON
header('Content-Type: application/json');
echo json_encode($produtos);
?>
