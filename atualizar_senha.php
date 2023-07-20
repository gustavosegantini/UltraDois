<?php
session_start();
include '../conexao.php';
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (empty($_POST['token']) || empty($_POST['senha']) || empty($_POST['confirmar_senha'])) {
    $_SESSION['senhaErro'] = "Por favor, preencha todos os campos.";
    header("Location: redefinir_senha.php?token=" . $_POST['token']);
    exit();
}

if ($_POST['senha'] !== $_POST['confirmar_senha']) {
    $_SESSION['senhaErro'] = "As senhas não correspondem.";
    header("Location: redefinir_senha.php?token=" . $_POST['token']);
    exit();
}

$token = mysqli_real_escape_string($conn, $_POST['token']);
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

// ...

$query = "SELECT * FROM perfil_cliente WHERE reset_token = '{$token}' AND reset_expiration >= UNIX_TIMESTAMP()";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) == 0) {
    $_SESSION['senhaErro'] = "O token de redefinição de senha é inválido ou expirou.";
    header("Location: redefinir_senha.php?token=" . $token);
    exit();
}

$row = mysqli_fetch_assoc($result);

// Atualizar a senha do usuário no banco de dados e remover o token de redefinição
$query = "UPDATE perfil_cliente SET senha = '{$senha}', reset_token = NULL, reset_expiration = NULL WHERE id = {$row['id']}";
$result = mysqli_query($conn, $query);
if (!$result) {
    die('Erro ao atualizar a senha: ' . mysqli_error($conn));
}

// Envie um e-mail de confirmação para o usuário após a alteração da senha
$mail = new PHPMailer;
$mail->isSMTP();
$mail->Host = 'smtp.hostinger.com';
$mail->SMTPAuth = true;
$mail->Username = 'contato@ultrafidelidade.com';
$mail->Password = '@Glesini1207';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

$mail->setFrom('contato@ultrafidelidade.com', 'UltraFidelidade');
$mail->addAddress($row['email']);

$mail->isHTML(true);
$mail->Subject = 'Senha alterada';
$mail->Body = "Olá, sua senha foi alterada com sucesso. Se você não solicitou essa alteração, entre em contato conosco imediatamente.";

if (!$mail->send()) {
    $_SESSION['resetError'] = "Ocorreu um erro ao enviar o e-mail de confirmação.";
    header("Location: redefinir_senha.php?token=" . $token);
    exit();
}

$_SESSION['senhaSucesso'] = "Senha redefinida com sucesso!";
header("Location: login.php");

exit();