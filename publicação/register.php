<?php
// Página de cadastro de usuários
require_once __DIR__ . '/../configurações/database.php';
require_once __DIR__ . '/../controles_e_inclusões/functions.php';

$errors = [];

// Só processa o formulário quando ele for enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Limpa os dados recebidos do formulário
    $name = clean_input($_POST['name']);
    $email = clean_input($_POST['email']);
    $password = $_POST['password']; // senha não passa por clean_input para não alterar caracteres
    $department_id = (int) $_POST['department_id'];

    // Validações simples
    if (empty($name) || empty($email) || empty($password)) {
        $errors[] = 'Preencha todos os campos.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'A senha deve ter pelo menos 6 caracteres.';
    }

    // Se não houver erros até aqui, verifica se o e-mail já está cadastrado
    if (empty($errors)) {
        $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = 'Este e-mail já está cadastrado.';
        }
        $stmt->close();
    }

    // Se ainda não houver erros, cadastra o usuário
    if (empty($errors)) {
        // Nunca salvamos a senha em texto puro: usamos hash
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Todo cadastro por aqui entra como perfil "user" (comum);
        // atendentes e administradores são promovidos depois, direto no banco ou por um admin
        $stmt = $conn->prepare(
            'INSERT INTO users (name, email, password, department_id, profile) VALUES (?, ?, ?, ?, "user")'
        );
        $stmt->bind_param('sssi', $name, $email, $hashed_password, $department_id);
        $stmt->execute();
        $stmt->close();

        redirect('login.php?registered=1');
    }
}

// Busca os departamentos para exibir no select do formulário
$departments = $conn->query('SELECT id, name FROM departments WHERE active = 1');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Case Fácil</title>
</head>
<body>
    <h1>Criar conta</h1>

    <?php foreach ($errors as $error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endforeach; ?>

    <form method="post" action="register.php">
        <label>Nome:</label><br>
        <input type="text" name="name" required><br><br>

        <label>E-mail:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Senha:</label><br>
        <input type="password" name="password" required><br><br>

        <label>Departamento:</label><br>
        <select name="department_id" required>
            <?php while ($dept = $departments->fetch_assoc()): ?>
                <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <button type="submit">Cadastrar</button>
    </form>

    <p>Já tem conta? <a href="login.php">Entrar</a></p>
</body>
</html>