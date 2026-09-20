-- Script de criação do banco e das tabelas (usuários e departamentos)

CREATE DATABASE IF NOT EXISTS laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE laravel;

-- Tabela de departamentos (setores da empresa)
CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    active BOOLEAN NOT NULL DEFAULT TRUE
);

-- Tabela de usuários
-- profile controla o que o usuário pode fazer: user (comum), agent (atendente), admin (administrador)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    department_id INT NULL,
    profile ENUM('user', 'agent', 'admin') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- Alguns departamentos de exemplo, só para você conseguir testar o cadastro sem precisar criar nada na mão
INSERT INTO departments (name) VALUES ('TI'), ('Administrativo'), ('Financeiro');