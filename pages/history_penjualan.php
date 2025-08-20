<?php
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Ambil keyword pencarian jika ada
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

$sql_sales_history = "
    SELECT 
        s.id, 
        s.date, 
        s.total, 
        p.nama AS nama_pegawai
    FROM sales s
    JOIN pegawai p ON s.pegawai_id = p.id
";

// Tambahkan filter pencarian
if ($keyword !== '') {
    $keyword_safe = $koneksi->real_escape_string($keyword);
    $sql_sales_history .= " 
        WHERE s.id LIKE '%$keyword_safe%'
        OR p.nama LIKE '%$keyword_safe%'
        OR s.date LIKE '%$keyword_safe%'
    ";
}

$sql_sales_history .= " ORDER BY s.date DESC";
$result_sales_history = $koneksi->query($sql_sales_history);
?>

<main class="main-content">
    <div class="header-content">
        <h2>Riwayat Penjualan</h2>
    </div>
    
    <!-- Form Pencarian -->
    <div class="card search-card">
        <form method="get" action="" class="search-form">
            <input type="text" name="keyword" placeholder="Cari ID, Nama Pegawai, atau Tanggal..."
                   value="<?php echo htmlspecialchars($keyword); ?>" 
                   class="form-control search-input">
            <button type="submit" class="btn btn-primary">Cari</button>
            <?php if ($keyword !== ''): ?>
                <a href="history_penjualan.php" class="btn btn-secondary">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="alert alert-success">Penjualan berhasil dihapus.</div>
    <?php endif; ?>
    <?php if (isset($_GET['status']) && $_GET['status'] == 'error'): ?>
        <div class="alert alert-danger">Gagal menghapus penjualan: <?php echo htmlspecialchars($_GET['message']); ?></div>
    <?php endif; ?>
    
    <div class="card data-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID Penjualan</th>
                    <th>Nama Pegawai</th>
                    <th>Tanggal & Waktu</th>
                    <th>Total</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_sales_history && $result_sales_history->num_rows > 0) {
                    while($row = $result_sales_history->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nama_pegawai']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                        echo "<td>Rp " . number_format($row['total'], 0, ',', '.') . "</td>";
                        echo "<td>";
                        echo "<a href='detail_penjualan.php?id=" . $row['id'] . "' class='btn-action'>Detail</a>";
                        // Ganti onclick dengan data-id untuk modal
                        echo "<button type='button' class='btn-action delete-btn' data-id='" . $row['id'] . "'>Hapus</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' style='text-align:center;'>Tidak ada riwayat penjualan.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</main>

<!-- Custom Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus data penjualan ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <a href="#" id="confirmDeleteButton" class="btn btn-danger">Hapus</a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // Tangani klik pada tombol 'Hapus'
    $(document).on('click', '.delete-btn', function() {
        const penjualanId = $(this).data('id');
        const deleteUrl = '../proses/proses_hapus_penjualan.php?id=' + penjualanId;
        
        // Atur URL hapus pada tombol di modal
        $('#confirmDeleteButton').attr('href', deleteUrl);
        
        // Tampilkan modal
        $('#deleteModal').modal('show');
    });
});
</script>
<style>
/* Main Content and Container Styling */
.main-content {
    padding: 2rem;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.header-content h2 {
    font-weight: 600;
    color: #333;
    margin: 0;
}

/* Card Styling */
.card {
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    margin-bottom: 20px;
}

.search-card {
    padding: 15px;
}

.search-form {
    display: flex;
    gap: 10px;
    align-items: center;
}

.search-input {
    flex-grow: 1;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 8px;
}

/* Table Styling */
.data-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

.data-table thead th {
    background-color: #007bff;
    color: #fff;
    padding: 12px;
    text-align: left;
    border: 1px solid #007bff;
}

.data-table tbody td {
    padding: 12px;
    border: 1px solid #e0e0e0;
    vertical-align: middle;
}

.data-table tbody tr:nth-child(even) {
    background-color: #f8f9fa;
}

/* Action Buttons */
.btn-action {
    display: inline-block;
    padding: 8px 12px;
    border-radius: 8px;
    text-decoration: none;
    color: #fff;
    font-weight: 500;
    text-align: center;
    transition: background-color 0.3s ease;
    margin-right: 5px;
    /* Perbaikan untuk membuat tombol Detail terlihat */
    background-color: #007bff; /* Menambahkan warna latar belakang biru */
    border: 1px solid #007bff; /* Menambahkan border */
}

.btn-action:hover {
    background-color: #0056b3; /* Warna hover yang lebih gelap */
    border-color: #0056b3;
}

.btn-action.delete-btn {
    background-color: #dc3545;
    border: none;
    cursor: pointer;
}

.btn-action.delete-btn:hover {
    background-color: #c82333;
}

/* Modal Styling */
.modal {
    display: none;
    position: fixed;
    z-index: 1050;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    outline: 0;
    background-color: rgba(0,0,0,0.5);
}
.modal-dialog {
    position: relative;
    width: auto;
    margin: 1.75rem auto;
}
.modal-dialog-centered {
    display: flex;
    align-items: center;
    min-height: calc(100% - 3.5rem);
}
.modal-content {
    position: relative;
    background-color: #fff;
    border: 1px solid rgba(0,0,0,.2);
    border-radius: 12px;
    outline: 0;
    width: 90%;
    max-width: 500px;
    margin: 0 auto;
}
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
}
.modal-body {
    position: relative;
    flex: 1 1 auto;
    padding: 1rem;
}
.modal-footer {
    display: flex;
    justify-content: flex-end;
    padding: 1rem;
    border-top: 1px solid #e9ecef;
}
.close {
    font-size: 1.5rem;
}
</style>
