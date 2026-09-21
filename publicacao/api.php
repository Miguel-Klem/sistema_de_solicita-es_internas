<?php
header('Content-Type: application/json; charset=utf-8');

// Inclui a conexão existente e controle de sessão
require_once __DIR__ . '/../configuracoes/database.php';
require_once __DIR__ . '/../controles_e_inclusoes/auth.php';

$action = $_GET['action'] ?? '';

try {
    // 1. Dados do Usuário Logado
    if ($action === 'get_user_info') {
        echo json_encode([
            'success' => true,
            'user' => [
                'name' => $_SESSION['user_name'] ?? 'Usuário',
                'profile' => $_SESSION['user_profile'] ?? 'usuario'
            ]
        ]);
        exit;
    }

    // 2. Listar Categorias
    if ($action === 'get_categories') {
        $stmt = $pdo->query("SELECT id, name FROM categories WHERE active = 1 ORDER BY name ASC");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
        exit;
    }

    // 3. Listar Solicitações
    if ($action === 'get_requests') {
        $status = $_GET['status'] ?? '';
        $priority = $_GET['priority'] ?? '';

        $sql = "SELECT r.*, c.name as category_name, u.name as creator_name, a.name as assigned_name 
                FROM requests r
                LEFT JOIN categories c ON r.category_id = c.id
                LEFT JOIN users u ON r.created_by = u.id
                LEFT JOIN users a ON r.assigned_to = a.id
                WHERE 1=1";
        
        $params = [];
        if (!empty($status)) {
            $sql .= " AND r.status = :status";
            $params['status'] = $status;
        }
        if (!empty($priority)) {
            $sql .= " AND r.priority = :priority";
            $params['priority'] = $priority;
        }

        $sql .= " ORDER BY r.id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
        exit;
    }

    // 4. Criar Solicitação
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create_request') {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $title = trim($input['title'] ?? '');
        $description = trim($input['description'] ?? '');
        $category_id = intval($input['category_id'] ?? 0);
        $priority = $input['priority'] ?? 'Média';
        $created_by = $_SESSION['user_id'] ?? 1;

        if (empty($title) || empty($description) || $category_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Preencha todos os campos obrigatórios.']);
            exit;
        }

        $req_number = 'REQ-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        $stmt = $pdo->prepare("INSERT INTO requests (request_number, title, description, category_id, priority, created_by) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$req_number, $title, $description, $category_id, $priority, $created_by]);

        echo json_encode(['success' => true, 'message' => 'Solicitação enviada e registrada com sucesso!']);
        exit;
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}