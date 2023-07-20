<?php
session_start();
// Conexão com o banco de dados
include '../conexao.php';

//Verificar o login

include 'session_verification.php';

verify_session('email_cafeteria', 'login_cafeteria.php');

// Restante do código

$email_cliente = $_GET['email_cliente'];

$query = "SELECT * FROM perfil_cliente WHERE email = '{$email_cliente}'";
$result = mysqli_query($conn, $query);

if (!$result) {
    die('Erro na consulta: ' . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($result);

if (isset($_POST['submit'])) {
    // Atualizar informações do cliente no banco de dados
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $data_nascimento = $_POST['data_nascimento'];
    $curso = $_POST['curso'];
    $pontos = $_POST['pontos'];
    $atletica = isset($_POST['atletica']) ? 1 : 0;

    $sql = "UPDATE perfil_cliente SET nome = '$nome', email = '$email', data_nascimento = '$data_nascimento', curso = '$curso', pontos = '$pontos', atletica = '$atletica' WHERE email = '$email_cliente'";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die('Erro ao atualizar: ' . mysqli_error($conn));
    } else {
        header("Location: perfil_cafeteria.php");
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Editar Cliente</title>
    <link rel="stylesheet" href="editar_cliente_style.css"> <!-- Atualize esta linha -->
    <script>
        function goBack() {
            window.history.back();
        }
    </script>
</head>


<body>
    <header>
        <h1>Editar Cliente</h1>
    </header>
    <?php
    if (isset($update_success) && $update_success) {
        echo '<div class="alert success">Dados do cliente atualizados com sucesso!</div>';
    } elseif (isset($update_success) && !$update_success) {
        echo '<div class="alert error">Ocorreu um erro ao atualizar os dados do cliente. Tente novamente.</div>';
    }
    ?>

    <main>
        <section>
            <form method="post">
                <input type="hidden" name="email_cliente" value="<?php echo $row['email']; ?>">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" value="<?php echo $row['nome']; ?>">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $row['email']; ?>">
                <label for="data_nascimento">Data de Nascimento:</label>
                <input type="date" id="data_nascimento" name="data_nascimento"
                    value="<?php echo $row['data_nascimento']; ?>">
                <label for="curso">Curso:</label>
                <input type="text" id="curso" name="curso" value="<?php echo $row['curso']; ?>">
                <label for="pontos">Pontos:</label>
                <input type="number" id="pontos" name="pontos" value="<?php echo $row['pontos']; ?>">

                <label for="atletica">Atlética:</label>
                <div class="switch">
                    <input type="checkbox" id="atletica" name="atletica" <?php echo $row['atletica'] ? 'checked' : ''; ?>>
                    <span class="slider round"></span>
                </div>
                <br>
                <input type="submit" name="submit" value="Atualizar">
            </form>
        </section>
    </main>
    <a href="deletar_cliente.php?email_cliente=<?php echo $email_cliente; ?>"
        onclick="return confirm('Tem certeza que deseja deletar este cliente?');">Deletar Cliente</a>
    <button onclick="goBack()">Voltar</button>


</body>

</html>