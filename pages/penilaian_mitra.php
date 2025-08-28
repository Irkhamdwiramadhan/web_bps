<?php
// Mulai sesi dan sertakan file koneksi database, header, dan sidebar
session_start();
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Tangkap parameter pencarian dari URL
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

try {
    // Awal kueri dasar
    $sql_penilaian = "SELECT
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
                        surveys AS s ON ms.survei_id = s.id";
    
    // Tambahkan kondisi pencarian jika ada
    if (!empty($search_query)) {
        $sql_penilaian .= " WHERE m.nama_lengkap LIKE ?";
    }

    // Tambahkan pengurutan
    $sql_penilaian .= " ORDER BY mpk.tanggal_penilaian DESC";

    $stmt_penilaian = $koneksi->prepare($sql_penilaian);
    
    // Bind parameter pencarian jika ada
    if (!empty($search_query)) {
        $search_param = "%" . $search_query . "%";
        $stmt_penilaian->bind_param("s", $search_param);
    }
    
    $stmt_penilaian->execute();
    $result_penilaian = $stmt_penilaian->get_result();

} catch (Exception $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
    exit;
}
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    
    body { font-family: 'Poppins', sans-serif; background: #eef2f5; }
    .content-wrapper { padding: 1rem; transition: margin-left 0.3s ease; }
    @media (min-width: 640px) { .content-wrapper { margin-left: 16rem; padding-top: 2rem; } }
    .btn-add {
        background-color: #28a745;
        color: #fff;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: 600;
        text-decoration: none;
        transition: background-color 0.2s;
    }
    .btn-add:hover {
        background-color: #218838;
    }
    .search-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 1rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .search-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        outline: none;
    }
    .card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }
    .mitra-card {
        background-color: #ffffff;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    .mitra-photo-container {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        overflow: hidden;
        margin-bottom: 1rem;
        border: 3px solid #f3f4f6;
    }
    .mitra-photo {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .mitra-name {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }
    .survey-info {
        font-size: 1rem;
        color: #6b7280;
        margin-bottom: 1rem;
    }
    .rating-badge {
        background-color: #e5e7eb;
        color: #4b5563;
        font-size: 1.25rem;
        font-weight: 700;
        padding: 0.5rem 1rem;
        border-radius: 9999px;
    }
    .actions-row {
        margin-top: 1rem;
        width: 100%;
        display: flex;
        justify-content: center;
        gap: 0.75rem;
    }
    .btn-action {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        text-align: center;
        text-decoration: none;
        transition: background-color 0.2s;
    }
    .btn-detail {
        background-color: #3b82f6;
        color: #fff;
    }
    .btn-detail:hover {
        background-color: #2563eb;
    }
    .btn-delete {
        background-color: #ef4444;
        color: #fff;
    }
    .btn-delete:hover {
        background-color: #dc2626;
    }
</style>

<div class="content-wrapper">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Riwayat Penilaian Kinerja Mitra</h1>
            <div class="flex flex-col sm:flex-row sm:justify-end gap-4 mt-4 md:mt-0 w-full md:w-auto">
                <form action="penilaian_mitra.php" method="GET" class="w-full sm:w-auto">
                    <input type="text" name="search" placeholder="Cari nama mitra..." class="search-input" value="<?= htmlspecialchars($search_query); ?>">
                </form>
                <br>
                <a href="tambah_penilaian_mitra.php" class="btn-add w-full sm:w-auto text-center">Tambah Penilaian</a>
            </div>
            <br>
        </div>
        
        <?php if ($result_penilaian->num_rows > 0) : ?>
            <div class="card-grid">
                <?php while ($row = $result_penilaian->fetch_assoc()) : ?>
                    <div class="mitra-card">
                        <div class="mitra-photo-container">
                            <?php 
                                $photo_path = !empty($row['foto_mitra']) ? '../uploads/' . $row['foto_mitra'] : 'https://via.placeholder.com/150';
                                $alt_text = !empty($row['foto_mitra']) ? 'Foto ' . $row['nama_mitra'] : 'Placeholder';
                            ?>
                            <img src="<?= htmlspecialchars($photo_path); ?>" alt="<?= htmlspecialchars($alt_text); ?>" class="mitra-photo">
                        </div>
                        <h3 class="mitra-name"><?= htmlspecialchars($row['nama_mitra']); ?></h3>
                        <p class="survey-info">
                            <?= htmlspecialchars($row['jenis_survei']); ?> (<?= htmlspecialchars($row['urutan_survei']); ?>)
                        </p>
                        <div class="rating-badge">
                            <?= number_format($row['rata_rata_penilaian'], 2); ?>
                        </div>
                        <div class="actions-row">
                            <a href="detail_penilaian.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn-action btn-detail">Detail</a>
                            <a href="../proses/delete_penilaian.php?id=<?= htmlspecialchars($row['id']) ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus penilaian ini?');" class="btn-action btn-delete">Hapus</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <div class="text-center text-gray-500 py-10">
                <p>Tidak ada data penilaian yang ditemukan.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php 
$stmt_penilaian->close();
$koneksi->close();
include '../includes/footer.php'; 
?>