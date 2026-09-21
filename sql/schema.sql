CREATE DATABASE IF NOT EXISTS sistema_solicitacoes 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE sistema_solicitacoes;

-- Tabela de Departamentos
CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Usuários
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    department_id INT DEFAULT NULL,
    profile ENUM('usuario', 'atendente', 'administrador') NOT NULL DEFAULT 'usuario',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Categorias vinculadas aos Departamentos
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    department_id INT NOT NULL,
    active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Solicitações
CREATE TABLE IF NOT EXISTS requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_number VARCHAR(20) UNIQUE NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    category_id INT NOT NULL,
    priority ENUM('Baixa', 'Média', 'Alta', 'Urgente') NOT NULL DEFAULT 'Média',
    status ENUM('Aberto', 'Em atendimento', 'Aguardando usuário', 'Concluído', 'Cancelado') NOT NULL DEFAULT 'Aberto',
    created_by INT NOT NULL,
    assigned_to INT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Histórico de Modificações
CREATE TABLE IF NOT EXISTS request_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    old_value VARCHAR(255) NULL,
    new_value VARCHAR(255) NULL,
    changed_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Carga inicial de testes
INSERT INTO departments (id, name) VALUES
(1, 'Tecnologia da Informação'),
(2, 'Recursos Humanos'),
(3, 'Financeiro');

INSERT INTO categories (name, department_id) VALUES
('Suporte a Hardware', 1),
('Acesso a Sistemas', 1),
('Folha de Pagamento', 2),
('Reembolso de Despesas', 3);

-- Senha padrão para os 3 usuários abaixo: 123456
INSERT INTO users (name, email, password, department_id, profile) VALUES
('Admin Sistema', 'admin@empresa.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1152d2B3vEIn/J0XyR.YvTqj4Z3pBJe', 1, 'administrador'),
('Atendente TI', 'ti@empresa.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1152d2B3vEIn/J0XyR.YvTqj4Z3pBJe', 1, 'atendente'),
('Solicitante Comum', 'usuario@empresa.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1152d2B3vEIn/J0XyR.YvTqj4Z3pBJe', 2, 'usuario');