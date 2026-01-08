<?php
// Menghubungkan ke database
include_once '../config/database.php';
session_start();

// Pastikan petugas sudah login (cek sesi atau session)
// Ambil data profil petugas berdasarkan id_petugas
$query = "SELECT * FROM petugas p LEFT JOIN profil_petugas pp ON p.id_petugas = pp.id_petugas WHERE p.id_petugas = :id_petugas";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':id_petugas', $id_petugas, PDO::PARAM_INT);
$stmt->execute();
$profil = $stmt->fetch(PDO::FETCH_ASSOC);

// Update profil petugas
if (isset($_POST['update_profil'])) {
    $alamat = $_POST['alamat'];
    $foto_profile = $_FILES['foto_profile']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($foto_profile);

    if ($foto_profile) {
        move_uploaded_file($_FILES['foto_profile']['tmp_name'], $target_file);
    }

    $queryUpdate = "UPDATE profil_petugas SET alamat = :alamat, foto_profile = :foto_profile WHERE id_petugas = :id_petugas";
    $stmtUpdate = $pdo->prepare($queryUpdate);
    $stmtUpdate->bindParam(':alamat', $alamat);
    $stmtUpdate->bindParam(':foto_profile', $foto_profile);
    $stmtUpdate->bindParam(':id_petugas', $id_petugas);
    $stmtUpdate->execute();

    echo "<script>alert('Profil berhasil diperbarui!'); window.location.href='profil.php';</script>";
}

// Ganti password petugas
if (isset($_POST['update_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Verifikasi password lama
    $queryPassword = "SELECT password_petugas FROM petugas WHERE id_petugas = :id_petugas";
    $stmtPassword = $pdo->prepare($queryPassword);
    $stmtPassword->bindParam(':id_petugas', $id_petugas);
    $stmtPassword->execute();
    $row = $stmtPassword->fetch(PDO::FETCH_ASSOC);

    if (password_verify($old_password, $row['password_petugas'])) {
        if ($new_password == $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update password
            $queryUpdatePassword = "UPDATE petugas SET password_petugas = :password WHERE id_petugas = :id_petugas";
            $stmtUpdatePassword = $pdo->prepare($queryUpdatePassword);
            $stmtUpdatePassword->bindParam(':password', $hashed_password);
            $stmtUpdatePassword->bindParam(':id_petugas', $id_petugas);
            $stmtUpdatePassword->execute();

            echo "<script>alert('Password berhasil diperbarui!'); window.location.href='profil.php';</script>";
        } else {
            echo "<script>alert('Password baru dan konfirmasi password tidak cocok!');</script>";
        }
    } else {
        echo "<script>alert('Password lama salah!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Petugas</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="dashboard.php">Dashboard</a>
        <a href="profil.php">Profil</a>
        <a href="../auth/logout.php">Logout</a>
    </nav>

    <h2>Profil Petugas</h2>

    <!-- Menampilkan profil petugas -->
    <div class="profil-container">
        <img src="uploads/<?php echo $profil['foto_profile']; ?>" alt="Foto Profil" class="foto-profil" width="150">
        <p><strong>Nama: </strong><?php echo $profil['nama_petugas']; ?></p>
        <p><strong>Email: </strong><?php echo $profil['email_petugas']; ?></p>
        <p><strong>Nomor Telepon: </strong><?php echo $profil['nomor_telepon']; ?></p>
        <p><strong>Alamat: </strong><?php echo $profil['alamat']; ?></p>
    </div>

    <!-- Form untuk mengupdate profil -->
    <h3>Update Profil</h3>
    <form method="POST" enctype="multipart/form-data">
        <label for="alamat">Alamat:</label>
        <textarea name="alamat" id="alamat"><?php echo $profil['alamat']; ?></textarea><br>

        <label for="foto_profile">Foto Profil:</label>
        <input type="file" name="foto_profile"><br>

        <button type="submit" name="update_profil">Update Profil</button>
    </form>

    <!-- Form untuk mengganti password -->
    <h3>Ganti Password</h3>
    <form method="POST">
        <label for="old_password">Password Lama:</label>
        <input type="password" name="old_password" required><br>

        <label for="new_password">Password Baru:</label>
        <input type="password" name="new_password" required><br>

        <label for="confirm_password">Konfirmasi Password Baru:</label>
        <input type="password" name="confirm_password" required><br>

        <button type="submit" name="update_password">Ganti Password</button>
    </form>
</body>
</html>
