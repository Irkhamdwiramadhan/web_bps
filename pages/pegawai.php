<?php
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main class="main-content">
    <div class="header-content">
        <h2>Data Pegawai</h2>
        <a href="tambah_pegawai.php" class="btn btn-primary">Tambah Pegawai</a>
    </div>

    <div class="card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>NIP</th>
                    <th>NIP BPS</th>
                    <th>Gol. Akhir</th>
        
                    <th>Seksi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $sql = "SELECT * FROM pegawai";
                $result = $koneksi->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nip']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nip_bps']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['gol_akhir']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['seksi']) . "</td>";
                        echo "<td>
                            <div class='btn-action-group'>
                                <a href='detail_pegawai.php?id=" . $row['id'] . "' class='btn-action detail'>Detail</a>
                                <a href='edit_pegawai.php?id=" . $row['id'] . "' class='btn-action edit'>Edit</a>
                                <a href='../proses/proses_hapus.php?id=" . $row['id'] . "' class='btn-action delete' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\")'>Hapus</a>
                            </div>
                        </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='11' style='text-align:center;'>Tidak ada data pegawai.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</main>

<?php include '../includes/footer.php'; ?>