<?php
// Encerra a sessão do usuário e volta para o login
require_once __DIR__ . '/../controles_e_inclusões/auth.php';

session_unset();   // remove todas as variáveis da sessão
session_destroy(); // destrói a sessão em si

redirect('login.php');