<?php
session_start();
// Conexão com o banco de dados
include '../conexao.php';

//Verificar o login

include 'session_verification.php';

verify_session('email', 'login.php');

// Restante do código

$email = $_SESSION['email'];

if (empty($_POST['codigo'])) {
    $_SESSION['codigoErro'] = "Por favor, preencha o campo do código.";
    header("Location: perfil.php");
    exit();
}

$codigo = mysqli_real_escape_string($conn, $_POST['codigo']);

// Verifica se o código existe na tabela "códigos" e ainda não foi usado
$query_codigo = "SELECT * FROM Codigos WHERE codigo = '$codigo' AND Utilizado = '0'";
$result_codigo = mysqli_query($conn, $query_codigo);
$row = mysqli_num_rows($result_codigo);

if ($row == 1) {
    // Código válido, atualiza a coluna "pontos" na tabela "perfil_cliente" com o valor atual mais 1
    $query = "UPDATE perfil_cliente SET pontos = pontos + 1, pontos_historico = pontos_historico + 1 WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    // Busca novamente o valor atualizado de pontos do usuário e obtém o curso_id
    $query = "SELECT pontos, id, curso FROM perfil_cliente WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $row = mysqli_fetch_assoc($result);
    $pontos = $row['pontos'];
    $id_cliente = $row['id'];
    $curso_id = $row['curso'];

    // // Consulta o nome do curso na tabela "cursos"
    // $query_curso = "SELECT nome_curso FROM cursos WHERE ID = ?";
    // $stmt_curso = mysqli_prepare($conn, $query_curso);
    // mysqli_stmt_bind_param($stmt_curso, "i", $curso_id);
    // mysqli_stmt_execute($stmt_curso);
    // $result_curso = mysqli_stmt_get_result($stmt_curso);
    // $row_curso = mysqli_fetch_assoc($result_curso);
    // $nome_curso = $row_curso['nome_curso'];

    // // Defina a quantidade de pontos necessários para cada curso
    // $pontos_necessarios = 6;
    // if ($nome_curso == 'Medicina') {
    //     $pontos_necessarios = 5;
    // }


    // Busca novamente o valor atualizado de pontos do usuário, bem como o valor da coluna "atletica"
    $query = "SELECT pontos, id, atletica FROM perfil_cliente WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $row = mysqli_fetch_assoc($result);
    $pontos = $row['pontos'];
    $id_cliente = $row['id'];
    $atletica = $row['atletica'];

    // Determina a quantidade de pontos necessários para gerar um cupom
    $pontos_necessarios = $atletica ? 5 : 6;




    // ...
    $data_utilizado = date('Y-m-d H:i:s');

    // Atualiza a coluna "usado" na tabela "codigos" para indicar que o código foi usado
    $update_codigo = "UPDATE Codigos SET Utilizado = ?, data_utilizado = ? WHERE codigo = ?";
    $stmt = mysqli_prepare($conn, $update_codigo);
    mysqli_stmt_bind_param($stmt, "sss", $email, $data_utilizado, $codigo);
    mysqli_stmt_execute($stmt);
    // ...

    // Verifica se o cliente acumulou pontos suficientes e gera um novo cupom
    if ($pontos >= $pontos_necessarios) {
        $cupom = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
        $sql = "INSERT INTO cupons (cupom, id_cliente) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $cupom, $id_cliente);
        mysqli_stmt_execute($stmt);

        // Atualiza a coluna "pontos" na tabela "perfil_cliente" para resetar os pontos do cliente
        $query = "UPDATE perfil_cliente SET pontos = 0 WHERE email = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $_SESSION['cupomGerado'] = true;
    } else {
        $_SESSION['cupomGerado'] = false;
    }

    $_SESSION['sucesso'] = "Pontos adicionados com sucesso!";
    header("Location: perfil.php");
    exit();
} else {
    $_SESSION['codigoErro'] = "Código inválido ou já utilizado.";
    header("Location: perfil.php");
    exit();
}
?>