<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kode Barang</title>
    <style>
        /* Atur ukuran kertas untuk pencetakan thermal roll (80mm x 100mm) */
@page {
    size: 80mm 100mm; /* Ukuran kertas thermal roll (80mm x 100mm) */
    margin: 5mm; /* Margin kecil agar konten pas di kertas */
}

/* Mengatur tampilan halaman cetak */
body {
    font-family: Arial, sans-serif;
    font-size: 14px; /* Menyesuaikan ukuran font untuk kertas kecil */
    text-align: center;
    margin: 0;
    padding: 10px;
    box-sizing: border-box;
}

/* Gaya untuk judul "Kode Barang" */
h3 {
    font-size: 18px; /* Ukuran font yang sesuai dengan kertas kecil */
    color: #333;
    margin: 0;
    padding: 5px;
}

/* Gaya untuk tombol cetak */
button {
    background-color: #4CAF50;
    color: white;
    padding: 5px 10px;
    font-size: 12px; /* Ukuran font lebih kecil agar sesuai dengan ukuran kertas */
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

button:hover {
    background-color: #45a049;
}

/* Mengatur layout saat halaman dicetak */
@media print {
    button {
        display: none; /* Menyembunyikan tombol saat mencetak */
    }
}

    </style>
</head>
<body>
    <?php
    // Mengambil kode barang dari URL
    if (isset($_GET['kode_barang'])) {
        $kode_barang = $_GET['kode_barang'];
        echo "<h3>Kode Barang: $kode_barang</h3>";
    } else {
        echo "<h3>Kode Barang Tidak Ditemukan</h3>";
    }
    ?>
    <button onclick="window.print()">Print</button> <!-- Tombol untuk mencetak -->
</body>
</html>