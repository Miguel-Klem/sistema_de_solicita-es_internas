<?php
require_once __DIR__ . '/../configuracoes/database.php';
require_once __DIR__ . '/../controles_e_inclusoes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user'] = [
                'id'            => $user['id'],
                'name'          => $user['name'],
                'email'         => $user['email'],
                'profile'       => $user['profile'],
                'department_id' => $user['department_id']
            ];
            header('Location: index.php');
            exit;
        } else {
            $error = "E-mail ou senha inválidos.";
        }
    } else {
        $error = "Preencha todos os campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema de Solicitações</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center vh-100">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h4 class="card-title text-center mb-4">Acesso ao Sistema</h4>
                    <?php if ($error): ?>
                        <div class="alert alert-danger mb-3"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">E-mail</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Senha</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Entrar</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="register.php">Cadastrar-se</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>