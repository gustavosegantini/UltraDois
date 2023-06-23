<?php
require_once '../conexao.php';
session_start();
if (!isset($_SESSION['cliente_logado'])) {
    header('Location: login_cliente.php');
    exit;
}


// Buscar estabelecimentos associados ao cliente
$query = $conn->prepare("SELECT Estabelecimentos.nome, Pontos.pontos FROM Pontos JOIN Estabelecimentos ON Pontos.id_estabelecimento = Estabelecimentos.id WHERE Pontos.id_cliente = ?");
$query->execute([$_SESSION['cliente_id']]);
$estabelecimentos = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard do Cliente</title>
    <link rel="stylesheet" href="dashboard_cliente.css">
</head>
<body>
    <div class="container">
        <h1>Dashboard do Cliente</h1>
        <button class="btn-modal" id="btnModal">Inserir código</button>

        <!-- Modal -->
        <div class="modal" id="myModal">
            <div class="modal-content">
                <span class="close-button" id="closeButton">&times;</span>
                <h2>Insira o código do estabelecimento</h2>
                <form action="resgatar_codigo.php" method="post">
                    <input type="text" name="codigo" placeholder="Código" required>
                    <button type="submit">Resgatar</button>
                </form>
            </div>
        </div>

        <h2>Estabelecimentos</h2>
        <div class="cards-container">
            <?php foreach ($estabelecimentos as $estabelecimento): ?>
                <div class="card">
                    <h3><?= $estabelecimento['nome'] ?></h3>
                    <p>Pontos: <?= $estabelecimento['pontos'] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        // Get the modal
        var modal = document.getElementById("myModal");

        // Get the button that opens the modal
        var btn = document.getElementById("btnModal");

        // Get the <span> element that closes the modal
        var span = document.getElementById("closeButton");

        // When the user clicks the button, open the modal 
        btn.onclick = function() {
            modal.style.display = "block";
        }

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
