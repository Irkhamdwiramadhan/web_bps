<?php
// Pastikan sesi sudah dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ambil info pengguna dari sesi
$nama_tampil = $_SESSION['user_nama'] ?? 'Admin';
$role_tampil = $_SESSION['user_role'] ?? 'Admin';
$foto_user = $_SESSION['user_foto'] ?? null;

// Tentukan path relatif untuk file CSS
$relative_path_to_css = str_repeat('../', substr_count(dirname($_SERVER['PHP_SELF']), '/') - 1);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* --- Perbaikan dan Penambahan Gaya CSS --- */
        :root {
            --sidebar-width: 250px;
            --sidebar-bg: #1e2a38; /* Warna dasar gelap */
            --sidebar-text: #e0e6ed; /* Warna teks terang */
            --accent-color: #0784faff; /* Warna aksen emas */
            --link-hover-bg: #2b394d;
            --border-color: rgba(255, 255, 255, 0.1);
            --logout-color: #e74c3c;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
        }

        /* Sidebar dan Main Content */
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
            z-index: 1000;
            transition: transform 0.3s ease-in-out;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
        }

        /* Sidebar Toggle dan Overlay untuk Mobile */
        .sidebar-toggle-btn {
            display: none; /* Sembunyikan di desktop */
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1001;
            background-color: rgba(255, 255, 255, 0.8);
            color: #3498db; /* Warna ikon biru */
            border: none;
            border-radius: 8px;
            padding: 10px 15px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .sidebar-toggle-btn:hover {
            transform: scale(1.05);
            background-color: #fff;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        /* Saat sidebar terbuka di mobile */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            body.sidebar-open .sidebar {
                transform: translateX(0);
            }
            body.sidebar-open .sidebar-overlay {
                display: block;
                opacity: 1;
            }
            .sidebar-toggle-btn {
                display: block;
            }
        }

        /* Gaya Logo dan Navigasi */
        .sidebar-top {
            padding: 20px 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
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
        .sidebar-nav {
            flex-grow: 1;
            padding: 15px;
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
        .nav-item:hover,
        .nav-item.active {
            background-color: var(--link-hover-bg);
            color: var(--accent-color);
        }
        .nav-item i {
            font-size: 1rem;
            width: 20px;
            text-align: center;
        }
        .nav-text {
            font-weight: 500;
        }
        .sub-menu {
            list-style: none;
            padding-left: 10px;
            margin: 5px 0 10px;
        }
        .sub-menu a {
            padding: 8px 0;
            font-size: 0.9rem;
            display: block;
            text-decoration: none;
            color: var(--sidebar-text);
            transition: color 0.2s ease;
        }
        .sub-menu a:hover {
            color: var(--accent-color);
        }
        .caret {
            margin-left: auto;
            transition: transform 0.2s ease;
        }
        details[open] .caret {
            transform: rotate(90deg);
        }

        /* Footer dan Profil Pengguna */
        .sidebar-footer {
            padding: 20px 15px;
            border-top: 1px solid var(--border-color);
        }
        .user-profile-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 20px;
        }
        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid var(--accent-color);
            box-shadow: 0 0 15px rgba(243, 156, 18, 0.4);
            margin-bottom: 10px;
        }
        .user-avatar img, .user-avatar .fa-user-circle {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .user-avatar .fa-user-circle {
            font-size: 80px;
            color: #bdc3c7;
        }

        .user-profile-container .user-name {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px;
        }
        .user-profile-container .user-role {
            margin-top: 3px;
            font-size: 0.8rem;
            font-weight: 400;
            color: #bdc3c7;
            text-transform: capitalize;
        }
        .logout-btn {
            background: none;
            color: var(--logout-color);
            width: 100%;
            text-align: left;
            border: none;
            text-decoration: none;
        }
        .logout-btn:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body>

    <button id="sidebarToggle" class="sidebar-toggle-btn"><i class="fas fa-bars"></i></button>
    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <aside id="sidebar" class="sidebar" aria-label="Sidebar navigation">
        <div class="sidebar-top">
            <div class="sidebar-logo">
                <img src="../assets/img/logo/logo1.png" alt="Logo BPS" class="logo">
                <h4 class="brand">BPS Dashboard</h4>
            </div>
            <button id="sidebarClose" class="sidebar-toggle-btn" style="position: absolute; right: 10px; top: 10px; background: none; color: #3498db; box-shadow: none;"><i class="fas fa-times"></i></button>
        </div>
         <div class="user-profile-container">
                <div class="user-avatar">
                    <?php if ($role_tampil === 'Admin'): ?>
                        <i class="fas fa-user-circle" style="font-size: 80px; color: #bdc3c7;"></i>
                    <?php else: ?>
                        <?php 
                            // Pastikan path foto tersedia sebelum menampilkannya
                            if (!empty($foto_user)):
                                $foto_path = '../assets/img/pegawai/' . htmlspecialchars($foto_user);
                        ?>
                            <img src="<?= $foto_path ?>" alt="Foto Pengguna">
                        <?php else: ?>
                            <i class="fas fa-user-circle" style="font-size: 80px; color: #bdc3c7;"></i>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="user-info">
                    <p class="user-name"><?php echo htmlspecialchars($nama_tampil); ?></p>
                    <p class="user-role"><?php echo ucfirst(htmlspecialchars($role_tampil)); ?></p>
                </div>
            </div>
    
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
                            <li><a href="rekap_transaksi.php"><i class="fas fa-history"></i> Rekap Transaksi</a></li>
                            

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
                <li class="has-sub">
                    <details class="prestasi-menu">
                        <summary class="nav-item">
                            <i class="fas fa-trophy"></i>
                            <span class="nav-text">PMS</span>
                            <i class="fas fa-chevron-right caret"></i>
                        </summary>
                        <ul class="sub-menu">
                            <li><a href="mitra.php"><i class="fas fa-user-plus"></i> Mitra Kita</a></li>
                            <li><a href="kegiatan.php"><i class="fas fa-clipboard-check"></i> Kegiatan</a></li>
                            <li><a href="jenis_surveys.php"><i class="fas fa-chart-line"></i> Jenis Survey</a></li>
                        </ul>
                    </details>
                </li>
            </ul>
        </nav>
    
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
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    body.classList.add('sidebar-open');
                });
            }

            // Menutup sidebar saat tombol close atau overlay diklik
            if (sidebarClose) {
                sidebarClose.addEventListener('click', function() {
                    body.classList.remove('sidebar-open');
                });
            }
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    body.classList.remove('sidebar-open');
                });
            }
        });
    </script>
</body>
</html>