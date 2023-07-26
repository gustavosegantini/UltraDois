<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
// Conexão com o banco de dados
include '../conexao.php';
//Verificar o login
include 'session_verification.php';

verify_session('email_cafeteria', 'login_cafeteria.php');

// Inclua a biblioteca PHPExcel ou PhpSpreadsheet aqui
// require_once 'PHPExcel.php';
include 'vendor/autoload.php';
require 'vendor/autoload.php';


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
    <script>
        // <!-- Hotjar Tracking Code for Cafeteria Perfils -->
        (function (h, o, t, j, a, r) {
            h.hj = h.hj || function () { (h.hj.q = h.hj.q || []).push(arguments) };
            h._hjSettings = { hjid: 3444812, hjsv: 6 };
            a = o.getElementsByTagName('head')[0];
            r = o.createElement('script'); r.async = 1;
            r.src = t + h._hjSettings.hjid + j + h._hjSettings.hjsv;
            a.appendChild(r);
        })(window, document, 'https://static.hotjar.com/c/hotjar-', '.js?sv=');
        function gerarCodigo() {
            $.ajax({
                url: 'gerar_codigo.php',
                type: 'POST',
                success: function (codigo) {
                    $('#codigo-gerado').html(codigo);

                },
                error: function () {
                    alert('Erro ao gerar código');
                }
            });
        }
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
        $(document).ready(function () {
            $("#busca").on("keyup", function () {
                var value = $(this).val().toLowerCase();
                $("table tbody tr").filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
</head>

<body>
    <?php
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
        <div class="modal">

            <header>
                <h1>Gerar Código</h1>
            </header>
            <button class="buttonCodigo" onclick="gerarCodigo()">Gerar Código</button>
            <div id="codigo-gerado" class="codigo-gerado"></div>
        </div>
        <div class="modal">


            <header>
                <h1>Validar Cupom</h1>
            </header>
            <?php
            if (isset($_POST['codigo_cupom'])) {
                $codigo_cupom = $_POST['codigo_cupom'];
                $query_cupom = "SELECT * FROM cupons INNER JOIN perfil_cliente ON cupons.id_cliente = perfil_cliente.id WHERE cupons.cupom = '$codigo_cupom' AND cupons.utilizado = 0";
                $result_cupom = mysqli_query($conn, $query_cupom);

                if (mysqli_num_rows($result_cupom) > 0) {
                    $row_cupom = mysqli_fetch_assoc($result_cupom);
                    $nome_cliente = $row_cupom['nome'];
                    $id_cliente = $row_cupom['id'];
                    $cupom_valido = true;
                    $data_utilizado = date("Y-m-d");
                    // Atualiza o cupom para utilizado e reseta os pontos do cliente
                    $sql_update_cupom = "UPDATE cupons SET utilizado = 1, data_utilizado = '$data_utilizado' WHERE cupom = '$codigo_cupom'";
                    mysqli_query($conn, $sql_update_cupom);

                    $sql_reset_pontos = "UPDATE perfil_cliente SET pontos = 0 WHERE id = '$id_cliente'";
                    mysqli_query($conn, $sql_reset_pontos);
                } else {
                    $cupom_valido = false;
                }
            }
            ?>
            <form action="perfil_cafeteria.php" method="post">
                <label for="codigo_cupom">Verificar código de desconto do cliente:</label>
                <input type="text" id="codigo_cupom" name="codigo_cupom">
                <input type="submit" value="Verificar Código">
            </form><br>
            <?php
            if (isset($cupom_valido) && $cupom_valido) {
                echo '<div class="alert success">Cupom válido! O desconto foi aplicado para ' . $nome_cliente . '.</div>';
            } elseif (isset($cupom_valido) && !$cupom_valido) {
                echo '<div class="alert error">Cupom inválido ou já utilizado. Tente novamente.</div>';
            }
            ?>
        </div>

        <div class="modal">
            <header>
                <h1>Clientes e Pontos</h1>
            </header>
            <form action="perfil_cafeteria.php" method="get">
                <!-- <label for="busca">Buscar cliente:</label> -->
                <input type="text" id="busca" name="busca" placeholder="Buscar...">
                <input type="submit" value="Buscar">
            </form>
            <button id="exportar">Exportar para Excel</button>

            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Pontos</th>
                        <th>Idade</th>
                        <th>Curso</th>
                        <th>Pontos Acumulados</th>
                        <th>Cupons Total</th>
                        <th>Cupons Utilizados</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Consulta para buscar todos os clientes e seus pontos
                    $busca = isset($_GET['busca']) ? $_GET['busca'] : '';
                    $query = "SELECT perfil_cliente.nome, perfil_cliente.email, perfil_cliente.pontos, data_nascimento, cursos.nome_curso as nome_curso, COUNT(cupons.id) as total_cupons, SUM(cupons.utilizado) as cupons_utilizados, pontos_historico FROM perfil_cliente LEFT JOIN cupons ON cupons.id_cliente = perfil_cliente.id INNER JOIN cursos ON perfil_cliente.curso = cursos.id WHERE perfil_cliente.nome LIKE '%$busca%' OR perfil_cliente.email LIKE '%$busca%' GROUP BY perfil_cliente.id";

                    $result = mysqli_query($conn, $query);
                    if (!$result) {
                        die('Erro na consulta: ' . mysqli_error($conn));
                    }
                    // Percorre os resultados e adiciona as linhas na tabela
                    while ($row = mysqli_fetch_assoc($result)) {
                        $email_cliente = $row['email'];
                        $pontos_cliente = $row['pontos'];
                        $data_nascimento = $row['data_nascimento'];
                        $curso = $row['curso'];
                        $pontos_historico = $row['pontos_historico'];
                        $total_cupons = $row['total_cupons'];
                        $cupons_utilizados = $row['cupons_utilizados'];
                        // Calcular a idade com base na data de nascimento
                        $hoje = new DateTime();
                        $nascimento = new DateTime($data_nascimento);
                        $idade = $hoje->diff($nascimento)->y;

                        echo '<tr>';
                        echo '<td>' . $row['nome'] . '</td>';
                        echo '<td>' . $row['email'] . '</td>';
                        echo '<td>' . $row['pontos'] . '</td>';
                        echo '<td>' . $idade . '</td>';
                        echo '<td>' . $row['nome_curso'] . '</td>';
                        echo '<td>' . $pontos_historico . '</td>';
                        echo '<td>' . $total_cupons . '</td>';
                        echo '<td>' . $cupons_utilizados . '</td>';
                        echo '<td><a href="editar_cliente.php?email_cliente=' . $row['email'] . '" class="buttonEditar">Editar</a></td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="modal">
            <header>
                <h1>Códigos</h1>
            </header>
            <form action="perfil_cafeteria.php" method="get">
                <!-- <label for="buscador">Buscar código:</label> -->
                <input type="text" id="buscador" name="buscador" placeholder="Buscar...">
                <input type="submit" value="Buscar">
            </form>
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Gerado</th>
                        <th>Data de Geração</th>
                        <th>Utilizado</th>
                        <th>Data de Utilização</th>
                        <!-- <th>Ações</th> -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Consulta para buscar todos os códigos
                    $buscador = isset($_GET['buscador']) ? $_GET['buscador'] : '';
                    $query = "SELECT Codigos.ID_Codigo, Codigos.Codigo, Codigos.Gerado, Codigos.data_gerado, Codigos.Utilizado, Codigos.data_utilizado FROM Codigos WHERE Codigos.Codigo LIKE '%$buscador%' OR Codigos.Utilizado LIKE '%$buscador%' OR Codigos.Gerado LIKE '%$buscador%'";
                    $result = mysqli_query($conn, $query);
                    if (!$result) {
                        die('Erro na consulta: ' . mysqli_error($conn));
                    }
                    // Percorre os resultados e adiciona as linhas na tabela
                    while ($row = mysqli_fetch_assoc($result)) {
                        $codigo = $row['Codigo'];
                        $gerado = $row['Gerado'];
                        $data_gerado = $row['data_gerado'];
                        $utilizado = $row['Utilizado'];
                        $data_utilizado = $row['data_utilizado'];

                        echo '<tr>';
                        echo '<td>' . $codigo . '</td>';
                        echo '<td>' . $gerado . '</td>';
                        echo '<td>' . $data_gerado . '</td>';
                        echo '<td>' . $utilizado . '</td>';
                        echo '<td>' . $data_utilizado . '</td>';
                        // echo '<td><a href="editar_codigo.php?codigo_id=' . $row['id'] . '" class="buttonEditar">Editar</a></td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#exportar").click(function () {
                window.location.href = 'perfil_cafeteria.php?exportar=true';
            });
        });
    </script>
</body>

</html>