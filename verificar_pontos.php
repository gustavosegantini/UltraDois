<?php
include '../conexao.php';

function gerarCupom() {
    return substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
}

$email_cliente = $_POST['email_cliente'];
$pontos_cliente = $_POST['pontos_cliente'];

// Verificar se o cliente já possui um cupom ou se ainda não atingiu 5 pontos
$query = "SELECT pontos, cupom FROM perfil_cliente WHERE email = '$email_cliente'";
$result = mysqli_query($conn, $query);

if (!$result) {
    die('Erro na consulta: ' . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($result);

if ($row['cupom'] || $pontos_cliente < 5) {
    exit();
}

// Gera um novo cupom e associa ao cliente
$cupom = gerarCupom();
$update_query = "UPDATE perfil_cliente SET cupom = '$cupom' WHERE email = '$email_cliente'";

if (mysqli_query($conn, $update_query)) {
    echo "Cupom gerado: $cupom";
} else {
    echo "Erro ao gerar cupom: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
