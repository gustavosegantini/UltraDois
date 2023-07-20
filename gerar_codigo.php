<?php
session_start();
// Conexão com o banco de dados
include '../conexao.php';

//Verificar o login

include 'session_verification.php';

verify_session('email_cafeteria', 'login_cafeteria.php');

// Gera um código aleatório único
function generate_unique_code($conn) {
    $unique = false;
    while (!$unique) {
        $codigo_gerado = substr(str_shuffle("123456789ABCDEFGHIJKLMNPQRSTUVWXYZ"), 0, 5);
        $sql_check = "SELECT * FROM Codigos WHERE Codigo = '$codigo_gerado'";
        $result_check = mysqli_query($conn, $sql_check);
        if (mysqli_num_rows($result_check) == 0) {
            $unique = true;
        }
    }
    return $codigo_gerado;
}

$codigo_gerado = generate_unique_code($conn);
$email = $_SESSION['email_cafeteria']; // Obtém o email do usuário logado

// ...
$data_atual = date('Y-m-d H:i:s');

// Insere o código no banco de dados
$sql = "INSERT INTO Codigos (Codigo, Gerado, data_gerado) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sss", $codigo_gerado, $email, $data_atual);
// ...

if (mysqli_stmt_execute($stmt)) {
    echo $codigo_gerado;
} else {
    http_response_code(500);
    echo "Erro ao inserir código: " . mysqli_error($conn);
}

// Fecha a conexão com o banco de dados
mysqli_close($conn);
?>
