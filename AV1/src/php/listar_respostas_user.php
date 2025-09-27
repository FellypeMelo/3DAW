<?php
require_once 'funcoes.php';

session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../html/index.html');
    exit;
}

$userId = null;
if (isset($_SESSION['user'])) {
    if (is_array($_SESSION['user']) && isset($_SESSION['user']['id'])) {
        $userId = $_SESSION['user']['id'];
    } elseif (is_string($_SESSION['user'])) {
        $cabecalhosUsuarios = ['id', 'tipo', 'nome', 'email', 'senha'];
        $usuarios = lerDados(USERS_FILE, $cabecalhosUsuarios);
        foreach ($usuarios as $u) {
            if (isset($u['nome']) && $u['nome'] === $_SESSION['user']) {
                $userId = $u['id'];
                break;
            }
        }
    }
}
$cabecalhosRespostas = ['id', 'id_usuario', 'id_pergunta', 'resposta_dada', 'data_hora'];
$respostas = lerDados(ANSWERS_FILE, $cabecalhosRespostas);

$minhas = array_filter($respostas, function($r) use ($userId) {
    return isset($r['id_usuario']) && $r['id_usuario'] == $userId;
});

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Respostas</title>
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
        <h1>Minhas Respostas</h1>
        <?php if (empty($minhas)): ?>
            <p>Você ainda não respondeu nenhuma pergunta.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ID Pergunta</th>
                        <th>Resposta</th>
                        <th>Data/Hora</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($minhas as $r): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($r['id']); ?></td>
                            <td><?php echo htmlspecialchars($r['id_pergunta']); ?></td>
                            <td><?php echo htmlspecialchars($r['resposta_dada'] ?? $r['resposta'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($r['data_hora'] ?? $r['data_resposta'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
