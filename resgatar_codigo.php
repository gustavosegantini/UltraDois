<?php
require_once '../conexao.php';

session_start();

// Verificar se o cliente está logado
if (!isset($_SESSION['cliente_id'])) {
    header('Location: login_cliente.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codigo = $_POST['codigo'];

    try {
        // Buscar código na tabela Pontos
        $query = $conn->prepare('SELECT * FROM Pontos WHERE codigo = :codigo AND utilizado = 0');
        $query->execute([':codigo' => $codigo]);
        $ponto = $query->fetch(PDO::FETCH_ASSOC);

        if ($ponto) {
            // Verificar se o cliente já resgatou pontos desse estabelecimento
            $query = $conn->prepare('SELECT * FROM Cupons WHERE id_cliente = :id_cliente AND id_estabelecimento = :id_estabelecimento');
            $query->execute([':id_cliente' => $_SESSION['cliente_id'], ':id_estabelecimento' => $ponto['id_estabelecimento']]);
            $cupom = $query->fetch(PDO::FETCH_ASSOC);

            if ($cupom) {
                // Se já resgatou pontos, incrementar a contagem
                $query = $conn->prepare('UPDATE Cupons SET pontos = pontos + 1 WHERE id = :id');
                $query->execute([':id' => $cupom['id']]);
            } else {
                // Se ainda não resgatou pontos, criar um novo cupom
                $query = $conn->prepare('INSERT INTO Cupons (id_cliente, id_estabelecimento, pontos) VALUES (:id_cliente, :id_estabelecimento, 1)');
                $query->execute([':id_cliente' => $_SESSION['cliente_id'], ':id_estabelecimento' => $ponto['id_estabelecimento']]);
            }

            // Marcar o código resgatado como utilizado
            $query = $conn->prepare('UPDATE Pontos SET utilizado = 1 WHERE id = :id');
            $query->execute([':id' => $ponto['id']]);

            $_SESSION['mensagem'] = 'Código resgatado com sucesso!';
        } else {
            $_SESSION['erro'] = 'Código inválido ou já utilizado.';
        }
    } catch (PDOException $e) {
        $_SESSION['erro'] = 'Erro ao resgatar código: ' . $e->getMessage();
    }

    header('Location: dashboard_cliente.php');
    exit;
}
?>

