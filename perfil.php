<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include '../conexao.php';

//Verificar o login

include 'session_verification.php';

verify_session('email', 'login.php');

$email = $_SESSION['email'];

// Verifica o perfil do cliente

$query = "SELECT * FROM perfil_cliente WHERE email = '{$email}'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

$nome = $row['nome'];
$pontos = $row['pontos'];
$pontos_historico = $row['pontos_historico'];
$id_cliente = $row['id'];

// Liste todos os cupons não utilizados do cliente
$query_cupons = "SELECT cupom FROM cupons WHERE id_cliente = '$id_cliente' AND utilizado = 0";
$result_cupons = mysqli_query($conn, $query_cupons);
$cupons = array();
while ($row_cupons = mysqli_fetch_assoc($result_cupons)) {
    $cupons[] = $row_cupons['cupom'];
}

// Liste todos os cupons do cliente, ordenados por utilização
// Liste os últimos 5 cupons utilizados do cliente
$query_cupons_utilizados = "SELECT cupom, utilizado, data_utilizado FROM cupons WHERE id_cliente = '$id_cliente' AND utilizado = 1 ORDER BY data_utilizado DESC LIMIT 5";
$result_cupons_utilizados = mysqli_query($conn, $query_cupons_utilizados);
$cupons_utilizados = array();
while ($row_cupons_utilizados = mysqli_fetch_assoc($result_cupons_utilizados)) {
    $cupons_utilizados[] = $row_cupons_utilizados;
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Perfil do Cliente</title>
    <link rel="stylesheet" type="text/css" href="perfil_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <script src="https://cdn.jsdelivr.net/npm/confetti-js@0.0.13/dist/index.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.3.3"></script>

    <!-- Hotjar Tracking Code for my site -->
    <script>
        (function (h, o, t, j, a, r) {
            h.hj = h.hj || function () { (h.hj.q = h.hj.q || []).push(arguments) };
            h._hjSettings = { hjid: 3443525, hjsv: 6 };
            a = o.getElementsByTagName('head')[0];
            r = o.createElement('script'); r.async = 1;
            r.src = t + h._hjSettings.hjid + j + h._hjSettings.hjsv;
            a.appendChild(r);
        })(window, document, 'https://static.hotjar.com/c/hotjar-', '.js?sv=');
    </script>


</head>

<body>
    <canvas id="confetti-canvas"
        style="position: fixed; top: 0; left: 0; pointer-events: none; z-index: 1000;"></canvas>
    <!-- <button onclick="showConfetti()">Testar Animação</button> -->

    <script>
        function showConfetti() {
            const duration = 3000;
            const end = Date.now() + duration;

            (function frame() {
                confetti({
                    particleCount: 2,
                    angle: 60,
                    spread: 55,
                    origin: { x: 0 },
                    colors: ['#e6b0aa', '#f7dc6f', '#76d7c4', '#f1948a'],
                });
                confetti({
                    particleCount: 2,
                    angle: 120,
                    spread: 55,
                    origin: { x: 1 },
                    colors: ['#e6b0aa', '#f7dc6f', '#76d7c4', '#f1948a'],
                });

                if (Date.now() < end) {
                    requestAnimationFrame(frame);
                }
            }());
        }

    </script>


    <div class="container">
        <div class="modal modal-title">
            <h1>
                Olá,
                <?php echo $nome; ?>!
            </h1>
            <div class="dropdown">
                <button class="dropbtn">
                    <i class="fas fa-user"></i>
                </button>
                <div class="dropdown-content">
                    <a href="editar_email.php">Editar e-mail</a>
                    <a href="criar_nova_senha.php">Criar nova senha</a>
                    <a href="sair.php">Sair</a>
                </div>
            </div>
        </div>
    </div>

    <!-- <div class="buttons">
                <a href="editar_email.php" class="btn nova-senha-btn">Editar e-mail</a>
                <a href="criar_nova_senha.php" class="btn editar-email-btn">Criar nova senha</a>
                <a href="sair.php" class="btn sair-btn">Sair</a>
            </div> -->


    <div class="modal">
        <header>
            <p>Você possui <span class="pontos">
                    <?php echo $pontos; ?>
                </span> pontos.</p>
        </header>
        <main>
            <progress max="5.5" value="<?php echo $pontos; ?>"></progress>

            <form action="adicionar_pontos.php" method="post">
                <label for="codigo">Insira o código da cafeteria:</label>
                <input type="text" id="codigo" name="codigo">
                <input type="submit" value="Resgatar Código">
            </form><br>
            <?php
            if (isset($_SESSION['sucesso'])) {
                echo '<div class="alert success">' . $_SESSION['sucesso'] . '</div>';
                unset($_SESSION['sucesso']);
            }
            if (isset($_SESSION['codigoErro'])) {
                echo '<div class="alert error">' . $_SESSION['codigoErro'] . '</div>';
                unset($_SESSION['codigoErro']);
            }
            ?>

            <?php if (isset($_SESSION['cupomGerado']) && $_SESSION['cupomGerado']): ?>
                <script>
                    showConfetti();
                </script>
                <?php unset($_SESSION['cupomGerado']); ?>
            <?php endif; ?>
        </main>
    </div>

    <?php if (!empty($cupons)): ?>
        <div class="modal">
            <header>
                <h1>Seus cupons</h1>
                <p>Na próxima compra informe esses cupons e ganhe um super desconto</p>
            </header>
            <main>
                <div class="cupons-list">
                    <?php foreach ($cupons as $cupom): ?>
                        <div class="cupom-item">
                            <?php echo $cupom; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </main>
        </div>
    <?php endif; ?>

    <?php if (!empty($cupons_utilizados)): ?>
        <div class="modal">
            <header>
                <h1>Cupons utilizados</h1>
                <p>Veja a lista dos 5 ultimos que você já utilizou</p>
            </header>
            <main>
                <div class="cupons-list">
                    <?php foreach ($cupons_utilizados as $cupom_utilizado): ?>
                        <div class="cupom-utilizado-item">
                            <!-- Código do cupom: -->
                            <?php echo $cupom_utilizado['cupom']; ?><br>
                            Usado em:
                            <?php echo $cupom_utilizado['data_utilizado']; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </main>
        </div>
    <?php endif; ?>

    <div class="modal">
        <header>
            <h1>Suporte e Política de Privacidade</h1>
        </header>
        <main>
            <p>Para suporte, entre em contato conosco pelo e-mail: <a
                    href="mailto:contato@ultrafidelidade.com">contato@ultrafidelidade.com</a></p><br>
            <p>Leia nossa <a href="politica_privacidade.php">Política de Privacidade</a> para saber mais sobre como
                lidamos com suas informações pessoais.</p>
        </main>
    </div>

    <footer>
        <div class="credit-banner">
            <p>Criado por Gustavo Segantini</p>
        </div>
    </footer>
    </div>
</body>

</html>