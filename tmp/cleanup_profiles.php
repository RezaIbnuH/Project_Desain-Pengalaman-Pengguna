<?php
require_once __DIR__ . '/../config/database.php';
try {
    $ids = $pdo->query('SELECT DISTINCT id_petugas FROM profil_petugas')->fetchAll(PDO::FETCH_COLUMN);
    foreach ($ids as $id) {
        // try to keep a profile with non-null foto_profile if exists
        $stmt = $pdo->prepare('SELECT alamat, foto_profile FROM profil_petugas WHERE id_petugas = :id AND foto_profile IS NOT NULL LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            // fallback: take any row
            $stmt2 = $pdo->prepare('SELECT alamat, foto_profile FROM profil_petugas WHERE id_petugas = :id LIMIT 1');
            $stmt2->execute([':id' => $id]);
            $row = $stmt2->fetch(PDO::FETCH_ASSOC);
        }
        // delete all rows for this id
        $del = $pdo->prepare('DELETE FROM profil_petugas WHERE id_petugas = :id');
        $del->execute([':id' => $id]);
        // insert canonical row
        $ins = $pdo->prepare('INSERT INTO profil_petugas (id_petugas, alamat, foto_profile) VALUES (:id, :alamat, :foto)');
        $ins->execute([':id' => $id, ':alamat' => $row['alamat'] ?? null, ':foto' => $row['foto_profile'] ?? null]);
    }
    echo "Cleanup complete\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
