<?php
include '../conexao.php';

$query_total_usuarios = "SELECT COUNT(*) AS total_usuarios FROM perfil_cliente";
$result_total_usuarios = mysqli_query($conn, $query_total_usuarios);
$row_total_usuarios = mysqli_fetch_assoc($result_total_usuarios);
$total_usuarios = $row_total_usuarios['total_usuarios'];

$query_total_cupons = "SELECT COUNT(*) AS total_cupons FROM cupons";
$result_total_cupons = mysqli_query($conn, $query_total_cupons);
$row_total_cupons = mysqli_fetch_assoc($result_total_cupons);
$total_cupons = $row_total_cupons['total_cupons'];

$query_total_cupons_utilizados = "SELECT COUNT(*) AS total_cupons_utilizados FROM cupons WHERE utilizado = 1";
$result_total_cupons_utilizados = mysqli_query($conn, $query_total_cupons_utilizados);
$row_total_cupons_utilizados = mysqli_fetch_assoc($result_total_cupons_utilizados);
$total_cupons_utilizados = $row_total_cupons_utilizados['total_cupons_utilizados'];

echo json_encode(
    array(
        'total_usuarios' => $total_usuarios,
        'total_cupons' => $total_cupons,
        'total_cupons_utilizados' => $total_cupons_utilizados
    )
);
?>