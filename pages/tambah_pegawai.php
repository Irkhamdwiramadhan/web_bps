<?php
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main class="main-content">
    <div class="header-content">
        <h2>Tambah Pegawai Baru</h2>
    </div>
    <div class="card">
        <form action="../proses/proses_tambah.php" method="POST" enctype="multipart/form-data">
            <label for="nama">Nama Lengkap:</label>
            <input type="text" id="nama" name="nama" required>
            
            <label for="ttl">Tanggal Lahir:</label>
            <input type="date" id="ttl" name="ttl">

            <label for="kecamatan">Kecamatan:</label>
            <input type="text" id="kecamatan" name="kecamatan">

            <label for="inisial">Inisial:</label>
            <input type="text" id="inisial" name="inisial">

            <label for="no_urut">No Urut:</label>
            <input type="number" id="no_urut" name="no_urut">
            
            <label for="nip">NIP:</label>
            <input type="text" id="nip" name="nip">

            <label for="nip_bps">NIP BPS:</label>
            <input type="text" id="nip_bps" name="nip_bps">
            
            <label for="gol_akhir">Golongan Akhir:</label>
            <input type="text" id="gol_akhir" name="gol_akhir">
            
            <label for="status">Status:</label>
            <input type="text" id="status" name="status">
            
            <label for="tmt_cpns">TMT CPNS:</label>
            <input type="date" id="tmt_cpns" name="tmt_cpns">

            <label for="pendidikan">Pendidikan Terakhir:</label>
            <input type="text" id="pendidikan" name="pendidikan">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email">
            
            <label for="gender">Jenis Kelamin:</label>
            <select id="gender" name="gender">
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>

            <label for="jabatan">Jabatan:</label>
            <input type="text" id="jabatan" name="jabatan">

            <label for="alamat">Tempat Lahir:</label>
            <input type="text" id="alamat" name="alamat">

            <label for="sebagai">Sebagai:</label>
            <select id="sebagai" name="sebagai">
                <option value="Anggota">Anggota</option>
                <option value="Pembina">Pembina</option>
                <option value="Staff">Staff</option>
            </select>

            <label for="seksi">Seksi:</label>
            <input type="text" id="seksi" name="seksi">

            <label for="foto">Foto Pegawai:</label>
            <input type="file" id="foto" name="foto">

            <button type="submit" class="btn btn-primary">Simpan Data</button>
        </form>
    </div>
</main>

<?php include '../includes/footer.php'; ?>