<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['nome']) && isset($_POST['sobrenome']) && isset($_POST['email']) && isset($_POST['senha']) && isset($_POST['confirmar_senha'])) {

    if ($_POST['senha'] == $_POST['confirmar_senha']) {

        session_start();
        include '../conexao.php';

        if ($conn->connect_error) {
            die("Conexão falhou: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("INSERT INTO perfil_cafeteria (nome, sobrenome, email, senha, data_cadastro) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nome, $sobrenome, $email, $senha, $data_cadastro);
        
        // Define os parâmetros e executa a query
        $nome = $_POST['nome'];
        $sobrenome = $_POST['sobrenome'];
        $email = $_POST['email'];
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        $data_cadastro = date("Y-m-d H:i:s");
        
        $stmt->execute();
        

        if ($stmt->affected_rows > 0) {
            header("Location: perfil_cafeteria.php");
            exit();
        } else {
            echo "Erro ao cadastrar funcionário. Por favor, tente novamente mais tarde.";
        }

        $stmt->close();
        $conn->close();

    } else {
        echo "As senhas não coincidem. Por favor, tente novamente.";
    }

} else {
    echo "Por favor, preencha todos os campos.";
}
?>
