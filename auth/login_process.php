<?php
session_start();
require_once '../config/database.php';

if (!isset($pdo)) {
    die("Database connection failed.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        empty($_POST['csrf_token']) ||
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        // Jangan kasih detail ke user
        header('Location: login.php?error=csrf');
        exit;
    }

    // Form kamu pakai name="username" tapi isinya email
    $email    = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($email === '' || $password === '') {
        header('Location: login.php?error=empty');
        exit;
    }

    // ✅ validasi format email (karena input type email)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: login.php?error=invalid_email');
        exit;
    }

    // ✅ Sesuaikan kolom tabel kamu
    $stmt = $pdo->prepare('
        SELECT id_petugas, nama_petugas, email_petugas, password_petugas
        FROM petugas
        WHERE email_petugas = :email
        LIMIT 1
    ');
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // ✅ Cek password hash
    if ($user && password_verify($password, $user['password_petugas'])) {
        // (opsional) regenerasi session id biar aman
        session_regenerate_id(true);

        $_SESSION['user_id']   = $user['id_petugas'];
        $_SESSION['email']     = $user['email_petugas'];
        $_SESSION['nama']      = $user['nama_petugas'];

        header('Location: ../component/dashboard.php');
        exit;
    }

    header('Location: login.php?error=invalid_credentials');
    exit;
}
?>