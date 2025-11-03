<?php
$servername = "localhost";
$username = "root";
$password = "";

// Criar conexão sem database
$conn = new mysqli($servername, $username, $password);

// Verificar conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Criar database
$sql = "CREATE DATABASE IF NOT EXISTS escola_alunos";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Selecionar database
$conn->select_db("escola_alunos");

// Criar tabela alunos
$sql = "CREATE TABLE IF NOT EXISTS alunos (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    telefone VARCHAR(20),
    data_nascimento DATE,
    endereco TEXT,
    serie VARCHAR(20),
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table alunos created successfully or already exists<br>";
    
    // Inserir alguns dados de exemplo
    $sql_insert = "INSERT IGNORE INTO alunos (nome, email, telefone, data_nascimento, endereco, serie) VALUES 
        ('João Silva', 'joao@email.com', '(11) 9999-8888', '2005-03-15', 'Rua A, 123 - São Paulo', '1º Ano'),
        ('Maria Santos', 'maria@email.com', '(11) 7777-6666', '2004-07-22', 'Av. B, 456 - São Paulo', '2º Ano'),
        ('Pedro Oliveira', 'pedro@email.com', '(11) 5555-4444', '2003-11-30', 'Rua C, 789 - São Paulo', '3º Ano')";
    
    if ($conn->query($sql_insert) === TRUE) {
        echo "Sample data inserted successfully<br>";
    }
    
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

$conn->close();
echo "Setup completed successfully!";
?>