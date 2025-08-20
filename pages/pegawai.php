<?php
session_start();
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Ambil role pengguna dari sesi
$user_role = $_SESSION['user_role'] ?? '';
?>

<main class="main-content">
    <!-- Mengatur layout header-content agar tombol Tambah Pegawai berada di kanan -->
    <!-- Menambahkan padding agar tidak terlalu mepet di pojok -->
    <div class="header-content" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; padding: 15px 20px;">
        <h2>Data Pegawai</h2>
        <?php if ($user_role === 'admin_pegawai' || $user_role === 'super_admin'): ?>
            <a href="tambah_pegawai.php" class="btn btn-primary">Tambah Pegawai</a>
        <?php endif; ?>
    </div>

    <div class="card">
        <div style="display: flex; justify-content: flex-end; padding: 10px;">
            <input type="text" id="searchInput" placeholder="Cari pegawai..." 
                   style="padding: 8px; width: 300px; border: 1px solid #ccc; border-radius: 5px;"
                   onkeyup="filterPegawai()">
        </div>

        <style>
            .pegawai-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 15px;
                padding: 15px;
            }
            .pegawai-card {
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                background: #fff;
                box-shadow: 0 2px 6px rgba(0,0,0,0.05);
                transition: transform 0.2s ease;
                display: flex;
                flex-direction: column;
                overflow: hidden;
            }
            .pegawai-card:hover {
                transform: translateY(-3px);
            }
            .pegawai-photo {
                width: 100%;
                height: 180px;
                object-fit: contain;
                background: #f5f5f5;
                padding: 5px;
            }

            .pegawai-body {
                padding: 15px;
                flex: 1;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }
            .pegawai-name {
                font-size: 16px;
                font-weight: bold;
                margin-bottom: 5px;
            }
            .pegawai-info {
                font-size: 14px;
                color: #555;
                margin-bottom: 3px;
            }
            .btn-action-group {
                /* Mengatur agar tombol CRUD berada di tengah */
                display: flex;
                justify-content: center;
                gap: 5px;
                margin-top: 10px;
            }
            .btn-action {
                padding: 6px 10px;
                border-radius: 5px;
                font-size: 13px;
                text-decoration: none;
                color: #fff;
                /* Menambahkan margin agar tidak terlalu mepet */
                margin: 0 5px;
            }
            .btn-action.detail { background: #3498db; }
            .btn-action.edit { background: #f1c40f; color: #000; }
            .btn-action.delete { background: #e74c3c; }
        </style>

        <div class="pegawai-grid" id="pegawaiGrid">
            <?php
            // Mengambil data pegawai dan mengurutkannya berdasarkan no_urut
            $sql = "SELECT * FROM pegawai ORDER BY no_urut ASC";
            $result = $koneksi->query($sql);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $nama = htmlspecialchars($row['nama']);
                    $nip = htmlspecialchars($row['nip']);
                    $nip_bps = htmlspecialchars($row['nip_bps']);
                    $jabatan = htmlspecialchars($row['jabatan']);
                    $seksi = htmlspecialchars($row['seksi']);
                    $id = $row['id'];

                    $foto = !empty($row['foto']) ? "../assets/img/pegawai/" . $row['foto'] : "../assets/img/pegawai/default.png";

                    echo "<div class='pegawai-card' data-search='" . strtolower("$nama $nip $nip_bps $jabatan $seksi") . "'>";
                    
                    // Foto
                    echo "<img src='" . htmlspecialchars($foto) . "' alt='Foto $nama' class='pegawai-photo'>";

                    // Body
                    echo "<div class='pegawai-body'>";
                    echo "<div>";
                    echo "<div class='pegawai-name'>$nama</div>";
                    echo "<div class='pegawai-info'>NIP BPS: $nip_bps</div>";
                    echo "<div class='pegawai-info'>Jabatan: $jabatan</div>";
                    echo "<div class='pegawai-info'>Seksi: $seksi</div>";
                    echo "</div>";
                    
                    // Kontrol Aksi berdasarkan role
                    echo "<div class='btn-action-group'>";
                    echo "<a href='detail_pegawai.php?id=$id' class='btn-action detail'>Detail</a>";
                    if ($user_role === 'admin_pegawai' || $user_role === 'super_admin') {
                        echo "<a href='edit_pegawai.php?id=$id' class='btn-action edit'>Edit</a>";
                        echo "<a href='../proses/proses_hapus.php?id=$id' class='btn-action delete' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\")'>Hapus</a>";
                    }
                    echo "</div>"; // end btn-action-group

                    echo "</div>"; // end body

                    echo "</div>"; // end card
                }
            } else {
                echo "<p style='padding: 15px;'>Tidak ada data pegawai.</p>";
            }
            ?>
        </div>

        <script>
            function filterPegawai() {
                let input = document.getElementById('searchInput').value.toLowerCase();
                let cards = document.querySelectorAll('#pegawaiGrid .pegawai-card');
                cards.forEach(card => {
                    let keyword = card.getAttribute('data-search');
                    card.style.display = keyword.includes(input) ? '' : 'none';
                });
            }
        </script>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
