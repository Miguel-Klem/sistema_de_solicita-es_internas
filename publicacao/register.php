<?php
require_once __DIR__ . '/../configuracoes/database.php';

$error = '';
$success = '';

$departments = $pdo->query("SELECT * FROM departments WHERE active = 1")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $department_id = !empty($_POST['department_id']) ? (int)$_POST['department_id'] : null;

    if ($name && $email && $password) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        try {
            $stmt = $pdo->prepare("
                INSERT INTO users (name, email, password, department_id, profile)
                VALUES (:name, :email, :password, :department_id, 'usuario')
            ");
            $stmt->execute([
                ':name'          => $name,
                ':email'         => $email,
                ':password'      => $hash,
                ':department_id' => $department_id
            ]);
            $success = "Conta criada com sucesso! <a href='login.php'>Clique aqui para entrar</a>";
        } catch (PDOException $e) {
            $error = "Erro ao cadastrar: e-mail já em uso ou dados inválidos.";
        }
    } else {
        $error = "Preencha todos os campos obrigatórios.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Sistema de Solicitações</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center vh-100">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h4 class="card-title text-center mb-4">Novo Cadastro</h4>
                    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nome Completo</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">E-mail Corporativo</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Senha</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Departamento</label>
                            <select name="department_id" class="form-select">
                                <option value="">Selecione...</option>
                                <?php foreach ($departments as $d): ?>
                                    <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Criar Conta</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>