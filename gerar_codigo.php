<?php
session_start();
include '../conexao.php';

include 'session_verification.php';

verify_session('email_cafeteria', 'login_cafeteria.php');

function generate_unique_code($conn)
{
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

$id_produto = $_POST['id_produto']; // Obtém o ID do produto
$codigo_gerado = generate_unique_code($conn);
$email = $_SESSION['email_cafeteria'];

$data_atual = date('Y-m-d H:i:s');

$sql = "INSERT INTO Codigos (Codigo, ID_Produto, Gerado, data_gerado) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "siss", $codigo_gerado, $id_produto, $email, $data_atual);

if (mysqli_stmt_execute($stmt)) {
    echo $codigo_gerado;
} else {
    http_response_code(500);
    echo "Erro ao inserir código: " . mysqli_error($conn);
}

mysqli_close($conn);
?>