<?php
require_once '../config/database.php'; // Make sure the database connection is correct

// $email = 'admin@admin.com';
// $passwordBaru = 'admin123';

// Hash the password
$hash = password_hash($passwordBaru, PASSWORD_DEFAULT);

// Prepare and execute the query to update the password
try {
    $stmt = $pdo->prepare("UPDATE petugas SET password_petugas = :hash WHERE email_petugas = :email");
    $stmt->execute([
        ':hash' => $hash,
        ':email' => $email
    ]);

    // Check if any row was updated
    if ($stmt->rowCount() > 0) {
        echo "Password sudah di-hash untuk $email";
    } else {
        echo "Email tidak ditemukan atau password sudah terupdate.";
    }
} catch (PDOException $e) {
    // Handle any errors that occur
    echo "Terjadi kesalahan: " . $e->getMessage();
}
