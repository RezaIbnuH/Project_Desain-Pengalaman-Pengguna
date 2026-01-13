<?php
include_once '../auth/check_auth.php';
require_login();
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
    <style>
        /* Modal overlay for printing preview */
        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        .overlay.show { display: flex; }
        .overlay-content {
            background: #fff;
            width: 90%;
            max-width: 1000px;
            max-height: 90vh;
            overflow: auto;
            padding: 20px;
            border-radius: 6px;
        }
        .overlay-controls { text-align: right; margin-bottom: 10px; }
        .overlay-controls button { margin-left: 8px; }

        /* Print only the .print-area when printing */
        @media print {
            body * { visibility: hidden; }
            .print-area, .print-area * { visibility: visible; }
            .print-area { position: absolute; left: 0; top: 0; width: 100%; }
        }
    </style>
</head>
<body>
    <?php include_once __DIR__ . '/../includes/header.php'; ?>

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

        <!-- Tombol Cetak (buka overlay) -->
        <button class="print-button" onclick="openPrintOverlay()">Cetak Riwayat</button>
    </div>

    <!-- Overlay untuk preview cetak -->
    <div id="printOverlay" class="overlay hidden" role="dialog" aria-hidden="true">
        <div class="overlay-content">
            <div class="overlay-controls">
                <button type="button" onclick="closePrintOverlay()">Tutup</button>
                <button type="button" onclick="printFromOverlay()">Cetak</button>
            </div>
            <div id="printArea" class="print-area">
                <!-- Konten riwayat akan disalin ke sini -->
            </div>
        </div>
    </div>
    </div>

    <?php include_once __DIR__ . '/../includes/footer.php'; ?>

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

        // Overlay print functions
        function openPrintOverlay() {
            const overlay = document.getElementById('printOverlay');
            const printArea = document.getElementById('printArea');
            // Clone the current table contents into the print area
            const table = document.querySelector('.riwayat-container table');
            if (table) {
                // Create a header for printed report
                const header = document.createElement('div');
                header.innerHTML = '<h2>Riwayat Barang</h2><p>Dicetak: ' + new Date().toLocaleString() + '</p>';
                // Clone table to avoid moving original
                const tableClone = table.cloneNode(true);
                printArea.innerHTML = '';
                printArea.appendChild(header);
                printArea.appendChild(tableClone);
                overlay.classList.remove('hidden');
                overlay.setAttribute('aria-hidden', 'false');
            } else {
                alert('Tabel riwayat tidak ditemukan.');
            }
        }

        function closePrintOverlay() {
            const overlay = document.getElementById('printOverlay');
            overlay.classList.add('hidden');
            overlay.setAttribute('aria-hidden', 'true');
        }

        function printFromOverlay() {
            // Gunakan print media rule untuk hanya mencetak .print-area
            window.print();
        }
    </script>

</body>
</html>
