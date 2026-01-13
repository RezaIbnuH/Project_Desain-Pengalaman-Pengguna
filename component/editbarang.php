<?php
// Auth and database
include_once '../auth/check_auth.php';
require_login();
include_once '../config/database.php';

// Menangani pencarian berdasarkan kode_barang
if (isset($_GET['kode_barang'])) {
    $kode_barang = $_GET['kode_barang'];
    
    try {
        // Query untuk mengambil data barang berdasarkan kode_barang
        $query = "SELECT * FROM barang WHERE kode_barang = :kode_barang";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':kode_barang', $kode_barang);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Memasukkan data ke dalam form jika ditemukan
        if ($row) {
            $kode_barang = $row['kode_barang'];
            $no_identitas = $row['no_identitas'];
            $nama_penitip = $row['nama_penitip'];
            $no_telepon = $row['no_telepon'];
            $nama_barang = $row['nama_barang'];
            $tanggal_masuk = $row['tanggal_masuk'];
            $jam_masuk = $row['jam_masuk'];
            $status = $row['status'];
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}

// Menangani update barang
if (isset($_POST['edit_barang'])) {
    $kode_barang = $_POST['kode_barang'];
    $no_telepon = $_POST['no_telepon'];  // Menambahkan No Telepon
    $status = $_POST['status']; // Mengupdate status

    try {
        // Query untuk mengupdate data barang
        $query = "UPDATE barang SET no_telepon = :no_telepon, status = :status 
                  WHERE kode_barang = :kode_barang";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':kode_barang', $kode_barang);
        $stmt->bindParam(':no_telepon', $no_telepon);
        $stmt->bindParam(':status', $status);

        // Menjalankan query untuk update data barang
        if ($stmt->execute()) {
            echo "<script>alert('Barang berhasil diperbarui'); window.location.href = 'barangmasuk.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan saat memperbarui data');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang - Titip Aman</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include_once __DIR__ . '/../includes/header.php'; ?>

    <!-- Form Edit Barang -->
    <div class="form-penambahan">
        <form method="POST" action="editbarang.php">
            <!-- Kolom yang tidak bisa diubah -->
            <input type="text" id="kode" name="kode_barang" placeholder="Kode Barang" value="<?php echo $kode_barang; ?>" required readonly>
            <input type="text" id="noIdentitas" name="no_identitas" placeholder="No Identitas (KTP)" value="<?php echo $no_identitas; ?>" readonly>
            
            <!-- Kolom yang bisa diedit -->
            <input type="text" id="namaPenitip" name="nama_penitip" placeholder="Nama Penitip" value="<?php echo $nama_penitip; ?>" readonly>
            <input type="text" id="namaBarang" name="nama_barang" placeholder="Nama Barang" value="<?php echo $nama_barang; ?>" readonly>
            
            <input type="date" id="tanggalMasuk" name="tanggal_masuk" value="<?php echo $tanggal_masuk; ?>" required readonly>
            <input type="time" id="jamMasuk" name="jam_masuk" value="<?php echo $jam_masuk; ?>" required readonly>

            <!-- Kolom No Telepon yang bisa diedit -->
            <input type="text" id="noTelepon" name="no_telepon" placeholder="No Telepon" value="<?php echo $no_telepon; ?>" required>

            <!-- Kolom Status yang bisa diedit -->
            <select id="status" name="status" required>
                <option value="Tersedia" <?php echo ($status == "Tersedia") ? 'selected' : ''; ?>>Tersedia</option>
                <option value="Diambil" <?php echo ($status == "Diambil") ? 'selected' : ''; ?>>Diambil</option>
                <option value="Diproses" <?php echo ($status == "Diproses") ? 'selected' : ''; ?>>Diproses</option>
                <option value="Dibatalkan" <?php echo ($status == "Dibatalkan") ? 'selected' : ''; ?>>Dibatalkan</option>
            </select>

            <div class="btn-group">
                <button type="submit" name="edit_barang" class="btn btn-confirm">Perbarui</button>
                <button type="button" class="btn btn-cancel" onclick="window.location.href='barangmasuk.php'">Batal</button>
            </div>
        </form>
    </div>

    <?php include_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
