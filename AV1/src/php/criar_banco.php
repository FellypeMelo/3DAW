<?php
// src/php/criar_banco.php

require_once 'cnx.php';

try {
    $database = new Database();
    
    if ($database->createDatabase()) {
        echo "<h1>Banco de Dados Criado com Sucesso!</h1>";
        echo "<p>O banco de dados 'water_falls' foi criado e populado com dados iniciais.</p>";
        echo "<h2>Usuários criados:</h2>";
        echo "<ul>";
        echo "<li>admin@test.com / admin123 (Administrador)</li>";
        echo "<li>user@test.com / user123 (Usuário Comum)</li>";
        echo "<li>pedro@test.com / pedro123 (Pedro)</li>";
        echo "</ul>";
        echo "<p><a href='../html/index.html'>Ir para o Sistema</a></p>";
    } else {
        echo "<h1>Erro ao criar banco de dados</h1>";
        echo "<p>Verifique se o MySQL está rodando e as credenciais estão corretas.</p>";
    }
} catch (Exception $e) {
    echo "<h1>Erro: " . $e->getMessage() . "</h1>";
    echo "<p>Verifique a configuração do banco de dados no arquivo cnx.php</p>";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criação do Banco de Dados - Water Falls</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-2xl">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-database text-2xl text-green-600"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Configuração do Banco de Dados</h1>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-3">Status do Banco</h2>
            <?php
            try {
                $database = new Database();
                $conn = $database->getConnection();
                
                $stmt = $conn->query("SHOW TABLES");
                $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                echo "<div class='text-green-600 mb-2'>✓ Conexão com MySQL estabelecida</div>";
                echo "<div class='text-green-600 mb-2'>✓ Banco de dados 'water_falls' acessível</div>";
                
                foreach (['usuarios', 'perguntas', 'respostas'] as $table) {
                    if (in_array($table, $tables)) {
                        echo "<div class='text-green-600 mb-1'>✓ Tabela '{$table}' criada</div>";
                    } else {
                        echo "<div class='text-red-600 mb-1'>✗ Tabela '{$table}' não encontrada</div>";
                    }
                }
                
                $stmt = $conn->query("SELECT COUNT(*) as count FROM usuarios");
                $userCount = $stmt->fetch()['count'];
                echo "<div class='text-blue-600 mt-3'>Total de usuários: {$userCount}</div>";
                
            } catch (Exception $e) {
                echo "<div class='text-red-600'>✗ Erro: " . $e->getMessage() . "</div>";
            }
            ?>
        </div>
        
        <div class="text-center">
            <a href="../html/index.html" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold transition-colors inline-block">
                <i class="fas fa-arrow-left mr-2"></i>
                Voltar para o Sistema
            </a>
        </div>
    </div>
</body>
</html>