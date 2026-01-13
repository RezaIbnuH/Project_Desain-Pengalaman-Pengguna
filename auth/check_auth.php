<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in() {
    return !empty($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        // Sesuaikan path jika aplikasi ditempatkan di folder berbeda
        header('Location: /projectdpp/auth/login.php?error=not_logged_in');
        exit;
    }
}

?>
