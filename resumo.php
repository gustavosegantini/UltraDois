<?php
$email_cafeteria = $_SESSION['email_cafeteria'];

$query = "SELECT * FROM perfil_cafeteria WHERE email = '$email_cafeteria'";
$result = mysqli_query($conn, $query);
if (!$result) {
    die('Erro na consulta: ' . mysqli_error($conn));
}
$row = mysqli_fetch_assoc($result);

$nome_cafeteria = $row['nome'];

// ... (outros códigos PHP para buscar informações do resumo)


$email_cafeteria = $_SESSION['email_cafeteria'];

$query = "SELECT * FROM perfil_cafeteria WHERE email = '$email_cafeteria'";
$result = mysqli_query($conn, $query);
if (!$result) {
    die('Erro na consulta: ' . mysqli_error($conn));
}
$row = mysqli_fetch_assoc($result);

$nome_cafeteria = $row['nome'];
// Conta o número total de usuários na tabela perfil_cliente
$query_total_usuarios = "SELECT COUNT(*) AS total_usuarios FROM perfil_cliente";
$result_total_usuarios = mysqli_query($conn, $query_total_usuarios);

if (!$result_total_usuarios) {
    die('Erro na consulta: ' . mysqli_error($conn));
}

$row_total_usuarios = mysqli_fetch_assoc($result_total_usuarios);
$total_usuarios = $row_total_usuarios['total_usuarios'];
// Conta a quantidade total de cupons gerados
$query_total_cupons = "SELECT COUNT(*) AS total_cupons FROM cupons";
$result_total_cupons = mysqli_query($conn, $query_total_cupons);

if (!$result_total_cupons) {
    die('Erro na consulta: ' . mysqli_error($conn));
}

$row_total_cupons = mysqli_fetch_assoc($result_total_cupons);
$total_cupons = $row_total_cupons['total_cupons'];
// Conta a quantidade total de cupons utilizados
$query_cupons_utilizados = "SELECT COUNT(*) AS total_cupons_utilizados FROM cupons WHERE utilizado = 1";
$result_cupons_utilizados = mysqli_query($conn, $query_cupons_utilizados);

if (!$result_cupons_utilizados) {
    die('Erro na consulta: ' . mysqli_error($conn));
}
$row_cupons_utilizados = mysqli_fetch_assoc($result_cupons_utilizados);
$total_cupons_utilizados = $row_cupons_utilizados['total_cupons_utilizados'];


?>
<div class="container">
    <div class="modal">
        <header>
            <h1>Resumo</h1>
        </header>
        <p>Número total de usuários: <span id="total-usuarios">
                <?php echo $total_usuarios; ?>
            </span></p>
        <p>Número total de cupons: <span id="total-cupons">
                <?php echo $total_cupons; ?>
            </span></p>
        <p>Número total de cupons utilizados: <span id="total-cupons-utilizados">
                <?php echo $total_cupons_utilizados; ?>
            </span></p>
    </div>
</div>

<script>
    function atualizarResumo() {
            $.ajax({
                url: 'atualizar_resumo.php',
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    $('#total-usuarios').html(data.total_usuarios);
                    $('#total-cupons').html(data.total_cupons);
                    $('#total-cupons-utilizados').html(data.total_cupons_utilizados);
                },
                error: function () {
                    alert('Erro ao atualizar resumo');
                }
            });
        }

    // ... (outros códigos JavaScript necessários para a funcionalidade resumo)
</script>