<?php
session_start();
include '../conexao.php';

if(empty($_POST['email']) || empty($_POST['senha'])) {
    $_SESSION['loginErro'] = "Por favor, preencha todos os campos.";
    header("Location: login_cafeteria.php");
    exit();
}

$email = mysqli_real_escape_string($conn, $_POST['email']);
$senha = mysqli_real_escape_string($conn, $_POST['senha']);

$query = "SELECT * FROM perfil_cafeteria WHERE email = '{$email}'";
$result = mysqli_query($conn, $query);
if (!$result) {
    die('Erro na consulta: ' . mysqli_error($conn));
}
$row = mysqli_fetch_assoc($result);

if(password_verify($senha, $row['senha'])) {
    $_SESSION['email_cafeteria'] = $email;
    header("Location: perfil_cafeteria.php");
    exit();
} else {
    $_SESSION['loginErro'] = "Email ou senha inválidos.";
    header("Location: login_cafeteria.php");
    exit();
}


?>
