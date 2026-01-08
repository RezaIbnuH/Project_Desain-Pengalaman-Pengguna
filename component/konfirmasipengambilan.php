<?php
include_once '../config/database.php';

// Initialize the variable for the searched item
$barang = [];

if (isset($_GET['kode_barang'])) {
    $kode_barang = $_GET['kode_barang'];

    try {
        // Query to get barang data based on kode_barang
        $query = "SELECT * FROM barang WHERE kode_barang = :kode_barang";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':kode_barang', $kode_barang);
        $stmt->execute();
        $barang = $stmt->fetch(PDO::FETCH_ASSOC); // Get barang data for display

        // If no data found, alert the user
        if (!$barang) {
            echo "<script>alert('Barang tidak ditemukan');</script>";
        }

    } catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}

// Handle barang taken confirmation
if (isset($_POST['confirm_pengambilan'])) {
    try {
        // Insert data into pengambilan table
        $insertQuery = "INSERT INTO pengambilan (kode_barang, nama_pengambil, tanggal_pengambilan, jam_pengambilan, petugas_id) 
                        VALUES (:kode_barang, :nama_pengambil, NOW(), NOW(), :petugas_id)";
        $stmt = $pdo->prepare($insertQuery);
        $stmt->bindParam(':kode_barang', $kode_barang);
        $stmt->bindParam(':nama_pengambil', $_POST['nama_pengambil']);
        $stmt->bindParam(':petugas_id', $barang['petugas_id']); // assuming petugas_id is taken from barang
        $stmt->execute();

        // Update status in barang table to 'Diambil'
        $updateQuery = "UPDATE barang SET status = 'Diambil' WHERE kode_barang = :kode_barang";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->bindParam(':kode_barang', $kode_barang);
        $stmt->execute();

        echo "<script>alert('Barang berhasil diambil!'); window.location.href = 'barangkeluar.php';</script>";
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
    <title>Konfirmasi Pengambilan Barang</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <a href="#" class="navbar-logo">
            <img src="../css/assets/logo.png" alt="Logo Titip Aman" class="logo-img">
        </a>
        <div class="navbar-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="barangmasuk.php">Barang Masuk</a>
            <a href="barangkeluar.php">Barang Keluar</a>
            <a href="riwayat.php">Riwayat</a>
        </div>
        <div class="navbar-extra">
            <a href="profil.php">Profil</a>
            <a href="../auth/logout.php">Logout</a>
        </div>
    </nav>

    <!-- Konfirmasi Pengambilan Barang -->
    <div class="form-pencarian">
        <?php if ($barang): ?>
            <h2>Konfirmasi Pengambilan Barang</h2>
            <p><strong>Kode Barang:</strong> <?php echo htmlspecialchars($barang['kode_barang']); ?></p>
            <p><strong>Nama Penitip:</strong> <?php echo isset($barang['nama_penitip']) ? htmlspecialchars($barang['nama_penitip']) : 'Tidak Tersedia'; ?></p>
            <p><strong>Nama Barang:</strong> <?php echo isset($barang['nama_barang']) ? htmlspecialchars($barang['nama_barang']) : 'Tidak Tersedia'; ?></p>
            <p><strong>Tanggal Masuk:</strong> <?php echo isset($barang['tanggal_masuk']) ? htmlspecialchars($barang['tanggal_masuk']) : 'Tidak Tersedia'; ?></p>
            <p><strong>Jam Masuk:</strong> <?php echo isset($barang['jam_masuk']) ? htmlspecialchars($barang['jam_masuk']) : 'Tidak Tersedia'; ?></p>
            <p><strong>Petugas:</strong> <?php echo isset($barang['petugas_id']) ? htmlspecialchars($barang['petugas_id']) : 'Tidak Tersedia'; ?></p>

            <form method="POST">
                <label for="nama_pengambil">Nama Pengambil:</label>
                <input type="text" id="nama_pengambil" name="nama_pengambil" required>
                <button type="submit" name="confirm_pengambilan" class="konfirmasi-button">Konfirmasi Pengambilan</button>
            </form>
        <?php else: ?>
            <p>Barang tidak ditemukan.</p>
        <?php endif; ?>
    </div>

    <!-- Waktu Realtime -->
    <div class="waktu-realtime">
        <p><strong>Waktu Sekarang:</strong> <span id="realtime-waktu"></span></p>
    </div>

    <footer class="footer">
        <div class="links">
            <a href="#dashboard">Dashboard</a>
            <a href="#masukkanbarang">Barang Masuk</a>
            <a href="#barangkeluar">Barang Keluar</a>
            <a href="#riwayat">Riwayat</a>
        </div>
        <div class="credits">
            <p>Created by <a href="#">Reza Ibnu Hanifa</a>. | &copy; 2025 Titip Aman</p>
        </div>
    </footer>

    <script>
        // Update time every second to show real-time
        function updateTime() {
            const waktuElement = document.getElementById("realtime-waktu");
            const currentDateTime = new Date();
            const formattedTime = currentDateTime.toLocaleString(); // Format waktu sesuai preferensi lokal
            waktuElement.textContent = formattedTime;
        }

        // Call the updateTime function every 1000 milliseconds (1 second)
        setInterval(updateTime, 1000);

        // Initial call to set the time when the page is loaded
        updateTime();
    </script>
</body>
</html>
