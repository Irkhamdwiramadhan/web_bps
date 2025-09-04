<?php
session_start();
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

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

// Menangkap nilai pencarian dan filter dari URL (metode GET)
$search_query = $_GET['q'] ?? '';
$kecamatan_filter = $_GET['kecamatan'] ?? '';

// Ambil data kecamatan dari database untuk dropdown filter
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
        font-size: 0.85rem;
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

    /* Styling Tabel (New) */
    .data-table-container {
        overflow-x: auto;
        margin-top: 25px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        background-color: #fff;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table th,
    .data-table td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #f0f0f0;
    }

    .data-table thead th {
        background-color: #f9fafb;
        font-weight: 600;
        color: #777;
        text-transform: uppercase;
        font-size: 0.9rem;
    }

    .data-table tbody tr:hover {
        background-color: #f5f5f5;
    }

    /* Media Queries untuk Desain Responsif */
    @media (max-width: 768px) {
        .data-table-container {
            border-radius: 0;
            box-shadow: none;
        }
        .data-table {
            border: 1px solid #ccc;
            border-radius: 8px;
            overflow: hidden;
            margin: 10px 0;
        }
        .data-table thead {
            display: none; /* Menyembunyikan header tabel */
        }
        .data-table,
        .data-table tbody,
        .data-table tr,
        .data-table td {
            display: block; /* Mengubah sel menjadi blok */
            width: 100%;
            box-sizing: border-box;
        }
        .data-table tr {
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            background-color: #fff;
        }
        .data-table td {
            text-align: right;
            padding-left: 50%; /* Memberi ruang untuk data-label */
            position: relative;
        }
        .data-table td::before {
            content: attr(data-label);
            position: absolute;
            left: 10px;
            width: 45%;
            text-align: left;
            font-weight: bold;
            color: #555;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .data-table td:first-of-type {
            padding-top: 15px;
        }
        .data-table td:last-of-type {
            border-bottom: none;
        }
        .btn-action {
            margin: 5px;
        }
    }
</style>

<div class="content-wrapper min-h-screen">
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

        <div class="data-table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Lengkap</th>
                        <th>NIK</th>
                        <th>Pekerjaan</th>
                        <th>Pendidikan</th>
                        <th>Kecamatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
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
                        $no = 1;

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                    ?>
                                <tr>
                                    <td data-label="No."><?= $no++ ?></td>
                                    <td data-label="Nama Lengkap"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                    <td data-label="NIK"><?= htmlspecialchars($row['nik']) ?></td>
                                    <td data-label="Pekerjaan"><?= htmlspecialchars($row['pekerjaan'] ?? '-') ?></td>
                                    <td data-label="Pendidikan"><?= htmlspecialchars($row['pendidikan'] ?? '-') ?></td>
                                    <td data-label="Kecamatan"><?= htmlspecialchars($row['nama_kecamatan'] ?? '-') ?></td>
                                    <td data-label="Aksi">
                                        <div class="flex flex-col md:flex-row gap-2">
                                            <a href="detail_mitra.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn-action btn-detail">Detail</a>
                                            <a href="edit_mitra.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn-action btn-edit">Edit</a>
                                            <a href="delete_mitra.php?id=<?= htmlspecialchars($row['id']) ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus mitra ini?');" class="btn-action btn-delete">Hapus</a>
                                        </div>
                                    </td>
                                </tr>
                    <?php
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center py-10'>Tidak ada data mitra yang ditemukan.</td></tr>";
                        }
                        
                        $stmt->close();

                    } catch (Exception $e) {
                        echo "<tr><td colspan='7' class='text-center py-10 text-red-500'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>