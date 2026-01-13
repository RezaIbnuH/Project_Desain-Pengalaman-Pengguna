<?php
include_once '../auth/check_auth.php';
require_login();
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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengambilan Barang</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include_once __DIR__ . '/../includes/header.php'; ?>

    <!-- Form Pencarian dan Konfirmasi Pengambilan -->
    <div class="form-pencarian">
        <input type="text" id="search-kode" placeholder="Masukkan Kode Barang untuk Pengambilan" required>
        <button onclick="searchBarang()">Cari Barang</button>
    </div>

    <!-- Menampilkan Data Barang yang Ditemukan -->
    <div class="form-pencarian" id="barang-info" style="display: none;">
        <?php if ($barang): ?>
            <p><strong>Kode Barang:</strong> <span id="kode"><?php echo htmlspecialchars($barang['kode_barang']); ?></span></p>
            <p><strong>Nama Penitip:</strong> <span id="nama_penitip"><?php echo isset($barang['nama_penitip']) ? htmlspecialchars($barang['nama_penitip']) : 'Tidak Tersedia'; ?></span></p>
            <p><strong>Nama Barang:</strong> <span id="nama_barang"><?php echo isset($barang['nama_barang']) ? htmlspecialchars($barang['nama_barang']) : 'Tidak Tersedia'; ?></span></p>
            <p><strong>Tanggal Masuk:</strong> <span id="tanggal_masuk"><?php echo isset($barang['tanggal_masuk']) ? htmlspecialchars($barang['tanggal_masuk']) : 'Tidak Tersedia'; ?></span></p>
            <p><strong>Jam Masuk:</strong> <span id="jam_masuk"><?php echo isset($barang['jam_masuk']) ? htmlspecialchars($barang['jam_masuk']) : 'Tidak Tersedia'; ?></span></p>
            <p><strong>Petugas:</strong> <span id="petugas_id"><?php echo isset($barang['petugas_id']) ? htmlspecialchars($barang['petugas_id']) : 'Tidak Tersedia'; ?></span></p>
            <div class="btn-group">
                <button type="button" class="btn btn-confirm" onclick="redirectToKonfirmasi()">Konfirmasi</button>
                <button type="button" class="btn btn-cancel" onclick="window.location.href='barangkeluar.php'">Batal</button>
            </div>
        <?php else: ?>
            <p>Barang tidak ditemukan.</p>
        <?php endif; ?>
    </div>

    <?php include_once __DIR__ . '/../includes/footer.php'; ?>

    <script>
        function searchBarang() {
            const searchKode = document.getElementById("search-kode").value.trim();
            if (searchKode) {
                // Redirect to the same page with the search query
                window.location.href = `barangkeluar.php?kode_barang=${searchKode}`;
            } else {
                alert("Kode Barang harus diisi");
            }
        }

        function redirectToKonfirmasi() {
            const kodeBarang = "<?php echo isset($barang['kode_barang']) ? $barang['kode_barang'] : ''; ?>"; // Get the kode_barang value from PHP
            if (kodeBarang) {
                window.location.href = `konfirmasipengambilan.php?kode_barang=${kodeBarang}`;
            } else {
                alert("Kode Barang tidak ditemukan!");
            }
        }

        // Display barang data after page load if the data exists
        window.onload = function() {
            const barangInfo = document.getElementById('barang-info');
            if (<?php echo json_encode($barang ? true : false); ?>) {
                barangInfo.style.display = "block";
            }
        }
    </script>
</body>
</html>
