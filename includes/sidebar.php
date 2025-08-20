<?php
// Pastikan sesi sudah dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ambil info pengguna dari sesi
$nama_tampil = $_SESSION['user_nama'] ?? 'Admin';
$role_tampil = $_SESSION['user_role'] ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Impor Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Impor font modern dari Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS khusus sidebar -->
    <style>
        :root {
            --primary-color: #4A90E2;
            --secondary-color: #50E3C2;
            --sidebar-width: 250px;
            --sidebar-bg: #2b3342;
            --sidebar-text: #e0e6ed;
            --active-item-bg: rgba(255, 255, 255, 0.1);
        }

        /* Gaya Dasar */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Sidebar dan Main Content */
        .dashboard-layout {
            display: flex;
        }

        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            height: 100vh;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            transition: transform 0.3s ease-in-out;
        }

        /* Tombol Toggle untuk Mobile */
        .sidebar-toggle-btn {
            display: none; /* Sembunyikan di desktop */
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 101;
            background: var(--primary-color);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 15px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }
        
        .sidebar-toggle-btn:hover {
            transform: scale(1.05);
        }

        /* Overlay untuk mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 99;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        /* Saat sidebar terbuka di mobile */
        body.sidebar-open .sidebar {
            transform: translateX(0);
        }

        body.sidebar-open .sidebar-overlay {
            display: block;
            opacity: 1;
        }

        /* Gaya Logo */
        .sidebar-top {
            padding: 20px;
            display: flex;
            align-items: center;
        }
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar-logo .logo {
            width: 40px;
            height: 40px;
        }
        .sidebar-logo .brand {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: #fff;
        }
        
        /* Profil Pengguna */
        .sidebar .user-profile-container {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .sidebar .user-profile-container .user-avatar {
            font-size: 2rem;
            color: var(--primary-color);
            background-color: #ecf0f1;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-shrink: 0;
        }
        .sidebar .user-profile-container .user-info {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            line-height: 1.2;
            overflow: hidden;
        }
        .sidebar .user-profile-container .user-name {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: #fff;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
            max-width: 150px;
        }
        .sidebar .user-profile-container .user-role {
            margin-top: 3px;
            font-size: 0.8rem;
            font-weight: 400;
            color: var(--light-text-color);
            text-transform: capitalize;
        }

        /* Navigasi */
        .sidebar-nav {
            flex-grow: 1;
            padding: 0 15px;
            overflow-y: auto;
        }
        .sidebar-nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-nav li {
            margin-bottom: 5px;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 15px;
            text-decoration: none;
            color: var(--sidebar-text);
            border-radius: 8px;
            transition: background-color 0.2s ease, color 0.2s ease;
        }
        .nav-item:hover {
            background-color: var(--active-item-bg);
            color: #fff;
        }
        .nav-item i {
            font-size: 1rem;
        }
        .nav-text {
            font-weight: 500;
        }

        .kbs-mart-menu summary.nav-item,
        .prestasi-menu summary.nav-item {
            cursor: pointer;
        }

        .sub-menu {
            list-style: none;
            padding-left: 30px;
            margin: 5px 0 10px;
        }
        .sub-menu a {
            padding: 8px 0;
            font-size: 0.9rem;
            display: block;
            text-decoration: none;
            color: var(--sidebar-text);
        }
        .sub-menu a:hover {
            color: var(--secondary-color);
        }
        .caret {
            margin-left: auto;
            transition: transform 0.2s ease;
        }
        details[open] .caret {
            transform: rotate(90deg);
        }

        /* Footer */
        .sidebar-footer {
            padding: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        .logout-btn {
            background: none;
            color: var(--sidebar-text);
            width: 100%;
            text-align: left;
            border: none;
        }
        .logout-btn:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* MEDIA QUERIES untuk responsif */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar-toggle-btn {
                display: block;
            }
        }
    </style>
</head>
<body>

    <button id="sidebarToggle" class="sidebar-toggle-btn"><i class="fas fa-bars"></i></button>
    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <aside id="sidebar" class="sidebar" aria-label="Sidebar navigation">
        <!-- Bagian Atas -->
        <div class="sidebar-top">
            <div class="sidebar-logo">
                <img src="../assets/img/logo/logo1.png" alt="Logo BPS" class="logo">
                <h4 class="brand">BPS Dashboard</h4>
            </div>
            <!-- Tombol tutup (close) untuk mobile -->
            <button id="sidebarClose" class="sidebar-toggle-btn" style="position: absolute; right: 10px; top: 10px; background: none; color: #fff; box-shadow: none;"><i class="fas fa-times"></i></button>
        </div>
    
        <!-- Profil User -->
        <div class="user-profile-container">
            <div class="user-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="user-info">
                <p class="user-name"><?php echo htmlspecialchars($nama_tampil); ?></p>
                <p class="user-role"><?php echo ucfirst(htmlspecialchars($role_tampil)); ?></p>
            </div>
        </div>
    
        <!-- Navigasi -->
        <nav class="sidebar-nav" role="navigation">
            <ul>
                <li><a href="dashboard.php" class="nav-item"><i class="fas fa-tachometer-alt"></i><span class="nav-text">Dashboard</span></a></li>
                <li><a href="pegawai.php" class="nav-item"><i class="fas fa-users"></i><span class="nav-text">Data Pegawai</span></a></li>
                <li><a href="apel.php" class="nav-item"><i class="fas fa-calendar-check"></i><span class="nav-text">Data Apel</span></a></li>
                <li class="has-sub">
                    <details class="kbs-mart-menu">
                        <summary class="nav-item">
                            <i class="fas fa-store"></i>
                            <span class="nav-text">KB-S Mart</span>
                            <i class="fas fa-chevron-right caret"></i>
                        </summary>
                        <ul class="sub-menu">
                            <li><a href="tambah_penjualan.php"><i class="fas fa-plus-circle"></i> Tambah Penjualan</a></li>
                            <li><a href="barang_tersedia.php"><i class="fas fa-box-open"></i> Stok Barang</a></li>
                            <li><a href="history_penjualan.php"><i class="fas fa-history"></i> History Penjualan</a></li>
                        </ul>
                    </details>
                </li>
    
                <li class="has-sub">
                    <details class="prestasi-menu">
                        <summary class="nav-item">
                            <i class="fas fa-trophy"></i>
                            <span class="nav-text">Pegawai Berprestasi</span>
                            <i class="fas fa-chevron-right caret"></i>
                        </summary>
                        <ul class="sub-menu">
                            <li><a href="calon_berprestasi.php"><i class="fas fa-user-plus"></i> Daftar Calon</a></li>
                            <li><a href="form_penilaian.php"><i class="fas fa-clipboard-check"></i> Form Penilaian</a></li>
                            <li><a href="hasil_penilaian.php"><i class="fas fa-chart-line"></i> Hasil Penilaian</a></li>
                        </ul>
                    </details>
                </li>
    
            </ul>
        </nav>
    
        <!-- Logout -->
        <div class="sidebar-footer">
            <a href="../logout.php" class="nav-item logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span class="nav-text">Logout</span>
            </a>
        </div>
    </aside>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarClose = document.getElementById('sidebarClose');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const body = document.body;

            // Membuka sidebar saat tombol toggle diklik
            sidebarToggle.addEventListener('click', function() {
                body.classList.add('sidebar-open');
            });

            // Menutup sidebar saat tombol close atau overlay diklik
            sidebarClose.addEventListener('click', function() {
                body.classList.remove('sidebar-open');
            });
            sidebarOverlay.addEventListener('click', function() {
                body.classList.remove('sidebar-open');
            });
        });
    </script>
</body>
</html>
