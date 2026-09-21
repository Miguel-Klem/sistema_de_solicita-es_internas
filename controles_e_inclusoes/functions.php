<?php
require_once __DIR__ . '/../configuracoes/database.php';

// CA01 / RN01 / RN02: Cria solicitação com status Aberto e gera SOL-XXXXXX atômico.

function create_request(PDO $pdo, string $title, string $description, int $category_id, string $priority, int $user_id): string {
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            INSERT INTO requests (title, description, category_id, priority, status, created_by, created_at)
            VALUES (:title, :description, :category_id, :priority, 'Aberto', :created_by, NOW())
        ");
        $stmt->execute([
            ':title'       => $title,
            ':description' => $description,
            ':category_id' => $category_id,
            ':priority'    => $priority,
            ':created_by'  => $user_id
        ]);

        $requestId = $pdo->lastInsertId();
        $requestNumber = sprintf('SOL-%06d', $requestId);

        $updateStmt = $pdo->prepare("UPDATE requests SET request_number = :req_num WHERE id = :id");
        $updateStmt->execute([':req_num' => $requestNumber, ':id' => $requestId]);

        // Histórico de criação
        $histStmt = $pdo->prepare("
            INSERT INTO request_history (request_id, action, old_value, new_value, changed_by, created_at)
            VALUES (:req_id, 'Criação', NULL, 'Aberto', :changed_by, NOW())
        ");
        $histStmt->execute([':req_id' => $requestId, ':changed_by' => $user_id]);

        $pdo->commit();
        return $requestNumber;
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * CA02 / RN05 / RN06 / CA04: Atualiza status ou responsável registrando histórico em transação.
 */
function update_request(PDO $pdo, int $requestId, array $user, ?string $newStatus, ?int $newAssignedTo): bool {
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            SELECT r.*, c.department_id 
            FROM requests r 
            JOIN categories c ON r.category_id = c.id 
            WHERE r.id = :id FOR UPDATE
        ");
        $stmt->execute([':id' => $requestId]);
        $request = $stmt->fetch();

        if (!$request) {
            throw new Exception("Solicitação não encontrada.");
        }

        // CA04 / RN07: Impedir alteração de concluídos por usuário comum
        if ($request['status'] === 'Concluído' && $user['profile'] === 'usuario') {
            throw new Exception("Solicitações concluídas não podem ser alteradas por usuários comuns.");
        }

        // RN05: Apenas Atendentes ou Administradores podem alterar o responsável ou status
        if ($user['profile'] === 'usuario') {
            throw new Exception("Usuários comuns não têm permissão para atualizar status/atribuição.");
        }

        // Atendentes só alteram chamados do seu próprio departamento
        if ($user['profile'] === 'atendente' && $request['department_id'] != $user['department_id']) {
            throw new Exception("Atendentes só podem alterar solicitações do seu departamento.");
        }

        // Modificação de Status
        if ($newStatus && $newStatus !== $request['status']) {
            $upStatus = $pdo->prepare("UPDATE requests SET status = :status WHERE id = :id");
            $upStatus->execute([':status' => $newStatus, ':id' => $requestId]);

            $histStatus = $pdo->prepare("
                INSERT INTO request_history (request_id, action, old_value, new_value, changed_by, created_at)
                VALUES (:req_id, 'Alteração de Status', :old_val, :new_val, :changed_by, NOW())
            ");
            $histStatus->execute([
                ':req_id'     => $requestId,
                ':old_val'    => $request['status'],
                ':new_val'    => $newStatus,
                ':changed_by' => $user['id']
            ]);
        }

        // Modificação de Atribuição
        if ($newAssignedTo !== null && $newAssignedTo != $request['assigned_to']) {
            $upAssign = $pdo->prepare("UPDATE requests SET assigned_to = :assigned WHERE id = :id");
            $upAssign->execute([':assigned' => $newAssignedTo, ':id' => $requestId]);

            $histAssign = $pdo->prepare("
                INSERT INTO request_history (request_id, action, old_value, new_value, changed_by, created_at)
                VALUES (:req_id, 'Alteração de Responsável', :old_val, :new_val, :changed_by, NOW())
            ");
            $histAssign->execute([
                ':req_id'     => $requestId,
                ':old_val'    => (string)$request['assigned_to'],
                ':new_val'    => (string)$newAssignedTo,
                ':changed_by' => $user['id']
            ]);
        }

        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * RN03 / RN04: Consulta solicitações respeitando escopo por perfil e filtros.
 */
function get_requests(PDO $pdo, array $user, array $filters = []): array {
    $sql = "
        SELECT r.*, c.name as category_name, d.name as department_name, 
               u_create.name as creator_name, u_assign.name as assignee_name
        FROM requests r
        JOIN categories c ON r.category_id = c.id
        JOIN departments d ON c.department_id = d.id
        JOIN users u_create ON r.created_by = u_create.id
        LEFT JOIN users u_assign ON r.assigned_to = u_assign.id
        WHERE 1=1
    ";
    $params = [];

    // RN03: Usuários comuns visualizam apenas o que criaram
    if ($user['profile'] === 'usuario') {
        $sql .= " AND r.created_by = :created_by";
        $params[':created_by'] = $user['id'];
    } 
    // RN04: Atendentes visualizam do seu departamento
    elseif ($user['profile'] === 'atendente') {
        $sql .= " AND c.department_id = :dept_id";
        $params[':dept_id'] = $user['department_id'];
    }

    // Filtros de busca
    if (!empty($filters['status'])) {
        $sql .= " AND r.status = :status";
        $params[':status'] = $filters['status'];
    }
    if (!empty($filters['priority'])) {
        $sql .= " AND r.priority = :priority";
        $params[':priority'] = $filters['priority'];
    }
    if (!empty($filters['department_id'])) {
        $sql .= " AND c.department_id = :filter_dept";
        $params[':filter_dept'] = $filters['department_id'];
    }

    $sql .= " ORDER BY r.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}