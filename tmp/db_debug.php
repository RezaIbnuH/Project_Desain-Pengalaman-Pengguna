<?php
require_once __DIR__ . '/../config/database.php';

try {
    $stmt = $pdo->query('SELECT id_petugas, foto_profile FROM profil_petugas');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
