<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Cliente</title>
    <link rel="stylesheet" href="cadastro_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
</head>

<body>
    <div class="container">
        <div class="modal">
            <header>
                <h1>UltraFidelidade</h1></br>
            </header>
            <form action="cadastrar_cliente.php" method="post">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" placeholder="Seu Nome" required>

                <label for="sobrenome">Sobrenome:</label>
                <input type="text" id="sobrenome" name="sobrenome" placeholder="Seu Sobrenome" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Seu e-mail" required>

                <div class="input-wrapper">
                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" placeholder="Sua Senha" required>
                    <i class="far fa-eye password-icon" onclick="togglePasswordVisibility('senha')"></i>
                </div>

                <div class="input-wrapper">
                    <label for="confirmar_senha">Confirmar Senha:</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Confirmar Senha"
                        required>
                    <i class="far fa-eye password-icon" onclick="togglePasswordVisibility('confirmar_senha')"></i>
                </div>


                <!-- <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" placeholder="Sua Senha" required>

                <label for="confirmar_senha">Confirmar Senha:</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Confirmar Senha"
                    required> -->

                <!-- Adicionando campo Data de Nascimento -->
                <label for="data_nascimento">Data de Nascimento:</label>
                <input type="date" id="data_nascimento" name="data_nascimento" placeholder="DD/MM/AAAA" required><br>

                <!-- Adicionando campo Curso -->
                <label for="curso">Curso:</label>
                <select id="curso" name="curso" required>
                    <option value="">Selecione o curso</option>
                    <?php
                    include '../conexao.php';

                    $query = "SELECT * FROM cursos";
                    $result = mysqli_query($conn, $query);

                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<option value="' . $row['id'] . '">' . $row['nome_curso'] . '</option>';
                    }
                    ?>
                </select>

                <label for="aceitar_politica_privacidade">
                    <input type="checkbox" id="aceitar_politica_privacidade" name="aceitar_politica_privacidade"
                        required>
                    Eu concordo com a <a href="politica_privacidade.php" target="_blank">Política de
                        Privacidade</a>.
                </label>

                <input type="submit" value="Cadastrar">
            </form>
        </div>
    </div>
</body>

<script>
    function togglePasswordVisibility(inputId) {
        const input = document.getElementById(inputId);
        const icon = input.nextElementSibling;

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>


</html>