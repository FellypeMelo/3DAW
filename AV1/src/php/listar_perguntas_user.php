<?php
require_once 'funcoes.php';

session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../html/index.html');
    exit;
}

$cabecalhosPerguntas = ['id', 'tipo', 'descricao', 'opcoes', 'correta'];
$perguntas = lerDados(QUESTIONS_FILE, $cabecalhosPerguntas);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responder Perguntas</title>
    <link rel="stylesheet" href="../../src/html/estilo.css">
</head>
<body>
    <?php
        if($_SESSION['tipo'] == 'admin'){
            include '../html/menu_admin.html';
        } else {
            include '../html/menu_user.html';
        }
    ?>
    <div class="container">
        <h1>Responder Perguntas</h1>
        <?php if (empty($perguntas)): ?>
            <p>Nenhuma pergunta disponível.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($perguntas as $p): ?>
                    <li>
                        <strong>[<?php echo htmlspecialchars($p['tipo']); ?>]</strong>
                        <?php echo htmlspecialchars($p['descricao']); ?>
                        <a href="ver_pergunta.php?id=<?php echo $p['id']; ?>">Responder / Ver</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</body>
</html>
