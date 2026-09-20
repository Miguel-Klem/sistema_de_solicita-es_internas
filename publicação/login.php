<?php
// Página de login
require_once __DIR__ . '/../configurações/database.php';
require_once __DIR__ . '/../controles_e_inclusões/functions.php';
require_once __DIR__ . '/../controles_e_inclusões/auth.php';

// Se já estiver logado, não faz sentido ver a tela de login de novo
if (is_logged_in()) {
    redirect('index.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean_input($_POST['email']);
    $password = $_POST['password'];

    // Busca o usuário pelo e-mail
    $stmt = $conn->prepare('SELECT id, name, password, profile FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    // password_verify compara a senha digitada com o hash salvo no banco
    if ($user && password_verify($password, $user['password'])) {
        // Guarda os dados essenciais na sessão para usar nas outras páginas
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_profile'] = $user['profile'];

        redirect('index.php');
    } else {
        $errors[] = 'E-mail ou senha inválidos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Case Fácil</title>
</head>
<body>
    <h1>Entrar</h1>

    <?php if (isset($_GET['registered'])): ?>
        <p style="color:green;">Cadastro realizado! Faça login.</p>
    <?php endif; ?>

    <?php foreach ($errors as $error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endforeach; ?>

    <form method="post" action="login.php">
        <label>E-mail:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Senha:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Entrar</button>
    </form>

    <p>Não tem conta? <a href="register.php">Cadastre-se</a></p>
</body>
</html>