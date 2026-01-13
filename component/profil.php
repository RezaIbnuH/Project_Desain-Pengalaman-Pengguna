<?php
// Auth and database
include_once '../auth/check_auth.php';
require_login();
include_once '../config/database.php';

// Ambil id petugas dari session
$id_petugas = $_SESSION['user_id'];

// Ambil data profil petugas berdasarkan id_petugas
$query = "SELECT * FROM petugas p LEFT JOIN profil_petugas pp ON p.id_petugas = pp.id_petugas WHERE p.id_petugas = :id_petugas";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':id_petugas', $id_petugas, PDO::PARAM_INT);
$stmt->execute();
$profil = $stmt->fetch(PDO::FETCH_ASSOC);

// Determine which profile photo to display (server-side check)
$displayPhoto = '../css/assets/logo.png'; // fallback
if (!empty($profil['foto_profile'])) {
    $candidate = __DIR__ . '/../uploads/' . $profil['foto_profile'];
    if (is_file($candidate)) {
        $displayPhoto = '../uploads/' . rawurlencode($profil['foto_profile']);
    }
}

// Update profil petugas
if (isset($_POST['update_profil'])) {
    $alamat = isset($_POST['alamat']) ? trim($_POST['alamat']) : null;

    // Ambil nama file lama, jika ada
    $currentFile = $profil['foto_profile'] ?? null;
    $newFileName = $currentFile;

    // Proses upload jika ada file
    if (!empty($_FILES['foto_profile']) && $_FILES['foto_profile']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['foto_profile']['tmp_name'];
        $fileName = $_FILES['foto_profile']['name'];
        $fileSize = $_FILES['foto_profile']['size'];

        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];

        if (!in_array($ext, $allowed)) {
            echo "<script>alert('Tipe file tidak diizinkan. Hanya JPG/PNG/GIF.');</script>";
        } elseif ($fileSize > 2 * 1024 * 1024) { // batas 2MB
            echo "<script>alert('Ukuran file terlalu besar (maks 2MB).');</script>";
        } else {
            $uploadsDir = __DIR__ . '/../uploads/';
            if (!is_dir($uploadsDir)) {
                mkdir($uploadsDir, 0755, true);
            }

            // Nama file unik untuk mencegah konflik
            $newFileName = $id_petugas . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $targetPath = $uploadsDir . $newFileName;

            if (move_uploaded_file($fileTmp, $targetPath)) {
                // Hapus file lama jika berbeda
                if ($currentFile && $currentFile !== $newFileName) {
                    $oldPath = $uploadsDir . $currentFile;
                    if (is_file($oldPath)) {
                        @unlink($oldPath);
                    }
                }
            } else {
                echo "<script>alert('Gagal mengunggah file.');</script>";
                $newFileName = $currentFile;
            }
        }
    }

    // Hapus baris profil lama (jika ada) untuk mencegah duplikat, lalu insert baru
    try {
        $stmtDel = $pdo->prepare('DELETE FROM profil_petugas WHERE id_petugas = :id_petugas');
        $stmtDel->bindParam(':id_petugas', $id_petugas, PDO::PARAM_INT);
        $stmtDel->execute();

        $queryInsert = "INSERT INTO profil_petugas (id_petugas, alamat, foto_profile) VALUES (:id_petugas, :alamat, :foto_profile)";
        $stmtInsert = $pdo->prepare($queryInsert);
        $stmtInsert->bindParam(':id_petugas', $id_petugas, PDO::PARAM_INT);
        $stmtInsert->bindParam(':alamat', $alamat);
        $stmtInsert->bindParam(':foto_profile', $newFileName);
        $stmtInsert->execute();
    } catch (Exception $e) {
        // jika ada error, log dan fallback
        error_log('Profil update error: ' . $e->getMessage());
    }

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
    <?php include_once __DIR__ . '/../includes/header.php'; ?>

    <h2>Profil Petugas</h2>

    <!-- Menampilkan profil petugas -->
    <div class="profil-container">
        <img src="<?php echo $displayPhoto; ?>" alt="Foto Profil" class="foto-profil" width="150">
        <div class="profile-details">
            <p><strong>Nama: </strong><?php echo htmlspecialchars($profil['nama_petugas']); ?></p>
            <p><strong>Email: </strong><?php echo htmlspecialchars($profil['email_petugas']); ?></p>
            <p><strong>Nomor Telepon: </strong><?php echo htmlspecialchars($profil['nomor_telepon']); ?></p>
            <p><strong>Alamat: </strong><?php echo htmlspecialchars($profil['alamat']); ?></p>
            <div class="btn-group" style="margin-top:12px;">
                <button class="btn btn-add" type="button" data-bs-toggle="modal" data-bs-target="#profileModal">Edit Profil</button>
                <button class="btn btn-confirm" type="button" data-bs-toggle="modal" data-bs-target="#passwordModal">Ganti Password</button>
            </div>
        </div>
    </div>

        <!-- Modal: Edit Profil -->
        <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="profileModalLabel">Update Profil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                        <label for="alamat" class="form-label">Alamat</label>
                                        <textarea name="alamat" id="alamat" class="form-control"><?php echo htmlspecialchars($profil['alamat']); ?></textarea>
                                </div>
                                <div class="mb-3">
                                        <label for="foto_profile" class="form-label">Foto Profil (JPG/PNG/GIF, maks 2MB)</label>
                                        <input class="form-control" type="file" name="foto_profile" accept="image/*">
                                </div>
                                <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" name="update_profil" class="btn btn-primary">Simpan</button>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Ganti Password -->
        <div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="passwordModalLabel">Ganti Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                                <div class="mb-3">
                                        <label for="old_password" class="form-label">Password Lama</label>
                                        <input type="password" name="old_password" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                        <label for="new_password" class="form-label">Password Baru</label>
                                        <input type="password" name="new_password" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                                        <input type="password" name="confirm_password" class="form-control" required>
                                </div>
                                <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" name="update_password" class="btn btn-primary">Simpan</button>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php include_once __DIR__ . '/../includes/footer.php'; ?>
    <?php include_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
