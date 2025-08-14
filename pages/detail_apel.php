<?php
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM apel WHERE id = $id";
    $result = $koneksi->query($sql);

    if ($result->num_rows > 0) {
        $data_apel = $result->fetch_assoc();
        $kehadiran_list = json_decode($data_apel['kehadiran'], true);
    } else {
        echo "<main class='main-content'><div class='card'>Data apel tidak ditemukan.</div></main>";
        include '../includes/footer.php';
        exit;
    }
} else {
    echo "<main class='main-content'><div class='card'>ID apel tidak ditemukan.</div></main>";
    include '../includes/footer.php';
    exit;
}
?>

<main class="main-content">
    <div class="header-content">
        <h2>Detail Apel Tanggal <?php echo htmlspecialchars($data_apel['tanggal']); ?></h2>
        <a href="apel.php" class="btn btn-primary">Kembali</a>
    </div>

    <div class="card detail-card">
        <div class="apel-info">
            <p><strong>Tanggal:</strong> <?php echo htmlspecialchars($data_apel['tanggal']); ?></p>
            <p><strong>Kondisi Apel:</strong> <?php echo htmlspecialchars($data_apel['kondisi_apel']); ?></p>
            <p><strong>Petugas Apel:</strong> <?php echo htmlspecialchars($data_apel['petugas']); ?></p>
            <p><strong>Komando:</strong> <?php echo htmlspecialchars($data_apel['komando']); ?></p>
            <p><strong>Pemimpin Doa:</strong> <?php echo htmlspecialchars($data_apel['pemimpin_doa']); ?></p>
            <p><strong>Pembina Apel:</strong> <?php echo htmlspecialchars($data_apel['pembina_apel']); ?></p>
            <?php if ($data_apel['kondisi_apel'] === 'tidak_ada'): ?>
                <p><strong>Alasan Tidak Dilaksanakan:</strong> <?php echo htmlspecialchars($data_apel['alasan_tidak_ada']); ?></p>
            <?php else: ?>
                <p><strong>Catatan Umum:</strong> <?php echo htmlspecialchars($data_apel['keterangan']); ?></p>
            <?php endif; ?>
        <h3>Foto Bukti</h3>
        <img src="../assets/img/bukti_apel/<?php echo htmlspecialchars($data_apel['foto_bukti']); ?>" alt="Foto Bukti Apel" class="bukti-foto">

        <h3>Daftar Kehadiran Pegawai</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Nama Pegawai</th>
                <th>Status</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $kehadiran_data = json_decode($data_apel['kehadiran'], true);
            
            if (is_array($kehadiran_data)) {
                foreach ($kehadiran_data as $data) {
                    $sql_pegawai = "SELECT nama FROM pegawai WHERE id = " . $data['id_pegawai'];
                    $result_pegawai = $koneksi->query($sql_pegawai);
                    $pegawai = $result_pegawai->fetch_assoc();
                    
                    // Ambil status dan ubah tampilannya
                    $status_db = $data['status'];
                    $display_status = ucwords(str_replace('_', ' ', $status_db));
                    $status_class = str_replace('_', '-', $status_db);
                    
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($pegawai['nama']) . "</td>";
                    echo "<td><span class='status " . $status_class . "'>" . htmlspecialchars($display_status) . "</span></td>";
                    echo "<td>" . htmlspecialchars($data['catatan']) . "</td>";
                    echo "</tr>";
                }
            }
            ?>
        </tbody>
    </table>
    </div>
</main>

<?php include '../includes/footer.php'; ?>