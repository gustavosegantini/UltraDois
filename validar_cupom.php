<?php
include '../conexao.php';

if (isset($_POST['cupom'])) {
    $cupom = $_POST['cupom'];

    // Consulta para verificar se o cupom existe e ainda não foi utilizado
    $query = "SELECT * FROM cupons WHERE cupom = '$cupom' AND utilizado = 0";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // Cupom existe e ainda não foi utilizado
        $row = mysqli_fetch_assoc($result);
        $email_cliente = $row['email_cliente'];

        // Atualiza o cupom como utilizado e registra a data de utilização
        $query_update_cupom = "UPDATE cupons SET utilizado = 1, data_utilizado = CURDATE() WHERE cupom = '$cupom'";
        $result_update_cupom = mysqli_query($conn, $query_update_cupom);

        if (!$result_update_cupom) {
            echo 'Erro ao atualizar o cupom: ' . mysqli_error($conn);
            exit();
        }

        // Atualiza os pontos do cliente para 0
        $query_update_pontos = "UPDATE perfil_cliente SET pontos = 0 WHERE email = '$email_cliente'";
        $result_update_pontos = mysqli_query($conn, $query_update_pontos);

        if (!$result_update_pontos) {
            echo 'Erro ao atualizar os pontos do cliente: ' . mysqli_error($conn);
            exit();
        }

        echo 'Cupom validado com sucesso e pontos zerados!';
    } else {
        // Cupom inválido ou já utilizado
        echo 'Cupom inválido ou já utilizado.';
    }
} else {
    echo 'Código de cupom não fornecido.';
}
?>
