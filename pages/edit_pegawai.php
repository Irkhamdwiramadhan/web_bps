<?php
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM pegawai WHERE id = $id";
    $result = $koneksi->query($sql);
    $pegawai = $result->fetch_assoc();
} else {
    echo "ID pegawai tidak ditemukan.";
    exit;
}
?>

<main class="main-content">
    <div class="header-content">
        <h2>Edit Data Pegawai</h2>
    </div>
    <div class="card">
        <form action="../proses/proses_edit.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $pegawai['id']; ?>">
            <input type="hidden" name="foto_lama" value="<?php echo $pegawai['foto']; ?>">

            <label for="nama">Nama Lengkap:</label>
            <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($pegawai['nama']); ?>" required>
            
            <label for="ttl">Tanggal Lahir:</label>
            <input type="date" id="ttl" name="ttl" value="<?php echo htmlspecialchars($pegawai['ttl']); ?>">

            <label for="kecamatan">Kecamatan:</label>
            <input type="text" id="kecamatan" name="kecamatan" value="<?php echo htmlspecialchars($pegawai['kecamatan']); ?>">
            
            <label for="inisial">Inisial:</label>
            <input type="text" id="inisial" name="inisial" value="<?php echo htmlspecialchars($pegawai['inisial']); ?>">
            
            <label for="no_urut">No Urut:</label>
            <input type="number" id="no_urut" name="no_urut" value="<?php echo htmlspecialchars($pegawai['no_urut']); ?>">
            
            <label for="nip">NIP:</label>
            <input type="text" id="nip" name="nip" value="<?php echo htmlspecialchars($pegawai['nip']); ?>">
            
            <label for="nip_bps">NIP BPS:</label>
            <input type="text" id="nip_bps" name="nip_bps" value="<?php echo htmlspecialchars($pegawai['nip_bps']); ?>">
            
            <label for="gol_akhir">Golongan Akhir:</label>
            <input type="text" id="gol_akhir" name="gol_akhir" value="<?php echo htmlspecialchars($pegawai['gol_akhir']); ?>">
            
            <label for="status">Status:</label>
            <input type="text" id="status" name="status" value="<?php echo htmlspecialchars($pegawai['status']); ?>">
            
            <label for="tmt_cpns">TMT CPNS:</label>
            <input type="date" id="tmt_cpns" name="tmt_cpns" value="<?php echo htmlspecialchars($pegawai['tmt_cpns']); ?>">
            <label for="pendidikan">Pendidikan Terakhir:</label>
            <input type="text" id="pendidikan" name="pendidikan" value="<?php echo htmlspecialchars($pegawai['pendidikan']); ?>">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($pegawai['email']); ?>">

            <label for="gender">Jenis Kelamin:</label>
            <select id="gender" name="gender">
                <option value="Laki-laki" <?php if ($pegawai['gender'] == 'Laki-laki') echo 'selected'; ?>>Laki-laki</option>
                <option value="Perempuan" <?php if ($pegawai['gender'] == 'Perempuan') echo 'selected'; ?>>Perempuan</option>
            </select>

            <label for="jabatan">Jabatan:</label>
            <input type="text" id="jabatan" name="jabatan" value="<?php echo htmlspecialchars($pegawai['jabatan']); ?>">
            
            <label for="sebagai">Sebagai:</label>
            <select id="sebagai" name="sebagai">
                <option value="Anggota" <?php if ($pegawai['sebagai'] == 'Anggota') echo 'selected'; ?>>Anggota</option>
                <option value="Pembina" <?php if ($pegawai['sebagai'] == 'Pembina') echo 'selected'; ?>>Pembina</option>
                <option value="Staff" <?php if ($pegawai['sebagai'] == 'Staff') echo 'selected'; ?>>Staff</option>
            </select>

            <label for="seksi">Seksi:</label>
            <input type="text" id="seksi" name="seksi" value="<?php echo htmlspecialchars($pegawai['seksi']); ?>">
            
            <label for="alamat">Tempat Lahir:</label>
            <input type="text" id="alamat" name="alamat" value="<?php echo htmlspecialchars($pegawai['alamat']); ?>">

            <label for="foto">Foto Pegawai (kosongkan jika tidak diubah):</label>
            <input type="file" id="foto" name="foto">

            <button type="submit" class="btn btn-primary">Update Data</button>
        </form>
    </div>
</main>

<?php include '../includes/footer.php'; ?>