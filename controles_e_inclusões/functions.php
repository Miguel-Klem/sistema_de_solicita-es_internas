<?php
// Funções utilitárias, usadas em várias páginas do sistema

// Remove espaços extras e tags HTML de um texto vindo de formulário (proteção básica contra XSS)
function clean_input($data) {
    $data = trim($data);
    $data = strip_tags($data);
    return $data;
}

// Redireciona o usuário para outra página e encerra o script atual
function redirect($url) {
    header("Location: $url");
    exit;
}