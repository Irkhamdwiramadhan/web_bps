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
        AVG(pt.total_skor) AS rata_rata_skor,
        COUNT(DISTINCT pt.id_penilai) AS jumlah_penilai
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
$sql .= "
    GROUP BY p.id, ct.triwulan, ct.tahun
    ORDER BY total_skor DESC";

$result = mysqli_query($koneksi, $sql);
$data_penilaian = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data_penilaian[] = $row;
    }
}
?>

<style>
    /* CSS baru untuk memastikan konten tidak bertabrakan dengan sidebar */
    .content-wrapper {
        min-height: calc(100vh - 101px);
        background-color: #f4f6f9;
        margin-left: 250px; /* Lebar sidebar */
        transition: margin-left .3s ease-in-out;
        padding: 20px;
    }
    
    /* Mengatur kontainer utama untuk kartu */
    .calon-container {
        display: flex;
        flex-wrap: wrap;
        gap: 25px; /* Jarak antar kartu */
        justify-content: flex-start;
        padding: 20px 0;
    }

    /* Style untuk setiap kartu calon */
    .calon-card {
        position: relative;
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 30px;
        width: calc(33.333% - 17px); /* Tiga kartu per baris, disesuaikan dengan gap */
    }

    .calon-card:hover {
        transform: translateY(-8px); /* Efek melayang saat di-hover */
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.12);
    }

    .card-rank {
        position: absolute;
        top: 15px;
        left: 15px;
        font-size: 1.5rem;
        font-weight: bold;
        color: #fff;
        background-color: #3498db;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card-rank.rank-1 { background-color: #f1c40f; } /* Emas */
    .card-rank.rank-2 { background-color: #bdc3c7; } /* Perak */
    .card-rank.rank-3 { background-color: #cd7f32; } /* Perunggu */
    
    .calon-foto {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #f0f4f7;
        margin-bottom: 20px;
    }

    .card-info {
        text-align: center;
        margin-bottom: 20px;
    }
    
    .card-info h3 {
        margin: 0 0 5px 0;
        font-size: 1.5rem;
        color: #2c3e50;
        font-weight: 700;
    }
    
    .card-info p {
        margin: 0;
        font-size: 0.9rem;
        color: #7f8c8d;
    }
    .score-info {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: 10px;
    }
    .score-info .total-score {
        font-size: 2rem;
        font-weight: 800;
        color: #3498db;
        line-height: 1;
        margin-bottom: 5px;
    }
    .score-info .average-score {
        font-size: 1.2rem;
        font-weight: 600;
        color: #2ecc71;
    }
    /* Responsiveness */
    @media (max-width: 992px) {
        .content-wrapper {
            margin-left: 0;
            padding-left: 20px;
        }
        .calon-card {
            width: calc(50% - 12.5px); /* Dua kartu per baris */
        }
    }
    
    @media (max-width: 576px) {
        .calon-card {
            width: 100%; /* Satu kartu per baris */
        }
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Hasil Penilaian Pegawai Berprestasi</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card mb-4 p-4">
                <h4>Filter Hasil</h4>
                <form action="" method="get" class="d-flex align-items-end gap-3">
                    <div class="form-group flex-grow-1">
                        <label for="triwulan">Triwulan:</label>
                        <select name="triwulan" id="triwulan" class="form-control">
                            <option value="">Semua</option>
                            <?php foreach ([1, 2, 3, 4] as $q): ?>
                                <option value="<?= $q ?>" <?= ($filter_triwulan == $q) ? 'selected' : '' ?>>Triwulan <?= $q ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group flex-grow-1">
                        <label for="tahun">Tahun:</label>
                        <select name="tahun" id="tahun" class="form-control">
                            <option value="">Semua</option>
                            <?php 
                            $result_filter_copy = mysqli_query($koneksi, $sql_filter);
                            $years = [];
                            while($row = mysqli_fetch_assoc($result_filter_copy)) {
                                if(!in_array($row['tahun'], $years)) {
                                    $years[] = $row['tahun'];
                                }
                            }
                            rsort($years);
                            foreach ($years as $y): ?>
                                <option value="<?= $y ?>" <?= ($filter_tahun == $y) ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
            </div>

            <div class="calon-container">
                <?php
                if (!empty($data_penilaian)) {
                    $rank = 0;
                    $last_total_score = null;
                    $skipped_ranks = 0;

                    foreach ($data_penilaian as $index => $row) {
                        // Logika untuk menentukan peringkat
                        if ($row['total_skor'] !== $last_total_score) {
                            $rank = $index + 1;
                            $last_total_score = $row['total_skor'];
                        }

                        // Menentukan URL foto
                        $foto_url = !empty($row['foto']) ? '../assets/img/pegawai/' . basename($row['foto']) : 'https://via.placeholder.com/120?text=No+Foto';
                ?>
                <div class="calon-card">
                    <?php if ($rank <= 3): ?>
                        <div class="card-rank rank-<?= $rank ?>">
                            <?= $rank ?>
                        </div>
                    <?php endif; ?>
                    <img src="<?= htmlspecialchars($foto_url) ?>" alt="Foto <?= htmlspecialchars($row['nama']) ?>" class="calon-foto">
                    <div class="card-info">
                        <h3><?= htmlspecialchars($row['nama']) ?></h3>
                        <p>NIP: <?= htmlspecialchars($row['nip']) ?></p>
                        <p>Triwulan: <?= htmlspecialchars($row['triwulan']) ?></p>
                        <p>Tahun: <?= htmlspecialchars($row['tahun']) ?></p>
                    </div>
                    <div class="score-info">
                        <span class="total-score"><?= htmlspecialchars($row['total_skor']) ?></span>
                        <p>Total Skor</p>
                        <span class="average-score"><?= number_format($row['rata_rata_skor'], 2) ?></span>
                        <p>Rata-rata Skor (dari <?= htmlspecialchars($row['jumlah_penilai']) ?> penilai)</p>
                    </div>
                </div>
                <?php
                    }
                } else {
                    echo '<div class="col-md-12"><p class="text-center">Belum ada data penilaian untuk filter yang dipilih.</p></div>';
                }
                ?>
            </div>
        </div>
    </section>
</div>

<?php 
include '../includes/footer.php'; 
mysqli_close($koneksi);
?>