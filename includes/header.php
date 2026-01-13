<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!-- Bootstrap CSS (CDN) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/projectdpp/component/dashboard.php">
                <img src="/projectdpp/css/assets/logo.png" alt="Logo" class="logo-img" style="height:40px;">&nbsp;Titip Aman
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="/projectdpp/component/dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/projectdpp/component/barangmasuk.php">Barang Masuk</a></li>
                    <li class="nav-item"><a class="nav-link" href="/projectdpp/component/barangkeluar.php">Barang Keluar</a></li>
                    <li class="nav-item"><a class="nav-link" href="/projectdpp/component/riwayat.php">Riwayat</a></li>
                </ul>

                <div class="d-flex align-items-center">
                    <?php
                    $userName = isset($_SESSION['nama']) ? trim($_SESSION['nama']) : '';
                    $initials = $userName ? strtoupper(substr($userName, 0, 1)) : 'U';
                    $profilePhoto = null;
                    if (!empty($_SESSION['user_id'])) {
                        if (!isset($pdo)) {
                            $dbPath = __DIR__ . '/../config/database.php';
                            if (file_exists($dbPath)) include_once $dbPath;
                        }
                        if (isset($pdo)) {
                            try {
                                $q = $pdo->prepare('SELECT foto_profile FROM profil_petugas WHERE id_petugas = :id LIMIT 1');
                                $q->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
                                $q->execute();
                                $r = $q->fetch(PDO::FETCH_ASSOC);
                                if ($r && !empty($r['foto_profile'])) {
                                    $uploadsPath = __DIR__ . '/../uploads/' . $r['foto_profile'];
                                    if (is_file($uploadsPath)) {
                                        $profilePhoto = $r['foto_profile'];
                                    }
                                }
                            } catch (Exception $ex) {
                                // ignore
                            }
                        }
                    }
                    ?>

                    <div class="dropdown">
                        <button class="btn btn-sm btn-light dropdown-toggle d-flex align-items-center" type="button" id="profileMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php if ($profilePhoto): ?>
                                <img src="/projectdpp/uploads/<?php echo rawurlencode($profilePhoto); ?>" alt="Profile" class="rounded-circle" style="width:32px;height:32px;object-fit:cover;">
                            <?php else: ?>
                                <span class="badge rounded-circle bg-secondary text-white" style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;"><?php echo htmlspecialchars($initials); ?></span>
                            <?php endif; ?>
                            <span class="ms-2 text-white d-none d-lg-inline"><?php echo htmlspecialchars($userName ?: 'User'); ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="profileMenuButton">
                            <li class="dropdown-header">Signed in as <strong><?php echo htmlspecialchars($userName ?: 'User'); ?></strong></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/projectdpp/component/profil.php">Profil</a></li>
                            <li><a class="dropdown-item text-danger" href="/projectdpp/auth/logout.php">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>
