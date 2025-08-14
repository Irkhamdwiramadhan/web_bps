<?php
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Pastikan ada ID pegawai yang dikirim melalui URL
if (isset($_GET['id'])) {
    // Ambil ID dari URL
    $id = $_GET['id'];

    // Gunakan Prepared Statement untuk keamanan
    $sql = "SELECT * FROM pegawai WHERE id = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("i", $id); // "i" menandakan tipe data integer
    $stmt->execute();
    $result = $stmt->get_result();

    // Cek apakah data ditemukan
    if ($result->num_rows > 0) {
        $pegawai = $result->fetch_assoc();
    } else {
        // Jika data tidak ditemukan, tampilkan pesan error
        echo "<main class='main-content'><div class='card'>Data pegawai tidak ditemukan.</div></main>";
        include '../includes/footer.php';
        exit;
    }
    $stmt->close();
} else {
    // Jika ID tidak ada di URL, tampilkan pesan error
    echo "<main class='main-content'><div class='card'>ID pegawai tidak ditemukan.</div></main>";
    include '../includes/footer.php';
    exit;
}
?>

<main class="main-content">
    <div class="header-content">
        <h2>Detail Pegawai</h2>
        <a href="pegawai.php" class="btn btn-primary">Kembali ke Daftar</a>
    </div>

    <div class="card detail-card">
        <div class="profile-header">
            <img src="../assets/img/pegawai/<?php echo htmlspecialchars($pegawai['foto']); ?>" alt="Foto <?php echo htmlspecialchars($pegawai['nama']); ?>" class="profile-img-lg">
            <h3><?php echo htmlspecialchars($pegawai['nama']); ?></h3>
            <p><?php echo htmlspecialchars($pegawai['jabatan']); ?></p>
        </div>
        <hr>
        <div class="profile-info">
            <p><strong>NIP:</strong> <?php echo htmlspecialchars($pegawai['nip']); ?></p>
            <p><strong>NIP BPS:</strong> <?php echo htmlspecialchars($pegawai['nip_bps']); ?></p>
            <p><strong>Sebagai:</strong> <?php echo htmlspecialchars($pegawai['sebagai']); ?></p>
            <p><strong>Tanggal Lahir:</strong> <?php echo htmlspecialchars($pegawai['ttl']); ?></p>
            <p><strong>TMT CPNS:</strong> <?php echo htmlspecialchars($pegawai['tmt_cpns']); ?></p>
            <p><strong>Jabatan:</strong> <?php echo htmlspecialchars($pegawai['jabatan']); ?></p>
            <p><strong>Golongan Akhir:</strong> <?php echo htmlspecialchars($pegawai['gol_akhir']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($pegawai['status']); ?></p>
            <p><strong>Pendidikan:</strong> <?php echo htmlspecialchars($pegawai['pendidikan']); ?></p>
            <p><strong>Jenis Kelamin:</strong> <?php echo htmlspecialchars($pegawai['gender']); ?></p>
            <p><strong>Inisial:</strong> <?php echo htmlspecialchars($pegawai['inisial']); ?></p>
            <p><strong>No. Urut:</strong> <?php echo htmlspecialchars($pegawai['no_urut']); ?></p>
            <p><strong>Seksi:</strong> <?php echo htmlspecialchars($pegawai['seksi']); ?></p>
            <p><strong>Kecamatan:</strong> <?php echo htmlspecialchars($pegawai['kecamatan']); ?></p>
            <p><strong>Tempat Lahir:</strong> <?php echo htmlspecialchars($pegawai['alamat']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($pegawai['email']); ?></p>
        </div>
    </div>
</main>

<?php
include '../includes/footer.php';
?>