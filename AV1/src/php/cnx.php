<?php

class Database {
    private $host = 'localhost';
    private $db_name = 'water_falls';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            error_log("Erro de conexão: " . $exception->getMessage());
            throw new Exception("Erro ao conectar com o banco de dados");
        }

        return $this->conn;
    }

    public function createDatabase() {
        try {
            $temp_conn = new PDO("mysql:host=" . $this->host, $this->username, $this->password);
            $temp_conn->exec("CREATE DATABASE IF NOT EXISTS " . $this->db_name . " CHARACTER SET utf8 COLLATE utf8_general_ci");
            
            $this->conn = $this->getConnection();
            
            $this->createTables();
            
            return true;
        } catch(PDOException $exception) {
            error_log("Erro ao criar banco: " . $exception->getMessage());
            return false;
        }
    }

    private function createTables() {
        $queries = [
            // Tabela de usuários
            "CREATE TABLE IF NOT EXISTS usuarios (
                id INT AUTO_INCREMENT PRIMARY KEY,
                tipo ENUM('admin', 'user') NOT NULL DEFAULT 'user',
                nome VARCHAR(100) NOT NULL,
                email VARCHAR(150) NOT NULL UNIQUE,
                senha VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

            // Tabela de perguntas
            "CREATE TABLE IF NOT EXISTS perguntas (
                id INT AUTO_INCREMENT PRIMARY KEY,
                tipo ENUM('texto', 'multipla_escolha') NOT NULL,
                descricao TEXT NOT NULL,
                opcoes TEXT NULL,
                correta VARCHAR(500) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

            // Tabela de respostas
            "CREATE TABLE IF NOT EXISTS respostas (
                id INT AUTO_INCREMENT PRIMARY KEY,
                id_usuario INT NOT NULL,
                id_pergunta INT NOT NULL,
                resposta_dada TEXT NOT NULL,
                data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
                FOREIGN KEY (id_pergunta) REFERENCES perguntas(id) ON DELETE CASCADE,
                INDEX idx_usuario (id_usuario),
                INDEX idx_pergunta (id_pergunta)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
        ];

        foreach ($queries as $query) {
            $this->conn->exec($query);
        }

        $this->insertInitialData();
    }

    private function insertInitialData() {
        $stmt = $this->conn->query("SELECT COUNT(*) as count FROM usuarios");
        $result = $stmt->fetch();
        
        if ($result['count'] == 0) {
            $users = [
                ['admin', 'Administrador', 'admin@test.com', password_hash('admin123', PASSWORD_DEFAULT)],
                ['user', 'Usuario Comum', 'user@test.com', password_hash('user123', PASSWORD_DEFAULT)],
                ['user', 'Pedro', 'pedro@test.com', password_hash('pedro123', PASSWORD_DEFAULT)]
            ];

            $stmt = $this->conn->prepare("INSERT INTO usuarios (tipo, nome, email, senha) VALUES (?, ?, ?, ?)");
            foreach ($users as $user) {
                $stmt->execute($user);
            }

            $perguntas = [
                ['texto', 'Qual é a cor do céu?', '', 'Azul'],
                ['multipla_escolha', 'Qual é a capital do Brasil?', 'Rio de Janeiro,São Paulo,Brasília,Salvador', 'Brasília'],
                ['texto', 'Quem descobriu o Brasil', '', 'Pedro Álvares Cabral'],
                ['multipla_escolha', 'Qual a Capital do Brasil', 'São Paulo,Brasília,Rio de Janeiro', 'Brasília'],
                ['multipla_escolha', 'Qual o seu nome', 'Roberto,Carlos,Pedro,Juliano,Rocha', 'Juliano'],
                ['texto', 'Qual a cor da porta', '', 'Verde'],
                ['multipla_escolha', 'Qual é a cor?', 'azul,verde,vermelho,preto,branco', 'branco']
            ];

            $stmt = $this->conn->prepare("INSERT INTO perguntas (tipo, descricao, opcoes, correta) VALUES (?, ?, ?, ?)");
            foreach ($perguntas as $pergunta) {
                $stmt->execute($pergunta);
            }

            $respostas = [
                [2, 1, 'Azul'],
                [2, 2, 'Brasília'],
                [3, 1, 'Verde'],
                [1, 1, 'verde'],
                [2, 1, 'Preto']
            ];

            $stmt = $this->conn->prepare("INSERT INTO respostas (id_usuario, id_pergunta, resposta_dada) VALUES (?, ?, ?)");
            foreach ($respostas as $resposta) {
                $stmt->execute($resposta);
            }
        }
    }
}

function getDbConnection() {
    static $db = null;
    if ($db === null) {
        $db = new Database();
        try {
            return $db->getConnection();
        } catch (Exception $e) {
            if ($db->createDatabase()) {
                return $db->getConnection();
            }
            throw $e;
        }
    }
    return $db->getConnection();
}
?>