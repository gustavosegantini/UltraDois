<?php
session_start();
// Conexão com o banco de dados
include '../conexao.php';

//Verificar o login

include 'session_verification.php';

verify_session('email_cafeteria', 'login_cafeteria.php');

// Restante do código


if (!isset($_GET['email_cliente'])) {
    header("Location: perfil_cafeteria.php");
    exit();
}


$email_cliente = $_GET['email_cliente'];

$sql = "DELETE FROM perfil_cliente WHERE email = '{$email_cliente}'";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die('Erro ao deletar cliente: ' . mysqli_error($conn));
}

header("Location: perfil_cafeteria.php");
exit();
?>
