<?php
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

session_start();
// Conexão com o banco de dados
include '../conexao.php';
//Verificar o login
include 'session_verification.php';

verify_session('email_cafeteria', 'login_cafeteria.php');

// Inclua a biblioteca PHPExcel ou PhpSpreadsheet aqui
// require_once 'PHPExcel.php';

require 'vendor/autoload.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



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
                <h1>Produtos</h1>
            </header>
            <div id="lista-produtos">
                <?php
                if (isset($_POST['nome_produto'])) {
                    $nomeProduto = $_POST['nome_produto'];
                    $sql = "SELECT ID, tamanho, preco FROM produtos WHERE nome = '$nomeProduto'";
                    // Executar consulta e retornar dados como JSON
                }
                ?>

                <!-- Os produtos serão inseridos aqui pelo PHP -->
                <?php
                $sql = "SELECT nome FROM produtos GROUP BY nome";
                $result = mysqli_query($conn, $sql);

                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<div class="produto" data-nome="' . $row['nome'] . '">';
                    echo '<h2>' . $row['nome'] . '</h2>';
                    echo '</div>';
                }
                ?>

            </div>
            <div id="codigo-gerado" class="codigo-gerado"></div>

            <div id="popup-selecao" class="popup-selecao" style="display: none;">
                <div class="popup-conteudo">
                    <span class="close-popup">&times;</span>
                    <h3>Selecione o Tamanho e Preço</h3>
                    <div id="opcoes-tamanho-preco">
                        <!-- Opções de tamanho e preço serão inseridas aqui -->
                    </div>
                    <button id="confirmar-selecao">Confirmar</button>
                </div>
            </div>

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
                <!-- <label for="codigo_cupom">Verificar código de desconto do cliente:</label> -->
                <input type="text" id="codigo_cupom" name="codigo_cupom" placeholder="Código do Cupom">
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
                <!-- <input type="submit" value="Buscar"> -->
                <button class="exportar" id="exportarCsvClientes">Exportar para CSV</button><br>
            </form>
            <!-- <button id="exportarS">Exportar para Excel</button> -->


            <table id="tabelaClientes">
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
                        // Calcular a idade com base na data de nascimento
                        $hoje = new DateTime();
                        $nascimento = new DateTime($row['data_nascimento']);
                        $idade = $hoje->diff($nascimento)->y;

                        echo '<tr>';
                        echo '<td>' . $row['nome'] . '</td>';
                        echo '<td>' . $row['email'] . '</td>';
                        echo '<td>' . $row['pontos'] . '</td>';
                        echo '<td>' . $idade . '</td>';
                        echo '<td>' . $row['nome_curso'] . '</td>';
                        echo '<td>' . $row['pontos_historico'] . '</td>';
                        echo '<td>' . $row['total_cupons'] . '</td>';
                        echo '<td>' . $row['cupons_utilizados'] . '</td>';
                        echo '<td><a href="editar_cliente.php?email_cliente=' . $row['email'] . '" class="buttonEditar">Editar</a></td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="modal">
            <header>
                <h1>Produtos</h1>
            </header>
            <form action="perfil_cafeteria.php" method="get">
                <input type="text" id="buscaProduto" name="buscaProduto" placeholder="Buscar...">
                <button class="exportar" id="exportarCsvProdutos">Exportar para CSV</button><br>
            </form>
            <table id="tabelaProdutos">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Tamanho</th>
                        <th>Preço</th>
                        <th>Quantidade Vendida</th>
                        <th>Valor Total Vendido</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $buscaProduto = isset($_GET['buscaProduto']) ? $_GET['buscaProduto'] : '';
                    $query = "SELECT p.nome, p.tamanho, p.preco, COUNT(c.ID_Codigo) as quantidade_vendida, COUNT(c.ID_Codigo)*p.preco as valor_total FROM produtos p LEFT JOIN Codigos c ON p.ID = c.ID_Produto WHERE p.nome LIKE '%$buscaProduto%' GROUP BY p.ID";

                    $result = mysqli_query($conn, $query);
                    if (!$result) {
                        die('Erro na consulta: ' . mysqli_error($conn));
                    }
                    while ($row = mysqli_fetch_assoc($result)) {
                        $tamanho = '';
                        switch ($row['tamanho']) {
                            case 0:
                                $tamanho = 'Pequeno';
                                break;
                            case 1:
                                $tamanho = 'Médio';
                                break;
                            case 2:
                                $tamanho = 'Grande';
                                break;
                            case 3:
                                $tamanho = 'Longo';
                                break;
                        }
                        echo '<tr>';
                        echo '<td>' . $row['nome'] . '</td>';
                        echo '<td>' . $tamanho . '</td>';
                        echo '<td>' . $row['preco'] . '</td>';
                        echo '<td>' . $row['quantidade_vendida'] . '</td>';
                        echo '<td>' . $row['valor_total'] . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>




        <div class="modal">
            <header>
                <h1>Cupons</h1>
            </header>
            <form action="perfil_cafeteria.php" method="get">
                <input type="text" id="buscadorCupons" name="buscadorCupons" placeholder="Buscar...">
            </form>
            <button id="exportarCsvCupons">Exportar para CSV</button>
            <br>
            <table id="tabelaCupons">
                <thead>
                    <tr>
                        <th>Cupom</th>
                        <th>Email do Cliente</th>
                        <th>Utilizado</th>
                        <th>Data de Criação</th>
                        <th>Data de Utilização</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $buscadorCupons = isset($_GET['buscadorCupons']) ? $_GET['buscadorCupons'] : '';
                    $buscadorCupons = "%{$buscadorCupons}%";
                    $stmt = $conn->prepare('SELECT 
            cupons.cupom,
            perfil_cliente.email,
            cupons.utilizado,
            cupons.data_criacao,
            cupons.data_utilizado
          FROM 
            cupons
          INNER JOIN 
            perfil_cliente ON cupons.id_cliente = perfil_cliente.id
          WHERE 
            cupons.cupom LIKE ? OR 
            perfil_cliente.email LIKE ?');
                    $stmt->bind_param('ss', $buscadorCupons, $buscadorCupons);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if (!$result) {
                        die('Erro na consulta: ' . mysqli_error($conn));
                    }
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . $row['cupom'] . '</td>';
                        echo '<td>' . $row['email'] . '</td>';
                        echo '<td>' . ($row['utilizado'] ? 'Sim' : 'Não') . '</td>';

                        $data_criacao = isset($row['data_criacao']) ? date('d/m/y - H:i', strtotime($row['data_criacao'])) : '';
                        echo '<td>' . $data_criacao . '</td>';

                        $data_utilizado = isset($row['data_utilizado']) ? date('d/m/y - H:i', strtotime($row['data_utilizado'])) : '';
                        echo '<td>' . $data_utilizado . '</td>';

                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>


        <div class="modal">
            <header>
                <h1>Vendas</h1>
            </header>
            <form action="perfil_cafeteria.php" method="get">
                <input type="text" id="buscador" name="buscador" placeholder="Buscar...">
            </form>
            <button id="exportarCsvCodigos">Exportar para CSV</button>
            <br>
            <table id="tabelaCodigos">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Tamanho</th>
                        <th>Preço</th>
                        <th>Vendedor</th>
                        <th>Data da Venda</th>
                        <th>Código de Fidelidade</th>
                        <th>Cliente</th>
                        <th>Data de Resgate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $buscador = isset($_GET['buscador']) ? $_GET['buscador'] : '';
                    $buscador = "%{$buscador}%";
                    $stmt = $conn->prepare('SELECT 
            Codigos.ID_Codigo, 
            Codigos.Codigo, 
            Codigos.Gerado, 
            Codigos.data_gerado, 
            Codigos.Utilizado, 
            Codigos.data_utilizado, 
            produtos.nome, 
            produtos.tamanho, 
            produtos.preco
        FROM 
            Codigos 
        INNER JOIN 
            produtos ON Codigos.ID_Produto = produtos.ID
        WHERE 
            Codigos.Codigo LIKE ? OR 
            Codigos.Utilizado LIKE ? OR 
            Codigos.Gerado LIKE ?');
                    $stmt->bind_param('sss', $buscador, $buscador, $buscador);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if (!$result) {
                        die('Erro na consulta: ' . mysqli_error($conn));
                    }
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . $row['nome'] . '</td>';

                        $tamanho = '';
                        switch ($row['tamanho']) {
                            case 0:
                                $tamanho = 'Pequeno';
                                break;
                            case 1:
                                $tamanho = 'Médio';
                                break;
                            case 2:
                                $tamanho = 'Grande';
                                break;
                            case 3:
                                $tamanho = 'Longo';
                                break;
                        }
                        echo '<td>' . $tamanho . '</td>';
                        echo '<td>' . $row['preco'] . '</td>';
                        echo '<td>' . $row['Gerado'] . '</td>';
                        $data_gerado = isset($row['data_gerado']) ? date('d/m/y - H:i', strtotime($row['data_gerado'])) : '';
                        echo '<td>' . $data_gerado . '</td>';
                        echo '<td>' . $row['Codigo'] . '</td>';
                        $utilizado = $row['Utilizado'] == 0 ? 'Não Resgatado' : $row['Utilizado'];
                        echo '<td>' . $utilizado . '</td>';
                        $data_utilizado = isset($row['data_utilizado']) ? date('d/m/y - H:i', strtotime($row['data_utilizado'])) : '';
                        echo '<td>' . $data_utilizado . '</td>';
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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>
        $(document).ready(function () {
            // Exportar Clientes
            $("#exportarCsvClientes").on('click', function (e) {
                e.preventDefault();
                exportarCsv('tabelaClientes', 'dados_clientes.csv');
            });

            // Exportar Códigos
            $("#exportarCsvCodigos").on('click', function (e) {
                e.preventDefault();
                exportarCsv('tabelaCodigos', 'dados_codigos.csv');
            });

            // Exportar Cupons
            $("#exportarCsvCupons").on('click', function (e) {
                e.preventDefault();
                exportarCsv('tabelaCupons', 'dados_cupons.csv');
            });

            // Exportar Produtos
            $("#exportarCsvProdutos").on('click', function (e) {
                e.preventDefault();
                exportarCsv('tabelaProdutos', 'dados_produtos.csv');
            });

            function exportarCsv(tabelaId, arquivoNome) {
                var headers = [];
                $('#' + tabelaId + ' th').each(function () {
                    headers.push($(this).text().trim());
                });

                var data = [];
                $('#' + tabelaId + ' tbody tr').each(function () {
                    var row = [];
                    $(this).find('td').each(function () {
                        row.push($(this).text().trim());
                    });
                    data.push(row.join(";"));
                });

                var csvContent = headers.join(";") + "\n" + data.join("\n");
                var encodedUri = encodeURI("data:text/csv;charset=utf-8," + csvContent);
                var link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", arquivoNome);
                document.body.appendChild(link);

                link.click();
            }
        });

        fetch('get_produtos.php')
            .then(response => response.json())
            .then(produtos => {
                let listaProdutos = document.getElementById('lista-produtos');

                for (let produto of produtos) {
                    let produtoElemento = document.createElement('div');
                    produtoElemento.classList.add('produto');
                    produtoElemento.innerHTML = `<h2>${produto.nome}</h2>`;
                    produtoElemento.setAttribute('data-id-produto', produto.ID);
                    produtoElemento.addEventListener('click', function () {
                        mostrarPopupSelecao(produto);
                    });
                    listaProdutos.appendChild(produtoElemento);
                }
            });

        // Função para gerar o pop-up de seleção de tamanho
        function exibirPopupTamanho(idProduto) {
            let popup = document.getElementById('popup-tamanho');
            popup.innerHTML = `
        <div>
            <h3>Selecione o Tamanho:</h3>
            <button onclick="selecionarTamanho(${idProduto}, 'P')">P</button>
            <button onclick="selecionarTamanho(${idProduto}, 'M')">M</button>
            <button onclick="selecionarTamanho(${idProduto}, 'G')">G</button>
            <button onclick="selecionarTamanho(${idProduto}, 'L')">L</button>
        </div>
    `;
            popup.style.display = 'block';
        }

        // Função para selecionar o tamanho e gerar o código
        function selecionarTamanho(idProduto, tamanho) {
            document.getElementById('popup-tamanho').style.display = 'none';
            // Aqui você pode incluir o código para buscar o preço com base no tamanho, se necessário
            gerarCodigo(idProduto, tamanho);
        }

        // Função para gerar o código
        function gerarCodigo(idProduto, tamanho) {
            // Aqui pode ser adicionada a lógica para incluir o tamanho na solicitação, se necessário
            fetch('gerar_codigo.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id_produto=${idProduto}&tamanho=${tamanho}`
            })
                .then(response => response.text())
                .then(codigo => {
                    document.getElementById('codigo-gerado').innerText = `Código gerado: ${codigo}`;
                })
                .catch(error => console.error('Erro:', error));
        }

        // Adiciona o evento de clique nos produtos
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.produto').forEach(produto => {
                produto.addEventListener('click', () => {
                    exibirPopupTamanho(produto.getAttribute('data-id'));
                });
            });
        });

        // Variável para armazenar o produto selecionado
        let produtoSelecionado = null;

        document.querySelectorAll('.produto').forEach(produto => {
            produto.addEventListener('click', function () {
                let nomeProduto = this.getAttribute('data-nome');
                produtoSelecionado = nomeProduto;
                fetch('get_tamanhos_precos.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `nome_produto=${nomeProduto}`
                })
                    .then(response => response.json())
                    .then(dados => {
                        let opcoes = document.getElementById('opcoes-tamanho-preco');
                        opcoes.innerHTML = ''; // Limpar opções anteriores
                        dados.forEach(item => {
                            opcoes.innerHTML += `<div class="opcao-tamanho-preco" data-id="${item.ID}">${item.tamanho} - ${item.preco}</div>`;
                        });
                        document.getElementById('popup-selecao').style.display = 'block';
                    });
            });
        });

        document.getElementById('confirmar-selecao').addEventListener('click', function () {
            let opcaoSelecionada = document.querySelector('.opcao-tamanho-preco.selecionado');
            if (opcaoSelecionada) {
                gerarCodigo(opcaoSelecionada.getAttribute('data-id'));
            }
        });




    </script>


</body>

</html>