<?php
// Mulai sesi dan sertakan file koneksi database, header, dan sidebar
session_start();
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Ambil ID penilaian dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id === 0) {
    header('Location: penilaian_mitra.php?status=error&message=ID_penilaian_tidak_valid');
    exit;
}

try {
    // Kueri untuk mengambil detail penilaian berdasarkan ID
    $sql_detail = "SELECT
                        mpk.id,
                        m.nama_lengkap AS nama_mitra,
                        m.foto AS foto_mitra,
                        p.nama AS nama_penilai,
                        s.nama_survei AS jenis_survei,
                        ms.survey_ke_berapa AS urutan_survei,
                        mpk.beban_kerja,
                        mpk.kualitas,
                        mpk.volume_pemasukan,
                        mpk.perilaku,
                        (mpk.beban_kerja + mpk.kualitas + mpk.volume_pemasukan + mpk.perilaku) / 4 AS rata_rata_penilaian,
                        mpk.tanggal_penilaian
                      FROM
                        mitra_penilaian_kinerja AS mpk
                      JOIN
                        mitra_surveys AS ms ON mpk.mitra_survey_id = ms.id
                      JOIN
                        mitra AS m ON ms.mitra_id = m.id
                      JOIN
                        pegawai AS p ON mpk.penilai_id = p.id
                      JOIN
                        surveys AS s ON ms.survei_id = s.id
                      WHERE
                        mpk.id = ?";
    
    $stmt_detail = $koneksi->prepare($sql_detail);
    if (!$stmt_detail) {
        throw new Exception("Gagal menyiapkan statement: " . $koneksi->error);
    }
    
    $stmt_detail->bind_param("i", $id);
    $stmt_detail->execute();
    $result_detail = $stmt_detail->get_result();
    $detail = $result_detail->fetch_assoc();

    if (!$detail) {
        throw new Exception("Data penilaian tidak ditemukan.");
    }

} catch (Exception $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
    exit;
}
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    
    body { font-family: 'Poppins', sans-serif; background: #f0f4f8; }
    .content-wrapper { padding: 1rem; transition: margin-left 0.3s ease; }
    @media (min-width: 640px) { .content-wrapper { margin-left: 16rem; padding-top: 2rem; } }
    .card { background-color: #ffffff; border-radius: 1rem; padding: 2rem; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05); }
    .btn-back {
        background-color: #6b7280;
        color: #fff;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        text-decoration: none;
        transition: background-color 0.2s;
    }
    .btn-back:hover {
        background-color: #4b5563;
    }
    .mitra-photo-container {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid #e2e8f0;
    }
    .mitra-photo {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .info-item {
        margin-bottom: 1.5rem;
    }
    .info-label {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .info-value {
        font-size: 1.125rem;
        color: #1f2937;
        font-weight: 600;
    }
    .rating-badge-lg {
        background-color: #d1fae5;
        color: #065f46;
        font-size: 2rem;
        font-weight: 700;
        padding: 0.5rem 1.5rem;
        border-radius: 9999px;
        display: inline-block;
    }
    .table-container {
        overflow-x: auto;
    }
    .table-details {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1.5rem;
    }
    .table-details th, .table-details td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
    }
    .table-details th {
        background-color: #f8fafc;
        font-weight: 600;
        color: #4b5563;
    }
    .table-details tr:last-child td {
        border-bottom: none;
    }
</style>

<div class="content-wrapper">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Detail Penilaian Mitra</h1>
            <a href="penilaian_mitra.php" class="btn-back mt-4 md:mt-0">Kembali</a>
        </div>
        <div class="card">
            <div class="flex flex-col sm:flex-row items-center sm:space-x-6 mb-6">
                <div class="mitra-photo-container flex-shrink-0">
                    <?php 
                        $photo_path = !empty($detail['foto_mitra']) ? '../uploads/' . $detail['foto_mitra'] : 'https://via.placeholder.com/150';
                        $alt_text = !empty($detail['foto_mitra']) ? 'Foto ' . $detail['nama_mitra'] : 'Placeholder';
                    ?>
                    <img src="<?= htmlspecialchars($photo_path); ?>" alt="<?= htmlspecialchars($alt_text); ?>" class="mitra-photo">
                </div>
                <div class="mt-4 sm:mt-0 text-center sm:text-left">
                    <h2 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($detail['nama_mitra']); ?></h2>
                    <p class="text-gray-600">Tanggal Penilaian: **<?= htmlspecialchars(date('d F Y', strtotime($detail['tanggal_penilaian']))); ?>**</p>
                    <p class="text-gray-600">Nama Penilai: **<?= htmlspecialchars($detail['nama_penilai']); ?>**</p>
                </div>
            </div>

            <hr class="my-6 border-t-2 border-gray-100">
            
            <h3 class="text-xl font-semibold text-gray-700 mb-4">Informasi Penilaian:</h3>
            <div class="table-container">
                <table class="table-details">
                    <thead>
                        <tr>
                            <th>Survey Ke</th>
                            <th>Jenis Survey</th>
                            <th>Beban Kerja</th>
                            <th>Kualitas</th>
                            <th>Volume Pemasukan</th>
                            <th>Perilaku</th>
                            <th>Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= htmlspecialchars($detail['urutan_survei']); ?></td>
                            <td><?= htmlspecialchars($detail['jenis_survei']); ?></td>
                            <td><?= htmlspecialchars($detail['beban_kerja']); ?>/10</td>
                            <td><?= htmlspecialchars($detail['kualitas']); ?>/4</td>
                            <td><?= htmlspecialchars($detail['volume_pemasukan']); ?>/4</td>
                            <td><?= htmlspecialchars($detail['perilaku']); ?>/4</td>
                            <td><?= number_format($detail['rata_rata_penilaian'], 2); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
$stmt_detail->close();
$koneksi->close();
include '../includes/footer.php'; 
?>