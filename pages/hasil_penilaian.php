<?php
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Menyiapkan variabel filter
$filter_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : null;
$filter_triwulan = isset($_GET['triwulan']) ? $_GET['triwulan'] : null;

// Query untuk mengambil data tahun dan triwulan yang tersedia untuk filter
$sql_filter = "SELECT DISTINCT tahun, triwulan FROM calon_triwulan ORDER BY tahun DESC, triwulan ASC";
$result_filter = mysqli_query($koneksi, $sql_filter);

// Query utama untuk mengambil data pegawai berprestasi beserta total skornya
$sql = "
    SELECT
        p.id,
        p.nama,
        p.nip,
        p.foto,
        ct.triwulan,
        ct.tahun,
        SUM(pt.total_skor) AS total_skor,
        AVG(pt.total_skor) AS rata_rata_skor
    FROM penilaian_triwulan pt
    JOIN calon_triwulan ct ON pt.id_calon = ct.id
    JOIN pegawai p ON ct.id_pegawai = p.id
    WHERE 1=1"; // Kondisi awal
if ($filter_tahun) {
    $sql .= " AND ct.tahun = '" . mysqli_real_escape_string($koneksi, $filter_tahun) . "'";
}
if ($filter_triwulan) {
    $sql .= " AND ct.triwulan = '" . mysqli_real_escape_string($koneksi, $filter_triwulan) . "'";
}
$sql .= " GROUP BY p.id, p.nama, p.nip, p.foto, ct.triwulan, ct.tahun ORDER BY total_skor DESC";

$result = mysqli_query($koneksi, $sql);
?>

<div class="main-content">
    <section class="content-header">
        <h1>
            <i class="fas fa-trophy"></i> Hasil Penilaian Pegawai Berprestasi
        </h1>
    </section>

    <section class="content">
        <!-- Form Filter -->
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="box-title">Filter Hasil Penilaian</h3>
            </div>
            <div class="box-body">
                <form method="GET" action="hasil_penilaian.php">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="tahun">Tahun:</label>
                                <select name="tahun" id="tahun" class="form-control">
                                    <option value="">-- Semua Tahun --</option>
                                    <?php
                                    $all_years = [];
                                    mysqli_data_seek($result_filter, 0); // Reset pointer
                                    while ($row_filter = mysqli_fetch_assoc($result_filter)) {
                                        if (!in_array($row_filter['tahun'], $all_years)) {
                                            $all_years[] = $row_filter['tahun'];
                                            $selected = ($row_filter['tahun'] == $filter_tahun) ? 'selected' : '';
                                            echo "<option value=\"{$row_filter['tahun']}\" {$selected}>{$row_filter['tahun']}</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="triwulan">Triwulan:</label>
                                <select name="triwulan" id="triwulan" class="form-control">
                                    <option value="">-- Semua Triwulan --</option>
                                    <?php
                                    $triwulans = ['1', '2', '3', '4'];
                                    foreach ($triwulans as $tw) {
                                        $selected = ($tw == $filter_triwulan) ? 'selected' : '';
                                        echo "<option value=\"{$tw}\" {$selected}>Triwulan {$tw}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group" style="margin-top: 25px;">
                                <button type="submit" class="btn btn-primary btn-block">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Daftar Hasil Penilaian -->
        <div class="row">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    // Tentukan URL foto atau gunakan placeholder jika tidak ada
                    $foto_url = empty($row['foto']) ? '../assets/img/placeholder.png' : '../assets/img/pegawai/' . $row['foto'];
                    ?>
                    <div class="col-md-4">
                        <div class="box box-solid bg-green">
                            <div class="box-header">
                                <h3 class="box-title"><?= htmlspecialchars($row['nama']) ?></h3>
                            </div>
                            <div class="box-body text-center">
                                <img src="<?= htmlspecialchars($foto_url) ?>" alt="<?= htmlspecialchars($row['nama']) ?>" class="img-circle img-responsive" style="height: 100px; width: 100px; margin: 0 auto; object-fit: cover;">
                                <p><strong>NIP:</strong> <?= htmlspecialchars($row['nip']) ?></p>
                                <p><strong>Triwulan:</strong> <?= htmlspecialchars($row['triwulan']) ?></p>
                                <p><strong>Tahun:</strong> <?= htmlspecialchars($row['tahun']) ?></p>
                                <h4 class="text-center"><strong>Total Skor:</strong> <?= htmlspecialchars($row['total_skor']) ?></h4>
                                <h4 class="text-center"><strong>Rata-rata Skor:</strong> <?= number_format($row['rata_rata_skor'], 2) ?></h4>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="col-md-12"><p class="text-center">Belum ada data penilaian untuk filter yang dipilih.</p></div>';
            }
            ?>
        </div>
    </section>
</div>

<?php 
include '../includes/footer.php'; 
mysqli_close($koneksi);
?>
