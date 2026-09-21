<?php
$host = 'localhost';
$dbname = 'sistema_solicitacoes';
$user = 'root';
$pass = 'Senai@118'; // Senha MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    if (pathinfo($_SERVER['PHP_SELF'], PATHINFO_EXTENSION) === 'php') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Erro ao conectar com o banco de dados: ' . $e->getMessage()]);
        exit;
    }
}