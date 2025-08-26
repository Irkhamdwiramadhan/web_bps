<?php
session_start();
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Menangkap nilai pencarian dan filter dari URL (metode GET)
$search_query = $_GET['q'] ?? '';
$kecamatan_filter = $_GET['kecamatan'] ?? '';

// Ambil data kecamatan dari database untuk dropdown filter
// Query ini tidak menggunakan input user, jadi tidak perlu prepared statement.
$sql_kecamatan = "SELECT DISTINCT nama_kecamatan FROM mitra ORDER BY nama_kecamatan ASC";
$result_kecamatan = $koneksi->query($sql_kecamatan);
$kecamatan_list = [];
if ($result_kecamatan && $result_kecamatan->num_rows > 0) {
    while ($row = $result_kecamatan->fetch_assoc()) {
        if (!empty($row['nama_kecamatan'])) {
            $kecamatan_list[] = $row['nama_kecamatan'];
        }
    }
}
?>

<style>
    /* Import Font Poppins dari Google Fonts */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    
    body {
        font-family: 'Poppins', sans-serif;
        background: #eef2f5;
    }

    /* Perbaikan layout untuk menghindari tabrakan dengan sidebar */
    .content-wrapper {
        padding: 1rem;
        transition: margin-left 0.3s ease-in-out;
    }
    
    @media (min-width: 640px) {
        .content-wrapper {
            margin-left: 16rem; /* Sesuai lebar sidebar */
            padding-top: 2rem;
        }
    }

    /* Styling tombol baru dengan efek 3D dan gradien */
    .btn-action {
        border-radius: 9999px;
        padding: 0.5rem 1.25rem;
        font-weight: 600;
        text-transform: uppercase;
        color: white;
        background-size: 200% auto;
        transition: all 0.4s ease-in-out;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        display: inline-block;
    }

    .btn-detail {
        background-image: linear-gradient(to right, #3b82f6 0%, #2563eb 50%, #3b82f6 100%);
    }
    .btn-detail:hover {
        background-position: right center;
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
    }
    
    .btn-edit {
        background-image: linear-gradient(to right, #f59e0b 0%, #d97706 50%, #f59e0b 100%);
    }
    .btn-edit:hover {
        background-position: right center;
        box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
    }

    .btn-delete {
        background-image: linear-gradient(to right, #ef4444 0%, #dc2626 50%, #ef4444 100%);
    }
    .btn-delete:hover {
        background-position: right center;
        box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
    }
    
    /* Tombol untuk form di atas */
    .btn-form {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: 700;
        transition: background-color 0.3s ease;
        color: white;
    }
    .btn-blue-form {
        background-color: #3b82f6;
    }
    .btn-blue-form:hover {
        background-color: #2563eb;
    }

    /* Styling kartu data mitra */
    .mitra-card-container {
        display: grid;
        gap: 1.5rem;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    }
    
    .mitra-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 1rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    }
    .mitra-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .mitra-card .profile-img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 1rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .mitra-card h2 {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .mitra-card p {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 0.25rem;
    }

    .mitra-card .info-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
    
    .mitra-card .info-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .mitra-card .info-item i {
        color: #4b5563;
    }

    .mitra-card .actions {
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
        margin-top: 1rem;
    }
</style>

<div class="content-wrapper bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto bg-white rounded-xl shadow-lg p-6 mt-14">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">Daftar Mitra BPS</h1>

        <form action="mitra.php" method="GET" class="flex flex-col md:flex-row justify-between items-center mb-6 space-y-4 md:space-y-0">
            <input type="text" name="q" placeholder="Cari berdasarkan Nama atau NIK..." class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300" value="<?php echo htmlspecialchars($search_query); ?>">

            <select id="kecamatanFilter" name="kecamatan" class="w-full md:w-1/4 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300">
                <option value="">Semua Kecamatan</option>
                <?php
                foreach ($kecamatan_list as $kec) {
                    $selected = ($kecamatan_filter == $kec) ? 'selected' : '';
                    echo "<option value='" . htmlspecialchars($kec) . "' " . $selected . ">" . htmlspecialchars($kec) . "</option>";
                }
                ?>
            </select>
            <button type="submit" class="btn-form btn-blue-form shadow-md">Cari</button>
            <a href="tambah_mitra.php" class="btn-form btn-blue-form shadow-md"> + Tambah Mitra </a>
        </form>

        <div class="mitra-card-container">
            <?php
            // **Penting: Penggunaan Prepared Statement untuk Mencegah SQL Injection**
            $sql = "SELECT id, nama_lengkap, nik, foto, pekerjaan, pendidikan, nama_kecamatan FROM mitra WHERE 1=1";
            $types = "";
            $params = [];

            if (!empty($search_query)) {
                $sql .= " AND (nama_lengkap LIKE ? OR nik LIKE ?)";
                $types .= "ss";
                $params[] = "%" . $search_query . "%";
                $params[] = "%" . $search_query . "%";
            }

            if (!empty($kecamatan_filter)) {
                $sql .= " AND nama_kecamatan = ?";
                $types .= "s";
                $params[] = $kecamatan_filter;
            }

            $sql .= " ORDER BY nama_lengkap ASC";

            try {
                $stmt = $koneksi->prepare($sql);

                // Periksa apakah statement berhasil disiapkan
                if ($stmt === false) {
                    throw new Exception("Gagal menyiapkan statement: " . $koneksi->error);
                }

                // Jika ada parameter, lakukan binding
                if (!empty($types)) {
                    $stmt->bind_param($types, ...$params);
                }
                
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Koreksi: Hilangkan 'uploads/' di dalam path karena sudah ada di database
                        $foto_path = !empty($row['foto']) ? "../uploads/" . htmlspecialchars($row['foto']) : '';
            ?>
                        <div class="mitra-card">
                            <div class="flex items-center justify-center mb-4">
                                <?php if (!empty($row['foto'])): ?>
                                    <img src="<?= $foto_path ?>" alt="Foto <?= htmlspecialchars($row['nama_lengkap']) ?>" class="profile-img">
                                <?php else: ?>
                                    <div class="w-20 h-20 rounded-full bg-gray-300 flex items-center justify-center text-xs text-gray-600">Tidak Ada Foto</div>
                                <?php endif; ?>
                            </div>
                            <div class="text-center mb-4">
                                <h2 class="text-xl font-semibold text-gray-900"><?= htmlspecialchars($row['nama_lengkap']) ?></h2>
                                <p class="text-gray-500 text-sm"><?= htmlspecialchars($row['nama_kecamatan'] ?? '-') ?></p>
                            </div>
                            <div class="info-grid text-gray-700">
                                <div class="info-item">
                                    <i class="fas fa-id-card"></i>
                                    <p><strong>NIK:</strong> <?= htmlspecialchars($row['nik']) ?></p>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-briefcase"></i>
                                    <p><strong>Pekerjaan:</strong> <?= htmlspecialchars($row['pekerjaan'] ?? '-') ?></p>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-user-graduate"></i>
                                    <p><strong>Pendidikan:</strong> <?= htmlspecialchars($row['pendidikan'] ?? '-') ?></p>
                                </div>
                            </div>
                            <div class="actions mt-4 flex justify-between">
                                <a href="detail_mitra.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn-action btn-detail">
                                    Detail
                                </a>
                                <a href="edit_mitra.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn-action btn-edit">
                                    Edit
                                </a>
                                <a href="delete_mitra.php?id=<?= htmlspecialchars($row['id']) ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus mitra ini?');" class="btn-action btn-delete">
                                    Hapus
                                </a>
                            </div>
                        </div>
            <?php
                    }
                } else {
                    echo "<div class='text-center py-10 w-full'>Tidak ada data mitra yang ditemukan.</div>";
                }
                
                $stmt->close();

            } catch (Exception $e) {
                // Tampilkan pesan error jika terjadi kesalahan
                echo "<div class='text-center py-10 w-full text-red-500'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
            ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
