<?php
// Funções relacionadas a sessão (login) e controle de acesso por perfil

require_once __DIR__ . '/functions.php';

// Inicia a sessão, caso ainda não tenha sido iniciada nesta requisição
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Retorna true se existe um usuário logado na sessão atual
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Usada no topo de páginas que exigem login. Se não estiver logado, manda para o login
function require_login() {
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

// Usada em páginas restritas a certos perfis.
// Exemplo: require_role(['admin', 'agent']); permite só administrador e atendente
function require_role($allowed_profiles) {
    require_login();
    if (!in_array($_SESSION['user_profile'], $allowed_profiles)) {
        die('Acesso negado: seu perfil não tem permissão para acessar esta página.');
    }
}