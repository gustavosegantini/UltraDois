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

<script>
    //Produtos:
    function gerarCodigo(id_produto) {
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
</script>