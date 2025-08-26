<?php
// Mulai sesi dan sertakan file koneksi database, header, dan sidebar
session_start();
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Ambil ID dari URL
$survey_id = $_GET['id'] ?? null;

// Validasi ID
if (!$survey_id || !is_numeric($survey_id)) {
    echo "<div class='content-wrapper'><div class='card p-6 text-center text-red-500 font-semibold'>ID Survei tidak valid.</div></div>";
    include '../includes/footer.php';
    exit;
}

// Ambil data survei berdasarkan ID
$sql_survey = "SELECT id, nama_survei, singkatan_survei, satuan, seksi_terdahulu, nama_tim_sekarang FROM surveys WHERE id = ?";
$stmt_survey = $koneksi->prepare($sql_survey);
$stmt_survey->bind_param("i", $survey_id);
$stmt_survey->execute();
$result_survey = $stmt_survey->get_result();
$survey_detail = $result_survey->fetch_assoc();
$stmt_survey->close();

// Jika survei tidak ditemukan
if (!$survey_detail) {
    echo "<div class='content-wrapper'><div class='card p-6 text-center text-red-500 font-semibold'>Survei tidak ditemukan.</div></div>";
    include '../includes/footer.php';
    exit;
}

// Ambil data mitra yang ikut serta dalam survei ini dengan JOIN tabel mitra_survey dan mitra
// Perbaikan: Menggunakan nama kolom yang benar dari tabel 'mitra'
$sql_mitra = "SELECT m.nama_lengkap, m.nik FROM mitra_surveys ms JOIN mitra m ON ms.mitra_id = m.id WHERE ms.survei_id = ?";
$stmt_mitra = $koneksi->prepare($sql_mitra);
$stmt_mitra->bind_param("i", $survey_id);
$stmt_mitra->execute();
$result_mitra = $stmt_mitra->get_result();
$mitra_list = [];
while ($row = $result_mitra->fetch_assoc()) {
    $mitra_list[] = $row;
}
$stmt_mitra->close();
$koneksi->close();

?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    
    body {
        font-family: 'Poppins', sans-serif;
        background: #eef2f5;
        color: #374151;
    }
    .content-wrapper {
        padding: 1rem;
    }
    @media (min-width: 640px) {
        .content-wrapper {
            margin-left: 16rem;
            padding-top: 2rem;
        }
    }
    .card {
        background-color: #ffffff;
        border-radius: 1rem;
        padding: 2rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    .detail-item {
        margin-bottom: 1.5rem;
    }
    .detail-item label {
        display: block;
        font-weight: 600;
        color: #4b5563;
        margin-bottom: 0.25rem;
    }
    .detail-item p {
        font-size: 1rem;
        color: #6b7280;
    }
    .list-container {
        max-height: 400px;
        overflow-y: auto;
    }
    .list-item {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e5e7eb;
    }
    .list-item:last-child {
        border-bottom: none;
    }
    .btn-back {
        background-color: #6b7280;
        color: #fff;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        transition: background-color 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-back:hover {
        background-color: #4b5563;
    }
</style>

<div class="content-wrapper">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center gap-4 mb-6">
            <a href="jenis_surveys.php" class="btn-back">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Kembali
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Detail Survei</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="card">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Informasi Dasar Survei</h2>
                
                <div class="detail-item">
                    <label>Nama Survei</label>
                    <p><?= htmlspecialchars($survey_detail['nama_survei']) ?></p>
                </div>
                <div class="detail-item">
                    <label>Singkatan</label>
                    <p><?= htmlspecialchars($survey_detail['singkatan_survei']) ?></p>
                </div>
                <div class="detail-item">
                    <label>Satuan</label>
                    <p><?= htmlspecialchars($survey_detail['satuan']) ?></p>
                </div>
                <div class="detail-item">
                    <label>Seksi Terdahulu</label>
                    <p><?= htmlspecialchars($survey_detail['seksi_terdahulu']) ?></p>
                </div>
                <div class="detail-item">
                    <label>Nama Tim Sekarang</label>
                    <p><?= htmlspecialchars($survey_detail['nama_tim_sekarang']) ?></p>
                </div>
            </div>

            <div class="card">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Mitra yang Terlibat</h2>
                
                <?php if (count($mitra_list) > 0) : ?>
                    <div class="list-container">
                        <?php foreach ($mitra_list as $mitra) : ?>
                            <div class="list-item">
                                <div>
                                    <p class="font-medium"><?= htmlspecialchars($mitra['nama_lengkap']) ?></p>
                                    <p class="text-sm text-gray-500">NIM: <?= htmlspecialchars($mitra['nik']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <p class="text-center text-gray-500 italic">Belum ada mitra yang terdaftar untuk survei ini.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php 
include '../includes/footer.php'; 
?>