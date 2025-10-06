<?php
require_once 'funcoes.php';
    session_start();
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
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .stats-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen">
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

    <div class="container mx-auto px-4 py-8">
        <!-- Header de Boas-Vindas -->
        <div class="text-center mb-12">
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-8 mx-auto max-w-2xl border border-white/20">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <i class="fas fa-user text-3xl text-purple-600"></i>
                </div>
                <h1 class="text-4xl font-bold text-white mb-3">
                    Bem-vindo, <?php echo htmlspecialchars($_SESSION['user']['nome'] ?? $_SESSION['user']); ?>!
                </h1>
                <div class="inline-flex items-center bg-white/20 px-4 py-2 rounded-full mb-4">
                    <span class="text-white font-semibold">
                        <i class="fas fa-user-tag mr-2"></i>
                        <?php echo htmlspecialchars($_SESSION['tipo'] ?? ''); ?>
                    </span>
                </div>
                <p class="text-white/80 text-lg mb-6">
                    Sistema de Gerenciamento de Perguntas e Respostas
                </p>
                <a 
                    href="../php/logout.php" 
                    class="bg-white/20 hover:bg-white/30 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 border border-white/30 flex items-center justify-center mx-auto w-48"
                >
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Sair do Sistema
                </a>
            </div>
        </div>

        <!-- Dashboard Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <!-- Card Perguntas -->
            <div class="stats-card rounded-xl p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-blue-500/20 p-3 rounded-lg">
                        <i class="fas fa-question-circle text-2xl text-blue-300"></i>
                    </div>
                    <span class="text-3xl font-bold"><?php 
                        $cabecalhosPerguntas = ['id', 'tipo', 'descricao', 'opcoes', 'correta'];
                        $perguntas = file_exists(QUESTIONS_FILE) ? lerDados(QUESTIONS_FILE, $cabecalhosPerguntas) : [];
                        echo count($perguntas);
                    ?></span>
                </div>
                <h3 class="text-lg font-semibold mb-2">Total de Perguntas</h3>
                <p class="text-white/70 text-sm">Perguntas cadastradas no sistema</p>
            </div>

            <!-- Card Usuários -->
            <div class="stats-card rounded-xl p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-green-500/20 p-3 rounded-lg">
                        <i class="fas fa-users text-2xl text-green-300"></i>
                    </div>
                    <span class="text-3xl font-bold"><?php 
                        $cabecalhosUsuarios = ['id', 'tipo', 'nome', 'email', 'senha'];
                        $usuarios = file_exists(USERS_FILE) ? lerDados(USERS_FILE, $cabecalhosUsuarios) : [];
                        echo count($usuarios);
                    ?></span>
                </div>
                <h3 class="text-lg font-semibold mb-2">Total de Usuários</h3>
                <p class="text-white/70 text-sm">Usuários registrados no sistema</p>
            </div>

            <!-- Card Respostas -->
            <div class="stats-card rounded-xl p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-purple-500/20 p-3 rounded-lg">
                        <i class="fas fa-comments text-2xl text-purple-300"></i>
                    </div>
                    <span class="text-3xl font-bold"><?php 
                        $cabecalhosRespostas = ['id', 'user_id', 'pergunta_id', 'resposta', 'data_hora'];
                        $respostas = file_exists(ANSWERS_FILE) ? lerDados(ANSWERS_FILE, $cabecalhosRespostas) : [];
                        echo count($respostas);
                    ?></span>
                </div>
                <h3 class="text-lg font-semibold mb-2">Respostas Registradas</h3>
                <p class="text-white/70 text-sm">Total de respostas dos usuários</p>
            </div>

            <!-- Card Status -->
            <div class="stats-card rounded-xl p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-yellow-500/20 p-3 rounded-lg">
                        <i class="fas fa-chart-line text-2xl text-yellow-300"></i>
                    </div>
                    <span class="text-3xl font-bold">
                        <?php echo $_SESSION['tipo'] === 'admin' ? 'Admin' : 'User'; ?>
                    </span>
                </div>
                <h3 class="text-lg font-semibold mb-2">Seu Status</h3>
                <p class="text-white/70 text-sm">Nível de acesso no sistema</p>
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-8 border border-white/20">
            <h2 class="text-2xl font-bold text-white mb-6 text-center">
                <i class="fas fa-rocket mr-3"></i>
                Ações Rápidas
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <?php if ($_SESSION['tipo'] === 'admin'): ?>
                    <!-- Admin Actions -->
                    <a href="listar_perguntas.php" class="bg-blue-500 hover:bg-blue-600 text-white p-4 rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg flex flex-col items-center text-center">
                        <i class="fas fa-list text-2xl mb-2"></i>
                        <span class="font-semibold">Gerenciar Perguntas</span>
                        <small class="text-blue-100 text-xs mt-1">Ver todas as perguntas</small>
                    </a>

                    <a href="adicionar_pergunta.php" class="bg-green-500 hover:bg-green-600 text-white p-4 rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg flex flex-col items-center text-center">
                        <i class="fas fa-plus text-2xl mb-2"></i>
                        <span class="font-semibold">Nova Pergunta</span>
                        <small class="text-green-100 text-xs mt-1">Adicionar texto</small>
                    </a>

                    <a href="adicionar_pergunta_me.php" class="bg-purple-500 hover:bg-purple-600 text-white p-4 rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg flex flex-col items-center text-center">
                        <i class="fas fa-list-ol text-2xl mb-2"></i>
                        <span class="font-semibold">Múltipla Escolha</span>
                        <small class="text-purple-100 text-xs mt-1">Nova pergunta ME</small>
                    </a>

                    <a href="listar_usuarios.php" class="bg-orange-500 hover:bg-orange-600 text-white p-4 rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg flex flex-col items-center text-center">
                        <i class="fas fa-users-cog text-2xl mb-2"></i>
                        <span class="font-semibold">Gerenciar Usuários</span>
                        <small class="text-orange-100 text-xs mt-1">Administrar</small>
                    </a>

                <?php else: ?>
                    <!-- User Actions -->
                    <a href="listar_perguntas_user.php" class="bg-blue-500 hover:bg-blue-600 text-white p-4 rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg flex flex-col items-center text-center">
                        <i class="fas fa-play text-2xl mb-2"></i>
                        <span class="font-semibold">Responder Perguntas</span>
                        <small class="text-blue-100 text-xs mt-1">Iniciar quiz</small>
                    </a>

                    <a href="ver_pergunta.php" class="bg-green-500 hover:bg-green-600 text-white p-4 rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg flex flex-col items-center text-center">
                        <i class="fas fa-eye text-2xl mb-2"></i>
                        <span class="font-semibold">Ver Progresso</span>
                        <small class="text-green-100 text-xs mt-1">Minhas respostas</small>
                    </a>

                    <a href="listar_perguntas_user.php" class="bg-purple-500 hover:bg-purple-600 text-white p-4 rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg flex flex-col items-center text-center">
                        <i class="fas fa-chart-bar text-2xl mb-2"></i>
                        <span class="font-semibold">Estatísticas</span>
                        <small class="text-purple-100 text-xs mt-1">Meu desempenho</small>
                    </a>

                    <a href="../php/logout.php" class="bg-red-500 hover:bg-red-600 text-white p-4 rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg flex flex-col items-center text-center">
                        <i class="fas fa-sign-out-alt text-2xl mb-2"></i>
                        <span class="font-semibold">Sair</span>
                        <small class="text-red-100 text-xs mt-1">Logout</small>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Informações do Sistema -->
        <div class="mt-8 text-center">
            <p class="text-white/60 text-sm">
                <i class="fas fa-shield-alt mr-1"></i>
                Sistema Water Falls v1.0 • Desenvolvido com PHP e Tailwind CSS
            </p>
        </div>
    </div>

    <!-- Script para animações -->
    <script>
        // Animações de entrada
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.stats-card, .bg-white\\/10');
            elements.forEach((el, index) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    el.style.transition = 'all 0.6s ease-out';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 200);
            });
        });

        // Efeito de hover nos cards de ação
        const actionCards = document.querySelectorAll('a.bg-blue-500, a.bg-green-500, a.bg-purple-500, a.bg-orange-500, a.bg-red-500');
        actionCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.05) translateY(-5px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1) translateY(0)';
            });
        });
    </script>
</body>
</html>