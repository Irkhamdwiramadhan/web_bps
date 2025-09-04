<?php
session_start();
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Ambil role pengguna dari sesi untuk validasi hak akses
// Ambil peran pengguna dari sesi. Jika tidak ada, atur sebagai array kosong.
$user_roles = $_SESSION['user_role'] ?? [];

// Tentukan peran mana saja yang diizinkan untuk mengakses fitur ini
$allowed_roles_for_action = ['super_admin', 'admin_pegawai'];
// Periksa apakah pengguna memiliki salah satu peran yang diizinkan untuk melihat aksi
$has_access_for_action = false;
foreach ($user_roles as $role) {
    if (in_array($role, $allowed_roles_for_action)) {
        $has_access_for_action = true;
        break; // Keluar dari loop setelah menemukan kecocokan
    }
}

$current_year = date('Y');
$current_triwulan = ceil(date('n') / 3);

$filter_triwulan = isset($_GET['triwulan']) ? htmlspecialchars($_GET['triwulan']) : strval($current_triwulan);
$filter_tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : $current_year;

// Query untuk mengambil data calon, termasuk path foto
$sql_calon = "
    SELECT 
        ct.id,
        p.nama,
        p.nip,
        p.jabatan,
        p.foto,
        ct.triwulan,
        ct.tahun
    FROM calon_triwulan ct
    JOIN pegawai p ON ct.id_pegawai = p.id
    WHERE ct.triwulan = ? AND ct.tahun = ?
    ORDER BY p.nama ASC";
$stmt = $koneksi->prepare($sql_calon);
$stmt->bind_param("si", $filter_triwulan, $filter_tahun);
$stmt->execute();
$result_calon = $stmt->get_result();

?>
<main class="main-content">
    <div class="header-content">
        <h2>Daftar Calon Pegawai Berprestasi</h2>
        <?php if ($has_access_for_action): ?>
            <a href="tambah_calon_berprestasi.php" class="btn btn-primary">Tambah Calon</a>
        <?php endif; ?>
    </div>

    <section class="filter-section">
        <form action="" method="get" class="filter-form">
            <div class="form-group">
                <label for="triwulan">Triwulan:</label>
                <select name="triwulan" id="triwulan" class="form-control">
                    <option value="1" <?= $filter_triwulan == 1 ? 'selected' : '' ?>>1</option>
                    <option value="2" <?= $filter_triwulan == 2 ? 'selected' : '' ?>>2</option>
                    <option value="3" <?= $filter_triwulan == 3 ? 'selected' : '' ?>>3</option>
                    <option value="4" <?= $filter_triwulan == 4 ? 'selected' : '' ?>>4</option>
                </select>
            </div>
            <div class="form-group">
                <label for="tahun">Tahun:</label>
                <input type="number" name="tahun" id="tahun" class="form-control" value="<?= $filter_tahun ?>" min="2020" max="<?= date('Y') + 1 ?>">
            </div>
            <button type="submit" class="btn btn-primary">Tampilkan</button>
        </form>
    </section>

    <section class="calon-list-section">
        <div class="card">
            <div class="card-header">
                <h3>Calon Triwulan <?= htmlspecialchars($filter_triwulan) ?> Tahun <?= htmlspecialchars($filter_tahun) ?></h3>
            </div>
            <div class="card-body">
                <div class="calon-container">
                    <?php if ($result_calon->num_rows > 0) :
                        while ($row = $result_calon->fetch_assoc()) :
                            $image_src = empty($row['foto']) || !file_exists("../assets/img/pegawai/{$row['foto']}")
                                ? 'https://placehold.co/200x200/E0E0E0/888888?text=No+Foto'
                                : "../assets/img/pegawai/{$row['foto']}";
                    ?>
                    <div class="calon-card">
                        <img src="<?= $image_src ?>" alt="Foto <?= htmlspecialchars($row['nama']) ?>" class="calon-foto">
                        <div class="card-info">
                            <h4><?= htmlspecialchars($row['nama']) ?></h4>
                            <p>Jabatan: <?= htmlspecialchars($row['jabatan']) ?></p>
                            <p>NIP: <?= htmlspecialchars($row['nip']) ?></p>
                        </div>
                        <?php if ($has_access_for_action):?>
                        <div class="card-actions">
                            <a href="../proses/proses_hapus_calon.php?id=<?= $row['id'] ?>&triwulan=<?= $filter_triwulan ?>&tahun=<?= $filter_tahun ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Apakah Anda yakin ingin menghapus calon ini?');">
                                <i class="fa fa-trash"></i> Hapus
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php 
                        endwhile; 
                    else: 
                    ?>
                    <p class="text-center no-data">Tidak ada calon untuk triwulan ini.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<?php 
include '../includes/footer.php'; 
$stmt->close();
mysqli_close($koneksi);
?>
<style>
    body {
        background-color: #f4f6f9;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    .main-content {
        padding: 2rem;
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .header-content h2 {
        font-weight: 600;
        color: #333;
        margin: 0;
    }

    /* Section Styling */
    .filter-section, .calon-list-section {
        margin-bottom: 25px;
    }

    /* Card Styling */
    .card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .card-header {
        background-color: #f8f9fa;
        padding: 1.25rem;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        align-items: center;
    }

    .card-header h3 {
        margin: 0;
        font-size: 1.25rem;
        color: #333;
    }

    .card-body {
        padding: 20px;
    }

    /* Filter Form */
    .filter-form {
        display: flex;
        gap: 15px;
        align-items: flex-end;
    }
    
    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-weight: 500;
        color: #555;
        margin-bottom: 5px;
    }

    .form-control {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        transition: border-color 0.2s ease;
    }
    
    .form-control:focus {
        border-color: #007bff;
        outline: none;
    }

    /* Calon Grid */
    .calon-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 25px;
        justify-content: flex-start;
    }
    
    .calon-card {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        text-align: center;
        padding-bottom: 15px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .calon-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    }
    
    .calon-foto {
        width: 100%;
        height: 200px; /* Atur tinggi foto agar seragam */
        object-fit: cover;
        border-bottom: 1px solid #eee;
        margin-bottom: 10px;
    }

    .card-info {
        padding: 0 15px;
        text-align: center;
    }

    .card-info h4 {
        margin-top: 0;
        margin-bottom: 5px;
        font-size: 1.1rem;
        color: #333;
    }
    
    .card-info p {
        margin: 3px 0;
        font-size: 0.9rem;
        color: #666;
    }

    .card-actions {
        margin-top: 15px;
        padding: 0 15px;
    }
    
    .no-data {
        color: #888;
        font-style: italic;
    }

    /* Button Styling */
    .btn {
        padding: 10px 15px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-weight: 500;
        text-decoration: none;
        transition: background-color 0.2s ease;
    }

    .btn-primary {
        background-color: #007bff;
        color: #fff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }
    
    .btn-danger {
        background-color: #dc3545;
        color: #fff;
    }

    .btn-danger:hover {
        background-color: #c82333;
    }

    .btn-sm {
        padding: 6px 10px;
        font-size: 0.8rem;
    }
    
    .fa-trash {
        margin-right: 5px;
    }

    /* Responsiveness */
    @media (max-width: 768px) {
        .main-content {
            padding: 15px;
        }

        .filter-form {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>