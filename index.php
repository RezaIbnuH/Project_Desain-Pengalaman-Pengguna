<?php
session_start();

// Jika sudah login, arahkan ke dashboard
if (!empty($_SESSION['user_id'])) {
    header('Location: component/dashboard.php');
    exit;
}

// Jika belum login, arahkan ke halaman login
header('Location: auth/login.php');
exit;
?>
