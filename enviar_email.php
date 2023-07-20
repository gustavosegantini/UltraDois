<?php
session_start();
include '../conexao.php';
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


ini_set('display_errors', 1);
error_reporting(E_ALL);

if (empty($_POST['email'])) {
    $_SESSION['emailErro'] = "Por favor, preencha o campo de e-mail.";
    header("Location: esqueci_senha.php");
    exit();
}

$email = mysqli_real_escape_string($conn, $_POST['email']);

$query = "SELECT * FROM perfil_cliente WHERE email = '{$email}'";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) == 0) {
    $_SESSION['emailErro'] = "E-mail não encontrado.";
    header("Location: esqueci_senha.php");
    exit();
}

$token = bin2hex(random_bytes(32)); // Gere um token de 64 caracteres
$expiration = new DateTime();
$expiration->modify('+1 hour'); // Token válido por 1 hora
$expiration_timestamp = $expiration->getTimestamp();

// Armazene o token e a data de expiração no banco de dados
$query = "UPDATE perfil_cliente SET reset_token = '{$token}', reset_expiration = {$expiration_timestamp} WHERE email = '{$email}'";
$result = mysqli_query($conn, $query);
if (!$result) {
    die('Erro ao atualizar o token de redefinição: ' . mysqli_error($conn));
}

// ...

// Envie um e-mail com o link de redefinição (substitua com seu próprio e-mail e configure seu servidor SMTP)
$reset_link = "http://ultrafidelidade.com/redefinir_senha.php?token=" . $token;

// Incluir a biblioteca PHPMailer para enviar e-mails
// require_once 'PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer;
$mail->isSMTP();
$mail->Host = 'smtp.hostinger.com';
$mail->SMTPAuth = true;
$mail->Username = 'contato@ultrafidelidade.com';
$mail->Password = '@Glesini1207';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

$mail->setFrom('contato@ultrafidelidade.com', 'Suporte UltraFidelidade');
$mail->addAddress($email);

$mail->isHTML(true);
$mail->Subject = 'Recuperacao de senha';
$mail->Body = "Clique no link a seguir para redefinir sua senha: <a href='{$reset_link}'>{$reset_link}</a>";

if (!$mail->send()) {
    $_SESSION['emailErro'] = "Ocorreu um erro ao enviar o e-mail. Por favor, tente novamente.";
    header("Location: esqueci_senha.php");
    exit();
}

$_SESSION['emailSucesso'] = "Um e-mail foi enviado com instruções para redefinir sua senha.";
header("Location: esqueci_senha.php");
exit();