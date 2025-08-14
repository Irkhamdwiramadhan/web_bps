<?php
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

$sql_sales_history = "
    SELECT 
        s.id, 
        s.date, 
        s.total, 
        p.nama AS nama_pegawai
    FROM sales s
    JOIN pegawai p ON s.pegawai_id = p.id
    ORDER BY s.date DESC
";
$result_sales_history = $koneksi->query($sql_sales_history);
?>

<main class="main-content">
    <div class="header-content">
        <h2>Riwayat Penjualan</h2>
    </div>
    
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
                        // Tambahkan tombol hapus dengan konfirmasi JavaScript
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