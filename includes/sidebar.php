<?php
// Pastikan sesi sudah dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ambil info pengguna dari sesi
$nama_tampil = $_SESSION['user_nama'] ?? 'Admin';
$role_tampil = $_SESSION['user_role'] ?? 'Admin';
?>

<aside id="sidebar" class="sidebar" aria-label="Sidebar navigation">
    <div class="sidebar-top">
        <div class="sidebar-logo">
            <img src="../assets/img/logo/logo1.png" alt="Logo BPS" class="logo">
            <h4 class="brand">BPS Dashboard</h4>
        </div>
        <button id="collapse-toggle" class="collapse-toggle" aria-label="Collapse sidebar" title="Collapse">
            <i class="fas fa-angle-double-left"></i>
        </button>
    </div>

    <div class="user-profile-container">
        <div class="user-avatar">
            <i class="fas fa-user-circle"></i>
        </div>
        <div class="user-info">
            <p class="user-name"><?php echo htmlspecialchars($nama_tampil); ?></p>
            <p class="user-role"><?php echo htmlspecialchars($role_tampil); ?></p>
        </div>
    </div>

    <nav class="sidebar-nav" role="navigation">
        <ul>
            <li><a href="dashboard.php" class="nav-item"><i class="fas fa-tachometer-alt"></i><span class="nav-text">Dashboard</span></a></li>
            <li><a href="pegawai.php" class="nav-item"><i class="fas fa-users"></i><span class="nav-text">Data Pegawai</span></a></li>
            <li><a href="apel.php" class="nav-item"><i class="fas fa-calendar-check"></i><span class="nav-text">Data Apel</span></a></li>
            <li class="has-sub">
                <details class="kbs-mart-menu">
                    <summary class="nav-item"><i class="fas fa-store"></i><span class="nav-text">KB-S Mart</span><i class="fas fa-chevron-right caret" aria-hidden="true"></i></summary>
                    <ul class="sub-menu">
                        <li><a href="tambah_penjualan.php">Tambah Penjualan</a></li>
                        <li><a href="barang_tersedia.php">Stok Barang</a></li>
                        <li><a href="history_penjualan.php">History Penjualan</a></li>
                    </ul>
                </details>
            </li>
            <li class="has-sub">
                <details class="prestasi-menu">
                    <summary class="nav-item"><i class="fas fa-trophy"></i><span class="nav-text">Pegawai Berprestasi</span><i class="fas fa-chevron-right caret" aria-hidden="true"></i></summary>
                    <ul class="sub-menu">
                        <li><a href="calon_berprestasi.php">Daftar Calon</a></li>
                        <li><a href="form_penilaian.php">Form Penilaian</a></li>
                        <li><a href="hasil_penilaian.php">Hasil Penilaian</a></li>
                    </ul>
                </details>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <a href="../logout.php" class="nav-item">
            <i class="fas fa-sign-out-alt"></i>
            <span class="nav-text">Logout</span>
        </a>
    </div>
</aside>