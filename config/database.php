<?php
// config/database.php

$host = 'localhost';   // Host database
$username = 'root';     // Username database
$password = '';         // Password database
$database = 'dpp'; // Nama database

try {
    // Membuat koneksi ke database dengan menggunakan PDO
    $pdo = new PDO(
        "mysql:host=$host;dbname=$database;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  // Mengaktifkan mode exception untuk error
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Pengaturan default fetch mode
            PDO::ATTR_EMULATE_PREPARES => false, // Disable emulasi prepare untuk keamanan
        ]
    );

    // echo "Koneksi Berhasil!";

} catch (PDOException $e) {

    error_log("Koneksi ke database gagal: " . $e->getMessage());
    die("Koneksi database gagal: " . $e->getMessage());
}
?>
