<?php
session_start();
include '../conexao.php';
include 'session_verification.php';

verify_session('email_cafeteria', 'login_cafeteria.php');

require 'vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ... (outros códigos PHP necessários, como a função exportData, etc.)
function exportData($data)
{


    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Defina o cabeçalho da planilha
    $headers = ['Nome', 'Email', 'Pontos', 'Idade', 'Curso', 'Pontos Acumulados', 'Cupons Total', 'Cupons Utilizados'];
    $sheet->fromArray([$headers], NULL, 'A1');

    // Preencha a planilha com dados
    $sheet->fromArray($data, NULL, 'A2');

    // Crie um escritor para salvar a planilha
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

    // Configure os headers HTTP para enviar o arquivo ao usuário
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="clientes_e_pontos.xlsx"');
    header('Cache-Control: max-age=0');

    // Escreva a planilha para a saída PHP

    $writer->save('php://output');

}


// Verifica se a ação de exportar foi solicitada
if (isset($_GET['exportar'])) {
    // Consulta para buscar todos os clientes e seus pontos
    $busca = isset($_GET['busca']) ? $_GET['busca'] : '';
    $query = "SELECT perfil_cliente.nome, perfil_cliente.email, perfil_cliente.pontos, data_nascimento, cursos.nome_curso as nome_curso, COUNT(cupons.id) as total_cupons, SUM(cupons.utilizado) as cupons_utilizados, pontos_historico FROM perfil_cliente LEFT JOIN cupons ON cupons.id_cliente = perfil_cliente.id INNER JOIN cursos ON perfil_cliente.curso = cursos.id WHERE perfil_cliente.nome LIKE '%$busca%' OR perfil_cliente.email LIKE '%$busca%' GROUP BY perfil_cliente.id";

    $result = mysqli_query($conn, $query);
    if (!$result) {
        die('Erro na consulta: ' . mysqli_error($conn));
    }
    // Coloque os dados da sua tabela em um array
    $data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        // Calcular a idade com base na data de nascimento
        $hoje = new DateTime();
        $nascimento = new DateTime($row['data_nascimento']);
        $idade = $hoje->diff($nascimento)->y;

        // Adicionar os dados do cliente no array
        $data[] = array(
            'nome' => $row['nome'],
            'email' => $row['email'],
            'pontos' => $row['pontos'],
            'idade' => $idade,
            'nome_curso' => $row['nome_curso'],
            // <-- use 'nome_curso' em vez de 'curso'
            'pontos_historico' => $row['pontos_historico'],
            'total_cupons' => $row['total_cupons'],
            'cupons_utilizados' => $row['cupons_utilizados']
        );

    }

    // Chamar a função para exportar os dados
    exportData($data);

    // Terminar o script para que a tabela não seja mostrada
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Painel da Cafeteria</title>
    <link rel="stylesheet" href="perfil_cafeteria_style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- ... (outros scripts JavaScript necessários, como o código Hotjar, etc.) -->
</head>

<body>
    <?php
    include 'menu_lateral.php';
    
    $page = $_GET['page'] ?? 'resumo'; // valor padrão é 'resumo'

    if ($page == 'resumo') {
        include 'resumo.php';
    } elseif ($page == 'produtos') {
        include 'produtos.php';
    }
    ?>
</body>

</html>
