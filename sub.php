<?php
// Dados simulados dos planos
$planos = [
    ["nome" => "Plano básico", "preco" => "R$10,00", "descricao" => "Acesso básico a todos os recursos"],
    ["nome" => "Plano padrão", "preco" => "R$20,00", "descricao" => "Acesso a todos os recursos com suporte"],
    ["nome" => "Plano premium", "preco" => "R$30,00", "descricao" => "Acesso a todos os recursos com suporte prioritário"],
    ["nome" => "Plano super premium", "preco" => "R$40,00", "descricao" => "Acesso a todos os recursos com suporte 24/7"],
];
?>
<!DOCTYPE html>
<html>

<head>
    <title>Planos de assinatura</title>
    <link rel="stylesheet" type="text/css" href="sub_style.css">
</head>

<body>
    <div class="container">
        <h1>Selecione seu plano de assinatura</h1>
        <div class="planos">
            <?php foreach ($planos as $plano): ?>
                <div class="plano-card">
                    <h2>
                        <?php echo $plano['nome']; ?>
                    </h2>
                    <h3>
                        <?php echo $plano['preco']; ?>
                    </h3>
                    <p>
                        <?php echo $plano['descricao']; ?>
                    </p>
                    <button>Assine agora</button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>