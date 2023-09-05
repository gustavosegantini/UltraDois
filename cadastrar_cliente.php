<?php
// Verifica se os campos foram preenchidos
if (isset($_POST['nome']) && isset($_POST['sobrenome']) && isset($_POST['email']) && isset($_POST['senha']) && isset($_POST['confirmar_senha']) && isset($_POST['data_nascimento']) && isset($_POST['curso'])) {

    // Verifica se as senhas coincidem
    if ($_POST['senha'] == $_POST['confirmar_senha']) {

        session_start();
        include '../conexao.php';

        // Verifica se a conexão foi estabelecida com sucesso
        if ($conn->connect_error) {
            die("Conexão falhou: " . $conn->connect_error);
        }

        // Prepara a query SQL
        $stmt = $conn->prepare("INSERT INTO perfil_cliente (nome, sobrenome, email, senha, data_nascimento, curso) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $nome, $sobrenome, $email, $senha, $data_nascimento, $curso);

        // Define os parâmetros e executa a query
        $nome = $_POST['nome'];
        $sobrenome = $_POST['sobrenome'];
        $email = $_POST['email'];
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        $data_nascimento = $_POST['data_nascimento'];
        $curso = $_POST['curso'];

        $stmt->execute();

        // Verifica se a query foi executada com sucesso
        if ($stmt->affected_rows > 0) {
            // Registro bem-sucedido
            $_SESSION['email'] = $email;
            // Redireciona para a página de sucesso
            header("Location: perfil.php");
            exit();
        } else {
            // Mostra uma mensagem de erro
            echo "Erro ao cadastrar cliente. Por favor, tente novamente mais tarde.";
        }


        // Fecha a conexão com o banco de dados
        $stmt->close();
        $conn->close();

    } else {
        // Mostra uma mensagem de erro
        echo "As senhas não coincidem. Por favor, tente novamente.";
    }

} else {
    // Mostra uma mensagem de erro
    echo "Por favor, preencha todos os campos.";
}
?>
