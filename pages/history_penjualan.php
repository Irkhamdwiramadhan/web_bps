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
    <form method="get" action="" style="margin-bottom:15px;">
        <input type="text" name="keyword" placeholder="Cari ID, Nama Pegawai, atau Tanggal"
               value="<?php echo htmlspecialchars($keyword); ?>" 
               style="padding:8px; width:250px;">
        <button type="submit" style="padding:8px 12px;">Cari</button>
        <?php if ($keyword !== ''): ?>
            <a href="riwayat_penjualan.php" style="margin-left:10px; text-decoration:none; color:red;">Reset</a>
        <?php endif; ?>
    </form>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="alert alert-success">Penjualan berhasil dihapus.</div>
    <?php endif; ?>
    <?php if (isset($_GET['status']) && $_GET['status'] == 'error'): ?>
        <div class="alert alert-danger">Gagal menghapus penjualan: <?php echo htmlspecialchars($_GET['message']); ?></div>
    <?php endif; ?>
    
    <div class="card">
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
                        echo "<a href='../proses/proses_hapus_penjualan.php?id=" . $row['id'] . "' class='btn-action delete' onclick='return confirm(\"Apakah Anda yakin ingin menghapus penjualan ini?\")'>Hapus</a>";
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

<?php include '../includes/footer.php'; ?>
