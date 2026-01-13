<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!-- Bootstrap CSS (CDN) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  :root{--sidebar-width:260px;--sidebar-height:56px}
  /* Sidebar layout */
  .app-sidebar{
    position:fixed;
    top:0;left:0;height:100vh;
    width:var(--sidebar-width);
    background:#0d6efd; /* Bootstrap primary */
    color:#fff;display:flex;flex-direction:column;z-index:1040;padding:1rem 0;
  }
  .app-sidebar .brand{display:flex;align-items:center;gap:.5rem;padding:0 .75rem;margin-bottom:1rem}
  .app-sidebar .brand img{height:36px}
  .app-sidebar .nav-links{flex:1;padding:.25rem .5rem}
  .app-sidebar .nav-links a{display:block;color:#fff;padding:.6rem .75rem;border-radius:6px;text-decoration:none}
  .app-sidebar .nav-links a:hover{background:rgba(255,255,255,0.08)}
  .app-sidebar .profile-area{padding:.75rem .75rem;border-top:1px solid rgba(255,255,255,0.06)}
  /* Push page content right to make room for sidebar */
  body{margin-left:var(--sidebar-width);transition:margin 160ms ease}
  /* Small screens: turn sidebar into top bar */
  @media (max-width:767.98px){
    .app-sidebar{position:fixed;left:0;top:0;width:100%;height:auto;flex-direction:row;align-items:center;padding:.5rem;}
    .app-sidebar .nav-links{display:flex;flex-direction:row;gap:.25rem;padding:0;margin-left:1rem}
    .app-sidebar .nav-links a{padding:.4rem .6rem}
    .app-sidebar .profile-area{display:none}
    body{margin-left:0;margin-top:var(--sidebar-height)}
  }
</style>

<aside class="app-sidebar">
    <div class="brand px-3">
        <a class="d-absolute align-items-center text-white text-decoration-none" href="/projectdpp/component/dashboard.php">
            <img src="/projectdpp/css/assets/logo.png" alt="Logo">
            <span class="fw-bold ms-1">Titip Aman</span>
        </a>
    </div>

    <nav class="nav-links">
        <a href="/projectdpp/component/dashboard.php">Dashboard</a>
        <a href="/projectdpp/component/barangmasuk.php">Barang Masuk</a>
        <a href="/projectdpp/component/barangkeluar.php">Barang Keluar</a>
        <a href="/projectdpp/component/riwayat.php">Riwayat</a>
    </nav>

    <div class="profile-area">
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
            <button class="btn btn-sm btn-light dropdown-toggle d-flex align-items-center w-100" type="button" id="sidebarProfileBtn" data-bs-toggle="dropdown" aria-expanded="false">
                <?php if ($profilePhoto): ?>
                    <img src="/projectdpp/uploads/<?php echo rawurlencode($profilePhoto); ?>" alt="Profile" class="rounded-circle" style="width:36px;height:36px;object-fit:cover;">
                <?php else: ?>
                    <span class="badge rounded-circle bg-secondary text-white" style="width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;"><?php echo htmlspecialchars($initials); ?></span>
                <?php endif; ?>
                <span class="ms-2 text-white small"><?php echo htmlspecialchars($userName ?: 'User'); ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-start shadow" aria-labelledby="sidebarProfileBtn">
                <li class="dropdown-header">Signed in as <strong><?php echo htmlspecialchars($userName ?: 'User'); ?></strong></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="/projectdpp/component/profil.php">Profil</a></li>
                <li><a class="dropdown-item text-danger" href="/projectdpp/auth/logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</aside>
