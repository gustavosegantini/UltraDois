<?php
include '../conexao.php';

// Consulta para buscar todos os clientes e seus pontos
$busca = isset($_GET['busca']) ? $_GET['busca'] : '';
$query = "SELECT nome, email, pontos FROM perfil_cliente WHERE nome LIKE '%$busca%' OR email LIKE '%$busca%'";
$result = mysqli_query($conn, $query);

if (!$result) {
    die('Erro na consulta: ' . mysqli_error($conn));
}

echo '<table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Pontos</th>
            </tr>
        </thead>
        <tbody>';

// Percorre os resultados e adiciona as linhas na tabela
while ($row = mysqli_fetch_assoc($result)) {
    $email_cliente = $row['email'];
    $pontos_cliente = $row['pontos'];
    echo '<tr>';
    echo '<td>' . $row['nome'] . '</td>';
    echo '<td>' . $row['email'] . '</td>';
    echo '<td>' . $row['pontos'] . '</td>';
    echo '</tr>';
    echo "<script>verificarPontos('$email_cliente', $pontos_cliente);</script>";
}

echo '</tbody>
    </table>';
?>
