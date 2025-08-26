<?php
// Mulai sesi dan sertakan file koneksi database, header, dan sidebar
session_start();
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

try {
    // Kueri untuk mengambil data partisipasi mitra
    // Kolom 'Status Partisipasi' sekarang ditentukan dari jumlah survei yang diikuti
    $sql_mitra_partisipasi = "SELECT
                                m.id,
                                m.nama_lengkap,
                                COUNT(ms.survei_id) AS jumlah_survei_diikuti,
                                CASE
                                    WHEN COUNT(ms.survei_id) > 0 THEN 'Ikut Kegiatan'
                                    ELSE 'Belum Ikut Kegiatan'
                                END AS status_partisipasi
                             FROM
                                mitra AS m
                             LEFT JOIN
                                mitra_surveys AS ms ON m.id = ms.mitra_id
                             GROUP BY
                                m.id
                             ORDER BY
                                m.nama_lengkap ASC";
    
    $stmt_mitra = $koneksi->prepare($sql_mitra_partisipasi);
    $stmt_mitra->execute();
    $result_mitra = $stmt_mitra->get_result();
    
    // Kueri untuk menghitung jumlah mitra yang sudah ikut kegiatan
    $sql_sudah = "SELECT COUNT(DISTINCT mitra_id) AS jumlah_sudah FROM mitra_surveys";
    $stmt_sudah = $koneksi->prepare($sql_sudah);
    $stmt_sudah->execute();
    $result_sudah = $stmt_sudah->get_result();
    $data_sudah = $result_sudah->fetch_assoc();
    $jumlah_sudah = $data_sudah['jumlah_sudah'];

    // Kueri untuk menghitung jumlah total mitra
    $sql_total = "SELECT COUNT(*) AS total FROM mitra";
    $stmt_total = $koneksi->prepare($sql_total);
    $stmt_total->execute();
    $result_total = $stmt_total->get_result();
    $data_total = $result_total->fetch_assoc();
    $jumlah_total = $data_total['total'];
    $jumlah_belum = $jumlah_total - $jumlah_sudah;

} catch (Exception $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
    exit;
}
?>

<style>
    /* Styling CSS yang sama seperti sebelumnya */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    
    body { font-family: 'Poppins', sans-serif; background: #eef2f5; }
    .content-wrapper { padding: 1rem; transition: margin-left 0.3s ease; }
    @media (min-width: 640px) { .content-wrapper { margin-left: 16rem; padding-top: 2rem; } }
    .card { background-color: #ffffff; border-radius: 1rem; padding: 2rem; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); }
    .summary-card { background-color: #eef2f5; border-radius: 0.75rem; padding: 1.5rem; text-align: center; }
    .summary-card-green { background-color: #d1f7e3; border-left: 5px solid #28a745; }
    .summary-card-red { background-color: #fce8e8; border-left: 5px solid #dc3545; }
    .summary-number { font-size: 2.5rem; font-weight: 700; color: #1f2937; }
    .summary-label { font-size: 1rem; font-weight: 500; color: #6b7280; }
    .table-container { overflow-x: auto; }
    table { width: 100%; border-collapse: separate; border-spacing: 0 0.75rem; }
    thead th { background-color: #e5e7eb; color: #4b5563; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; padding: 1rem 1.5rem; text-align: left; }
    tbody td { background-color: #ffffff; padding: 1rem 1.5rem; border-radius: 0.5rem; }
    tbody tr:hover td { background-color: #f9fafb; }
    tbody tr { box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); }

    /* Gaya untuk tombol aksi */
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
</style>

<div class="content-wrapper">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Halaman Kegiatan Mitra</h1>
            <a href="tambah_kegiatan.php" class="btn-add">Tambah Kegiatan</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
            <div class="summary-card summary-card-green">
                <div class="summary-number"><?= htmlspecialchars($jumlah_sudah); ?></div>
                <div class="summary-label">Mitra Sudah Ikut Kegiatan</div>
            </div>
            <div class="summary-card summary-card-red">
                <div class="summary-number"><?= htmlspecialchars($jumlah_belum); ?></div>
                <div class="summary-label">Mitra Belum Ikut Kegiatan</div>
            </div>
        </div>

        <div class="card">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Detail Partisipasi Mitra</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th class="rounded-l-lg">Nama Mitra</th>
                            <th>Status Partisipasi</th>
                            <th>Jumlah Survei</th>
                            <th class="rounded-r-lg">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_mitra->num_rows > 0) : ?>
                            <?php while ($row = $result_mitra->fetch_assoc()) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['nama_lengkap']); ?></td>
                                    <td><?= htmlspecialchars($row['status_partisipasi']); ?></td>
                                    <td><?= htmlspecialchars($row['jumlah_survei_diikuti']); ?></td>
                                    <td>
                                        <div class="flex space-x-2">
                                            <a href="detail_kegiatan.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn-action btn-detail">Detail</a>
                                            <a href="../proses/delete_kegiatan.php?id=<?= htmlspecialchars($row['id']) ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus data mitra ini?');" class="btn-action btn-delete">Hapus</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="4" class="text-center text-gray-500 py-4">Tidak ada data mitra yang ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
$stmt_mitra->close();
$stmt_sudah->close();
$stmt_total->close();
$koneksi->close();
include '../includes/footer.php'; 
?>