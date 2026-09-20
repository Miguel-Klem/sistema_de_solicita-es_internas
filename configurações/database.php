<?php

// Configurações de acesso ao banco de dados.

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '94264221118'); // senha do MySQL
define('DB_NAME', 'laravel');

// Conexão com o banco mysqli (orientado a objeto)
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Se conexão falhar exibe o erro
if ($conn->connect_error) {
    die('Erro na conexão com o banco: ' . $conn->connect_error);
}

// UTF-8
$conn->set_charset('utf8mb4');