<?php
// Database connection
include_once '../config/database.php';

// Initialize statistics variables
$totalBarangTersedia = 0;
$totalBarangMasuk = 0;
$totalBarangKeluar = 0;

// Function to generate random code
function generateRandomCode() {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $kode = '';
    for ($i = 0; $i < 8; $i++) {
        $kode .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $kode;
}

try {
    // Query to count available items
    $queryTersedia = "SELECT COUNT(*) FROM barang WHERE status = 'Tersedia'";  
    $stmtTersedia = $pdo->prepare($queryTersedia);
    $stmtTersedia->execute();
    $totalBarangTersedia = $stmtTersedia->fetchColumn();

    // Query to count incoming items
    $queryMasuk = "SELECT COUNT(*) FROM barang WHERE tanggal_masuk IS NOT NULL";  
    $stmtMasuk = $pdo->prepare($queryMasuk);
    $stmtMasuk->execute();
    $totalBarangMasuk = $stmtMasuk->fetchColumn();

    // Query to count outgoing items
    $queryKeluar = "SELECT COUNT(*) FROM pengambilan WHERE tanggal_pengambilan IS NOT NULL";  
    $stmtKeluar = $pdo->prepare($queryKeluar);
    $stmtKeluar->execute();
    $totalBarangKeluar = $stmtKeluar->fetchColumn();

    // Query to get the latest 10 items from the barang table
    $latestRows = [];
    $query = "SELECT * FROM barang ORDER BY tanggal_masuk DESC LIMIT 10";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $latestRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Penitipan Barang yang mudah dan aman untuk mengelola barang masuk dan keluar.">
    <meta name="author" content="Reza Ibnu Hanifa">
    <meta name="keywords" content="Penitipan Barang, Sistem Penitipan, Barang Masuk, Barang Keluar">
    <title>Dashboard - Titip Aman</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://unpkg.com/feather-icons"></script>
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

    <!-- Konten Dashboard -->
    <div class="dashboard-content">
        <div class="dashboard-left">
            <img src="../css/assets/dashboard.jpg" alt="Dashboard Image">
        </div>
        <div class="dashboard-right">
            <h1>Selamat Datang di <b>Sistem Penitipan Barang</b></h1>
        </div>
    </div>

    <!-- Statistik -->
    <div class="statistik">
        <h2>Statistik Hari Ini</h2>
        <div class="statistik-cards">
            <div class="menu-card">
                <h5 class="card-title">Total Barang Tersedia</h5>
                <p class="card-text"><?php echo $totalBarangTersedia; ?></p>
            </div>
            <div class="menu-card">
                <h5 class="card-title">Total Barang</h5>
                <p class="card-text"><?php echo $totalBarangMasuk; ?></p>
            </div>
            <div class="menu-card">
                <h5 class="card-title">Total Barang Keluar</h5>
                <p class="card-text"><?php echo $totalBarangKeluar; ?></p>
            </div>
        </div>
    </div>

    <!-- Form Pencarian -->
    <div class="search-form">
        <input type="search" id="search-box" placeholder="Cari Barang...">
        <label for="search-box"></label>
    </div>

    <!-- Info Terbaru -->
    <div class="info-baru">
        <h1 class="info-baru-title">Info Terbaru</h1>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Penitip</th>
                    <th>Nama Barang</th>
                    <th>Waktu</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($latestRows)): ?>
                <?php foreach ($latestRows as $i => $r): ?>
                <tr>
                    <td><?php echo $i + 1; ?></td>
                    <td><?php echo htmlspecialchars($r['kode_barang']); ?></td>
                    <td><?php echo htmlspecialchars($r['nama_penitip']); ?></td>
                    <td><?php echo htmlspecialchars($r['nama_barang']); ?></td>
                    <td><?php echo htmlspecialchars($r['tanggal_masuk']); ?></td>
                    <td><?php echo htmlspecialchars($r['status']); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">Tidak ada data terbaru.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
        <div class="button-group-dashboard">
            <button class="btn-lihat">Lihat Semua</button>
            <button class="btn-tambah">+ Tambah Barang</button>
            <button class="btn-konfirmasi">Konfirmasi Pengambilan</button>
        </div>
    </div>

    <!-- Footer -->
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

    <!-- JavaScript -->
    <script>
        // Fungsi untuk menampilkan dan menyembunyikan form
        function toggleForm() {
            const form = document.querySelector(".form-penambahan");
            if (form) {
                form.classList.toggle("hidden");
            }

            // Auto-generate kode barang saat form ditampilkan
            if (!document.getElementById('kode').value) {
                generateKodeBarang();
            }
        }

        // Fungsi untuk menghasilkan kode barang secara random
        function generateKodeBarang() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let kode = '';
            for (let i = 0; i < 8; i++) {
                kode += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById('kode').value = kode;
        }
    </script>
</body>
</html>
