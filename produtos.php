<div class="container">
    <div class="modal">
        <header>
            <h1>Produtos</h1>
        </header>
        <div id="lista-produtos">
            <!-- Os produtos serão inseridos aqui pelo PHP -->
            <?php
            $sql = "SELECT * FROM produtos";
            $result = mysqli_query($conn, $sql);

            while ($row = mysqli_fetch_assoc($result)) {
                $tamanho = '';
                switch ($row['tamanho']) {
                    case 0:
                        $tamanho = 'P';
                        break;
                    case 1:
                        $tamanho = 'M';
                        break;
                    case 2:
                        $tamanho = 'G';
                        break;
                    case 3:
                        $tamanho = 'L';
                        break;
                }
                echo '<div class="produto" data-id="' . $row['ID'] . '">';
                echo '<h2>' . $row['nome'] . ' - ' . $tamanho . '</h2>';
                echo '<p>' . $row['preco'] . '</p>';
                echo '</div>';
            }
            ?>
        </div>
        <div id="codigo-gerado" class="codigo-gerado"></div>
    </div>
</div>

<script>
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
    function gerarCodigo(id_produto) {
        // ... (o seu código JavaScript para gerar código)
        // A solicitação POST é feita para gerar_codigo.php com o ID do produto
        fetch('gerar_codigo.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id_produto=${id_produto}`
        })
            .then(response => response.text())
            .then(codigo => {
                document.getElementById('codigo-gerado').innerText = `Código gerado: ${codigo}`;
            })
            .catch(error => console.error('Erro:', error));
    }

    // ... (outros códigos JavaScript necessários para a funcionalidade produtos)
</script>