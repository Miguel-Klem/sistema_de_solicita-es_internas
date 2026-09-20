<?php
// Página inicial - só acessível para quem está logado
require_once __DIR__ . '/../controles_e_inclusões/auth.php';
require_login();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Início - Case Fácil</title>
</head>
<body>
    <h1>Bem-vindo(a), <?= htmlspecialchars($_SESSION['user_name']) ?>!</h1>
    <p>Perfil: <?= htmlspecialchars($_SESSION['user_profile']) ?></p>

    <p><em>As telas de solicitações (criar, listar, atualizar status) entram nos próximos passos.</em></p>

    <p><a href="logout.php">Sair</a></p>
</body>
</html>