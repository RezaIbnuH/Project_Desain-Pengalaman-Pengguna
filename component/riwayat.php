<?php
include_once '../config/database.php';

// Handle filter options
$whereClause = "WHERE 1=1 "; // Default condition to ensure query is valid
$startDate = "";
$endDate = "";
$search = "";

if (isset($_GET['filter'])) {
    if ($_GET['filter'] == 'day') {
        $whereClause .= "AND DATE(p.tanggal_pengambilan) = CURDATE()"; // Filter Hari Ini
    } elseif ($_GET['filter'] == 'month') {
        $whereClause .= "AND MONTH(p.tanggal_pengambilan) = MONTH(CURDATE()) AND YEAR(p.tanggal_pengambilan) = YEAR(CURDATE())"; // Filter Bulan Ini
    } elseif ($_GET['filter'] == 'year') {
        $whereClause .= "AND YEAR(p.tanggal_pengambilan) = YEAR(CURDATE())"; // Filter Tahun Ini
    }
}

if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $startDate = $_GET['start_date'];
    $endDate = $_GET['end_date'];
    $whereClause .= " AND p.tanggal_pengambilan BETWEEN :start_date AND :end_date"; // Filter based on date range
}

if (isset($_GET['search_box'])) {
    $search = $_GET['search_box'];
    $whereClause .= " AND (p.kode_barang LIKE :search OR b.nama_barang LIKE :search)"; // Search query
}

// Query to fetch only available items (status = 'Tersedia')
try {
    $query = "SELECT 
              p.id AS pengambilan_id,
              p.kode_barang,
              p.nama_pengambil,
              b.nama_penitip,
              b.nama_barang,
              p.tanggal_pengambilan,
              p.jam_pengambilan,
              p.petugas_id,
              b.status
          FROM 
              pengambilan p
          JOIN 
              barang b ON p.kode_barang = b.kode_barang " . $whereClause;

    $stmt = $pdo->prepare($query);

    // Binding parameters for date range and search query
    if ($startDate && $endDate) {
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
}

if ($search) {
    $search = "%$search%";
    $stmt->bindParam(':search', $search);
}


    $stmt->execute();
    $riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Barang Masuk/Keluar</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
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

    <div class="riwayat-container">
        <h1 class="riwayat-header">Riwayat Barang Masuk/Keluar</h1>

        <!-- Filter Section -->
        <div class="filter-section">
            <select id="filter-period" onchange="applyFilter()">
                <option value="all">Semua</option>
                <option value="day">Hari Ini</option>
                <option value="month">Bulan Ini</option>
                <option value="year">Tahun Ini</option>
            </select>

            <input type="date" id="start-date" placeholder="Tanggal Mulai">
            <input type="date" id="end-date" placeholder="Tanggal Selesai">
            <input type="text" id="search-box" placeholder="Cari Barang...">

            <button onclick="applyFilter()">Terapkan Filter</button>
        </div>

        <!-- Tabel Riwayat -->
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Pengambil</th>
                    <th>Nama Penitip</th>
                    <th>Nama Barang</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Petugas</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="riwayatTableBody">
                <?php if ($riwayat): ?>
                    <?php foreach ($riwayat as $index => $row): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($row['kode_barang']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_pengambil']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_penitip']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                            <td><?php echo htmlspecialchars($row['tanggal_pengambilan']); ?></td>
                            <td><?php echo htmlspecialchars($row['jam_pengambilan']); ?></td>
                            <td><?php echo htmlspecialchars($row['petugas_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align: center;">Tidak ada riwayat yang ditemukan</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Tombol Cetak -->
        <button class="print-button" onclick="window.print()">Cetak Riwayat</button>
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
        function applyFilter() {
            const filterPeriod = document.getElementById("filter-period").value;
            const startDate = document.getElementById("start-date").value;
            const endDate = document.getElementById("end-date").value;
            const searchBox = document.getElementById("search-box").value;

            let url = "riwayat.php?filter=" + filterPeriod;
            if (startDate) {
                url += "&start_date=" + startDate;
            }
            if (endDate) {
                url += "&end_date=" + endDate;
            }
            if (searchBox) {
                url += "&search_box=" + searchBox;
            }
            window.location.href = url;
        }
    </script>

</body>
</html>
