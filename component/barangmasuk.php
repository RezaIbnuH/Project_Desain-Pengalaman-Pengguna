<?php
// Menghubungkan ke database
include_once '../config/database.php';

// Menangani penambahan barang baru
if (isset($_POST['add_barang'])) {
    $kode_barang = $_POST['kode_barang'];
    $no_identitas = $_POST['no_identitas'];
    $nama_penitip = $_POST['nama_penitip'];
    $no_telepon = $_POST['no_telepon'];
    $nama_barang = $_POST['nama_barang'];
    $tanggal_masuk = $_POST['tanggal_masuk'];
    $jam_masuk = $_POST['jam_masuk'];
    $petugas_id = $_POST['petugas']; // Petugas ID dari nama petugas
    $status = $_POST['status']; 

    try {
        $query = "INSERT INTO barang (kode_barang, no_identitas, nama_penitip, no_telepon, nama_barang, tanggal_masuk, jam_masuk, petugas_id, status)
                  VALUES (:kode_barang, :no_identitas, :nama_penitip, :no_telepon, :nama_barang, :tanggal_masuk, :jam_masuk, :petugas_id, :status)";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':kode_barang', $kode_barang);
        $stmt->bindParam(':no_identitas', $no_identitas);
        $stmt->bindParam(':nama_penitip', $nama_penitip);
        $stmt->bindParam(':no_telepon', $no_telepon);
        $stmt->bindParam(':nama_barang', $nama_barang);
        $stmt->bindParam(':tanggal_masuk', $tanggal_masuk);
        $stmt->bindParam(':jam_masuk', $jam_masuk);
        $stmt->bindParam(':petugas_id', $petugas_id);
        $stmt->bindParam(':status', $status);

        if ($stmt->execute()) {
            echo "<script>alert('Barang berhasil ditambahkan');</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan. Silakan coba lagi.');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}

// Menangani penghapusan barang
if (isset($_GET['hapus'])) {
    $kode_barang = $_GET['hapus'];
    
    try {
        $query = "DELETE FROM barang WHERE kode_barang = :kode_barang";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':kode_barang', $kode_barang);
        
        if ($stmt->execute()) {
            echo "<script>alert('Barang berhasil dihapus');</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan saat menghapus data');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}

// Mengambil data barang untuk mengedit
if (isset($_GET['edit'])) {
    $kode_barang = $_GET['edit'];

    try {
        $query = "SELECT * FROM barang WHERE kode_barang = :kode_barang";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':kode_barang', $kode_barang);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            echo "<script>
                document.getElementById('kode').value = '" . $row['kode_barang'] . "';
                document.getElementById('noIdentitas').value = '" . $row['no_identitas'] . "';
                document.getElementById('namaPenitip').value = '" . $row['nama_penitip'] . "';
                document.getElementById('noTelepon').value = '" . $row['no_telepon'] . "';
                document.getElementById('namaBarang').value = '" . $row['nama_barang'] . "';
                document.getElementById('tanggalMasuk').value = '" . $row['tanggal_masuk'] . "';
                document.getElementById('jamMasuk').value = '" . $row['jam_masuk'] . "';
                document.getElementById('petugas').value = '" . $row['petugas_id'] . "';
                document.getElementById('status').value = '" . $row['status'] . "';
            </script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}

// Menangani pencarian barang
$search_query = "";
$result = [];
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];

    try {
        $query = "SELECT * FROM barang WHERE kode_barang LIKE :search_query OR nama_barang LIKE :search_query AND status = 'Tersedia'";
        $stmt = $pdo->prepare($query);
        $search_param = "%$search_query%";
        $stmt->bindParam(':search_query', $search_param);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
} else {
    try {
        $query = "SELECT * FROM barang WHERE status = 'Tersedia'";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Barang Masuk</title>
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

    <!-- Tombol untuk menambah barang baru -->
    <button class="tambah-barang-button" onclick="toggleForm()">+ Tambah Barang Baru</button>

    <!-- Form untuk menambah dan mengedit data -->
    <div class="form-penambahan hidden">
        <form id="barangForm" method="POST" action="barangmasuk.php">
            <input type="text" id="kode" name="kode_barang" placeholder="Kode Barang" required readonly>
            <input type="text" id="noIdentitas" name="no_identitas" placeholder="No Identitas (KTP)" required>
            <input type="text" id="namaPenitip" name="nama_penitip" placeholder="Nama Penitip" required>
            <input type="text" id="noTelepon" name="no_telepon" placeholder="No Telepon" required>
            <input type="text" id="namaBarang" name="nama_barang" placeholder="Nama Barang" required>
            <input type="date" id="tanggalMasuk" name="tanggal_masuk" required>
            <input type="time" id="jamMasuk" name="jam_masuk" required>
            <select id="petugas" name="petugas" required>
                <option value="">Pilih Petugas</option>
                <?php
                // Mengambil daftar nama petugas dari database
                $queryPetugas = "SELECT id_petugas, nama_petugas FROM petugas";
                $stmtPetugas = $pdo->prepare($queryPetugas);
                $stmtPetugas->execute();
                $petugasList = $stmtPetugas->fetchAll(PDO::FETCH_ASSOC);

                foreach ($petugasList as $petugas) {
                    echo "<option value='" . $petugas['id_petugas'] . "'>" . $petugas['nama_petugas'] . "</option>";
                }
                ?>
            </select>
            <select id="status" name="status" required>
                <option value="Tersedia">Tersedia</option>
                <option value="Diambil">Diambil</option>
                <option value="Diproses">Diproses</option>
                <option value="Dibatalkan">Dibatalkan</option>
            </select>
            <button type="submit" name="add_barang">Tambah</button>
        </form>
    </div>

    <!-- Form Pencarian Barang -->
    <div class="search-form-masuk">
        <form action="barangmasuk.php" method="GET">
            <input type="search" name="search" id="search-box" placeholder="Cari Barang..." value="<?php echo htmlspecialchars($search_query); ?>">
            <label for="search-box"><i data-feather="search"></i></label>
        </form>
    </div>

    <!-- Tabel untuk menampilkan data -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>No Identitas (KTP)</th>
                <th>Nama Penitip</th>
                <th>No Telepon</th>
                <th>Nama Barang</th>
                <th>Tanggal Masuk</th>
                <th>Jam Masuk</th>
                <th>Petugas</th>
                <th>Status</th>
                <th>Aksi</th>
                <th>Cetak Kode</th> <!-- Kolom untuk tombol cetak -->
            </tr>
        </thead>
        <tbody id="itemTableBody">
            <?php
            if ($result) {
                $no = 1;
                foreach ($result as $row) {
                    echo "<tr>";
                    echo "<td>{$no}</td>";
                    echo "<td>{$row['kode_barang']}</td>";
                    echo "<td>{$row['no_identitas']}</td>";
                    echo "<td>{$row['nama_penitip']}</td>";
                    echo "<td>{$row['no_telepon']}</td>";
                    echo "<td>{$row['nama_barang']}</td>";
                    echo "<td>{$row['tanggal_masuk']}</td>";
                    echo "<td>{$row['jam_masuk']}</td>";
                    echo "<td>{$row['petugas_id']}</td>";
                    echo "<td>{$row['status']}</td>";
                    echo "<td>
                        <a href='editbarang.php?kode_barang={$row['kode_barang']}'>Edit</a> |
                        <a href='barangmasuk.php?hapus={$row['kode_barang']}' onclick='return confirm(\"Apakah Anda yakin ingin menghapus barang ini?\")'>Hapus</a>
                        </td>";
                    // Tombol untuk mencetak kode barang
                    // Tombol untuk mencetak kode barang
echo "<td><a href='cetak-kode.php?kode_barang={$row['kode_barang']}' target='_blank'>Cetak</a></td>";
                    echo "</tr>";
                    $no++;
                }
            } else {
                echo "<tr><td colspan='12'>Tidak ada data barang.</td></tr>";
            }
            ?>
        </tbody>
    </table>

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

        // Fungsi untuk mencetak kode barang
        function cetakKode(kodeBarang) {
            var printWindow = window.open('', '_blank', 'width=600,height=400');
            printWindow.document.write('<html><head><title>Cetak Kode Barang</title></head><body>');
            printWindow.document.write('<h3>Kode Barang: ' + kodeBarang + '</h3>');
            printWindow.document.write('<button onclick="window.print()">Print</button>');
            printWindow.document.write('</body></html>');
            printWindow.document.close();
        }

        // Fungsi untuk menyesuaikan tanggal dan jam masuk dengan waktu real-time
        window.onload = function() {
            // Set current date (tanggal_masuk) to today's date
            var today = new Date();
            var dateString = today.toISOString().split('T')[0]; // Get date in YYYY-MM-DD format
            document.getElementById('tanggalMasuk').value = dateString;

            // Set current time (jam_masuk) to current time (rounded to the nearest minute)
            var hours = today.getHours().toString().padStart(2, '0');
            var minutes = today.getMinutes().toString().padStart(2, '0');
            var timeString = hours + ":" + minutes; // Format time as HH:mm
            document.getElementById('jamMasuk').value = timeString;
        }
    </script>
</body>
</html>
