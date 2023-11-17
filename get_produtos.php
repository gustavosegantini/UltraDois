<?php
// Conectar ao banco de dados
$host = 'localhost'; // ou seu host de banco de dados
$username = 'seu_usuario'; // seu usuário do banco de dados
$password = 'sua_senha'; // sua senha do banco de dados
$database = 'nome_do_banco_de_dados'; // nome do seu banco de dados

$conn = new mysqli($host, $username, $password, $database);

// Checar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

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
