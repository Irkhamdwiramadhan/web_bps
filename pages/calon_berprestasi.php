<?php
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

$current_year = date('Y');
$current_triwulan = ceil(date('n') / 3);

$filter_triwulan = isset($_GET['triwulan']) ? $_GET['triwulan'] : strval($current_triwulan);
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
<style>
    .card-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: flex-start;
    }
    .calon-card {
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
        text-align: center;
        width: 250px;
        transition: transform 0.2s ease-in-out;
    }
    .calon-card:hover {
        transform: translateY(-5px);
    }
    .calon-foto {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 15px;
        border: 4px solid #007bff;
    }
    .card-info h4 {
        margin: 0 0 5px 0;
        color: #333;
    }
    .card-info p {
        margin: 0 0 15px 0;
        color: #666;
        font-size: 14px;
    }
    .card-actions .btn {
        width: 100%;
    }
</style>

<div class="main-content">
    <section class="content-header">
        <h1>
            <i class="fas fa-trophy"></i> Calon Pegawai Berprestasi
        </h1>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Daftar Calon</h3>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <a href="tambah_calon_berprestasi.php" class="btn btn-primary mb-3">
                        <i class="fa fa-plus"></i> Tambah Calon
                    </a>
                </div>

                <hr>

                <!-- Filter Form -->
                <form class="form-inline mb-4" method="GET" action="">
                    <div class="form-group">
                        <label for="tahun">Tahun:</label>
                        <select name="tahun" id="tahun" class="form-control">
                            <?php for ($y = 2020; $y <= 2030; $y++): ?>
                                <option value="<?= $y ?>" <?= $y == $filter_tahun ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="triwulan">Triwulan:</label>
                        <select name="triwulan" id="triwulan" class="form-control">
                            <?php for ($t = 1; $t <= 4; $t++): ?>
                                <option value="<?= $t ?>" <?= $t == $filter_triwulan ? 'selected' : '' ?>><?= $t ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-info">Filter</button>
                </form>

                <?php
                if (isset($_GET['status']) && $_GET['status'] == 'success') {
                    echo '<div class="alert alert-success alert-dismissible">Data calon berhasil ditambahkan!</div>';
                }
                if (isset($_GET['status']) && $_GET['status'] == 'success_delete') {
                    echo '<div class="alert alert-warning alert-dismissible">Data calon berhasil dihapus!</div>';
                }
                if (isset($_GET['status']) && $_GET['status'] == 'error') {
                    $message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'Terjadi kesalahan.';
                    echo '<div class="alert alert-danger alert-dismissible">Error: ' . $message . '</div>';
                }
                ?>
                
                <div class="card-container">
                    <?php 
                    if ($result_calon->num_rows > 0): 
                        while ($row = $result_calon->fetch_assoc()):
                            // Menggabungkan path direktori dengan nama file foto dari database
                            $foto_path = '../assets/img/pegawai/' . $row['foto'];
                            // Menggunakan placeholder jika path foto kosong atau file tidak ditemukan
                            $image_src = (!empty($row['foto']) && file_exists($foto_path)) ? htmlspecialchars($foto_path) : 'https://placehold.co/120x120/E0E0E0/333333?text=No+Foto';
                    ?>
                    <div class="calon-card">
                        <img src="<?= $image_src ?>" alt="Foto <?= htmlspecialchars($row['nama']) ?>" class="calon-foto">
                        <div class="card-info">
                            <h4><?= htmlspecialchars($row['nama']) ?></h4>
                            <p>Jabatan: <?= htmlspecialchars($row['jabatan']) ?></p>
                            <p>NIP: <?= htmlspecialchars($row['nip']) ?></p>
                        </div>
                        <div class="card-actions">
                            <a href="../proses/proses_hapus_calon.php?id=<?= $row['id'] ?>&triwulan=<?= $filter_triwulan ?>&tahun=<?= $filter_tahun ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Apakah Anda yakin ingin menghapus calon ini?');">
                                <i class="fa fa-trash"></i> Hapus
                            </a>
                        </div>
                    </div>
                    <?php 
                        endwhile; 
                    else: 
                    ?>
                    <p class="text-center" style="width: 100%;">Tidak ada calon untuk triwulan ini.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<?php 
include '../includes/footer.php'; 
$stmt->close();
mysqli_close($koneksi);
?>
