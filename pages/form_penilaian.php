<?php
// Masukkan file koneksi database dan layout utama
session_start();
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Ambil data user dari sesi
$user_role = $_SESSION['user_role'] ?? '';
$user_id = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['user_name'] ?? '';

// Cek hak akses. Jika user adalah admin, berikan pesan dan keluar.
if (in_array($user_role, ['admin_super'])) {
    echo "<main class=\"main-content\"><div class=\"card card-access-denied\"><h2 class=\"text-center text-danger\">Akses Ditolak</h2><p class=\"text-center\">Halaman ini hanya bisa diakses oleh pegawai untuk melakukan penilaian.</p></div></main>";
    include '../includes/footer.php';
    exit; // Keluar dari skrip
}

// Menyiapkan variabel untuk filter
$current_year = date('Y');
$current_triwulan = ceil(date('n') / 3);
$filter_triwulan = isset($_GET['triwulan']) ? intval($_GET['triwulan']) : $current_triwulan;
$filter_tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : $current_year;

// Cek apakah user sudah mengisi penilaian untuk triwulan dan tahun ini
$has_rated = false;
$sql_check_rating = "SELECT COUNT(*) AS count FROM penilaian_triwulan WHERE id_penilai = ? AND triwulan = ? AND tahun = ?";
$stmt_check = $koneksi->prepare($sql_check_rating);
$stmt_check->bind_param("isi", $user_id, $filter_triwulan, $filter_tahun);
$stmt_check->execute();
$result_check = $stmt_check->get_result()->fetch_assoc();
if ($result_check['count'] > 0) {
    $has_rated = true;
}
$stmt_check->close();

// Query untuk mengambil data calon pegawai untuk penilaian
$query_calon = "
    SELECT 
        ct.id,
        p.nama,
        p.jabatan,
        ct.triwulan,
        ct.tahun
    FROM calon_triwulan ct
    JOIN pegawai p ON ct.id_pegawai = p.id
    WHERE ct.triwulan = ? AND ct.tahun = ?
    ORDER BY p.nama ASC";
$stmt = $koneksi->prepare($query_calon);
$stmt->bind_param("ii", $filter_triwulan, $filter_tahun);
$stmt->execute();
$result_calon = $stmt->get_result();

$calon_list = [];
while ($row = $result_calon->fetch_assoc()) {
    $calon_list[] = $row;
}
$stmt->close();
?>

<main class="main-content">
    <div class="header-content">
        <h2>Formulir Penilaian</h2>
    </div>

    <section class="filter-section">
        <div class="card card-filter">
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
        </div>
    </section>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="alert alert-success">Penilaian berhasil disimpan.</div>
    <?php endif; ?>
    <?php if (isset($_GET['status']) && $_GET['status'] == 'error'): ?>
        <div class="alert alert-danger">Gagal menyimpan penilaian: <?php echo htmlspecialchars($_GET['message']); ?></div>
    <?php endif; ?>

    <?php if ($has_rated): ?>
        <div class="card card-info">
            <p class="text-center">Anda sudah mengisi formulir penilaian untuk periode ini. Terima kasih atas partisipasinya. 💖</p>
        </div>
    <?php elseif (empty($calon_list)): ?>
        <div class="card card-info">
            <p class="text-center">Belum ada calon pegawai untuk triwulan <?= htmlspecialchars($filter_triwulan) ?> tahun <?= htmlspecialchars($filter_tahun) ?>.</p>
        </div>
    <?php else: ?>
        <form action="../proses/proses_penilaian.php" method="post" class="rating-form">
            <input type="hidden" name="triwulan" value="<?= htmlspecialchars($filter_triwulan) ?>">
            <input type="hidden" name="tahun" value="<?= htmlspecialchars($filter_tahun) ?>">
            <input type="hidden" name="id_penilai" value="<?= htmlspecialchars($user_id) ?>">

            <div class="card">
                <div class="card-header">
                    <h3>Form Penilaian (<?= htmlspecialchars($filter_triwulan) ?>/<?= htmlspecialchars($filter_tahun) ?>)</h3>
                </div>
                <div class="card-body">
                        <div class="form-group">
                        <label for="nama_penilai">Nama Penilai:</label>
                        <input type="text" id="nama_penilai" class="form-control" value="<?= htmlspecialchars($user_name) ?>" readonly>
                    </div>
                    <br>

                    <p class="form-description">Berikan nilai 1-100 untuk setiap calon berdasarkan kriteria berikut. (1=Sangat Kurang, 100=Sangat Baik)</p>
                    
                    <?php foreach ($calon_list as $calon): ?>
                        <div class="calon-card">
                            <h4 class="calon-name"><?= htmlspecialchars($calon['nama']) ?> <small>(<?= htmlspecialchars($calon['jabatan']) ?>)</small></h4>
                            <input type="hidden" name="id_calon_dinilai[]" value="<?= $calon['id'] ?>">

                            <div class="rating-group">
                                <label for="skor_berorientasi-<?= $calon['id'] ?>">Berorientasi Pelayanan:</label>
                                <input type="range" name="skor_berorientasi[<?= $calon['id'] ?>]" id="skor_berorientasi-<?= $calon['id'] ?>" class="range-slider" min="1" max="100" value="50">
                                <span class="range-value">50</span>
                            </div>
                            <div class="rating-group">
                                <label for="skor_akuntabel-<?= $calon['id'] ?>">Akuntabel:</label>
                                <input type="range" name="skor_akuntabel[<?= $calon['id'] ?>]" id="skor_akuntabel-<?= $calon['id'] ?>" class="range-slider" min="1" max="100" value="50">
                                <span class="range-value">50</span>
                            </div>
                            <div class="rating-group">
                                <label for="skor_kompeten-<?= $calon['id'] ?>">Kompeten:</label>
                                <input type="range" name="skor_kompeten[<?= $calon['id'] ?>]" id="skor_kompeten-<?= $calon['id'] ?>" class="range-slider" min="1" max="100" value="50">
                                <span class="range-value">50</span>
                            </div>
                            <div class="rating-group">
                                <label for="skor_harmonis-<?= $calon['id'] ?>">Harmonis:</label>
                                <input type="range" name="skor_harmonis[<?= $calon['id'] ?>]" id="skor_harmonis-<?= $calon['id'] ?>" class="range-slider" min="1" max="100" value="50">
                                <span class="range-value">50</span>
                            </div>
                            <div class="rating-group">
                                <label for="skor_loyal-<?= $calon['id'] ?>">Loyal:</label>
                                <input type="range" name="skor_loyal[<?= $calon['id'] ?>]" id="skor_loyal-<?= $calon['id'] ?>" class="range-slider" min="1" max="100" value="50">
                                <span class="range-value">50</span>
                            </div>
                            <div class="rating-group">
                                <label for="skor_adaptif-<?= $calon['id'] ?>">Adaptif:</label>
                                <input type="range" name="skor_adaptif[<?= $calon['id'] ?>]" id="skor_adaptif-<?= $calon['id'] ?>" class="range-slider" min="1" max="100" value="50">
                                <span class="range-value">50</span>
                            </div>
                            <div class="rating-group">
                                <label for="skor_kolaboratif-<?= $calon['id'] ?>">Kolaboratif:</label>
                                <input type="range" name="skor_kolaboratif[<?= $calon['id'] ?>]" id="skor_kolaboratif-<?= $calon['id'] ?>" class="range-slider" min="1" max="100" value="50">
                                <span class="range-value">50</span>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="form-actions text-center">
                        <button type="submit" class="btn btn-submit">Kirim Penilaian</button>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sliders = document.querySelectorAll('.range-slider');
        sliders.forEach(slider => {
            const output = slider.nextElementSibling;
            output.textContent = slider.value;
            slider.oninput = function() {
                output.textContent = this.value;
            };
        });
    });
</script>
<style>
    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        background-color: #f4f6f9;
        color: #495057;
    }

    .main-content {
        padding: 2rem;
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .header-content h2 {
        font-weight: 600;
        color: #333;
        margin: 0;
    }

    /* Card Styling */
    .card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 25px;
        padding: 20px;
    }

    .card-access-denied {
        padding: 50px;
        text-align: center;
    }

    .card-info {
        padding: 30px;
        text-align: center;
    }

    .card-header {
        background-color: #f8f9fa;
        padding: 1.25rem;
        border-bottom: 1px solid #e9ecef;
        margin: -20px -20px 20px -20px;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }
    
    .card-header h3 {
        margin: 0;
        font-size: 1.25rem;
        color: #333;
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
        flex-grow: 1;
    }

    .form-group label {
        font-weight: 500;
        color: #555;
        margin-bottom: 5px;
    }

    .form-control {
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    
    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        outline: none;
    }

    .form-control[readonly] {
        background-color: #e9ecef;
        cursor: not-allowed;
    }

    .form-description {
        margin-top: -10px;
        margin-bottom: 20px;
        font-style: italic;
        color: #888;
        font-size: 0.9rem;
    }

    /* Rating Cards */
    .calon-card {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        background: #fcfcfc;
        box-shadow: 0 2px 5px rgba(0,0,0,0.03);
    }
    
    .calon-name {
        margin-top: 0;
        margin-bottom: 15px;
        font-size: 1.2rem;
        color: #2c3e50;
    }

    .rating-group {
        margin-bottom: 15px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 15px;
    }
    
    .rating-group label {
        flex: 1;
        min-width: 150px;
        margin-bottom: 0;
    }
    
    .rating-group .range-slider {
        flex: 2;
        min-width: 200px;
    }
    
    .rating-group .range-value {
        flex-basis: 40px;
        text-align: right;
        font-weight: 600;
        color: #007bff;
    }

    /* Slider Styling */
    .range-slider {
        -webkit-appearance: none;
        width: 100%;
        height: 10px;
        background: #e0e0e0;
        outline: none;
        opacity: 0.7;
        transition: opacity .2s;
        border-radius: 5px;
    }

    .range-slider:hover {
        opacity: 1;
    }

    .range-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 20px;
        height: 20px;
        background: #007bff;
        cursor: pointer;
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0 0 5px rgba(0,0,0,0.2);
    }

    .range-slider::-moz-range-thumb {
        width: 20px;
        height: 20px;
        background: #007bff;
        cursor: pointer;
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0 0 5px rgba(0,0,0,0.2);
    }

    /* Button and Alerts */
    .btn {
        padding: 12px 20px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-weight: 500;
        text-decoration: none;
        transition: background-color 0.2s ease, transform 0.2s ease;
    }
    
    .btn-primary {
        background-color: #007bff;
        color: #fff;
    }
    
    .btn-primary:hover {
        background-color: #0056b3;
    }

    .btn-submit {
        width: 100%;
        background-color: #28a745;
        color: #fff;
    }

    .btn-submit:hover {
        background-color: #218838;
        transform: translateY(-2px);
    }

    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 8px;
        font-weight: 500;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
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

        .rating-group {
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
        }

        .rating-group label, .rating-group .range-slider, .rating-group .range-value {
            min-width: unset;
            flex: unset;
            width: 100%;
            text-align: left;
        }

        .rating-group .range-value {
            text-align: center;
        }
    }
</style>