<?php
    session_start();
    // Mostra alerta se erro na query string
    if (isset($_GET['erro']) && $_GET['erro'] == 1) {
        echo "<script>alert('Usuário ou senha inválidos!');</script>";
    }
    if(!isset($_SESSION['user'])){
        header('Location: ../html/index.html');
        exit;
    }
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Sr Water Fall</title>
</head>
<body>
    <?php
        if (!isset($_SESSION)) {
            session_start();
        }
        if($_SESSION['tipo'] == 'admin'){
            include '../html/menu_admin.html';
        } else {
            include '../html/menu_user.html';
        }
    ?>

    <h1>Bem-vindo, <?php echo htmlspecialchars($_SESSION['user']['nome'] ?? $_SESSION['user']); ?>!</h1>
    <p>Tipo de usuário: <?php echo htmlspecialchars($_SESSION['tipo'] ?? ''); ?></p>
    <a href="../php/logout.php">Sair</a>


    
</body>
</html>