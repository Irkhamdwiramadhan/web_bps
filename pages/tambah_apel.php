<?php
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

$sql_pegawai = "SELECT id, nama FROM pegawai";
$result_pegawai = $koneksi->query($sql_pegawai);
$pegawai_list = [];
if ($result_pegawai->num_rows > 0) {
    while($row = $result_pegawai->fetch_assoc()) {
        $pegawai_list[] = $row;
    }
}
?>

<main class="main-content">
    <div class="header-content">
        <h2>Tambah Data Apel</h2>
    </div>
    <div class="card">
        <form action="../proses/proses_apel.php" method="POST" enctype="multipart/form-data">
            <label for="tanggal">Tanggal Apel:</label>
            <input type="date" id="tanggal" name="tanggal" required>

            <label for="kondisi_apel">Kondisi Apel:</label>
            <select id="kondisi_apel" name="kondisi_apel" class="form-control" onchange="toggleFormFields()">
                <option value="ada">Apel Dilaksanakan</option>
                <option value="tidak_ada">Apel Tidak Dilaksanakan</option>
                <option value="lupa_didokumentasikan" selected>Apel Lupa Didokumentasikan</option>
            </select>
            
            <div id="form_tidak_ada" style="display: none;">
                <label for="alasan_tidak_ada">Alasan Apel Tidak Dilaksanakan:</label>
                <textarea id="alasan_tidak_ada" name="alasan_tidak_ada" rows="4"></textarea>
            </div>

            <div id="form_ada_lupa">
                <label for="petugas">Petugas Apel:</label>
                <input type="text" id="petugas" name="petugas">
                
                <label for="komando">Komando:</label>
                <input type="text" id="komando" name="komando">
                
                <label for="pemimpin_doa">Pemimpin Doa:</label>
                <input type="text" id="pemimpin_doa" name="pemimpin_doa">
                
                <label for="pembina_apel">Pembina Apel:</label>
                <input type="text" id="pembina_apel" name="pembina_apel">
                
                <label for="keterangan">Catatan Umum:</label>
                <textarea id="keterangan" name="keterangan" rows="4"></textarea>

                <div id="form_foto">
                    <label for="foto_bukti">Foto Bukti Apel:</label>
                    <input type="file" id="foto_bukti" name="foto_bukti">
                </div>
                
                <h3>Status Kehadiran Pegawai</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama Pegawai</th>
                            <th>Status Kehadiran</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pegawai_list as $pegawai): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($pegawai['nama']); ?></td>
                            <td>
                                <select name="kehadiran[<?php echo $pegawai['id']; ?>][status]">
                                    <option value="hadir_awal">Hadir Awal</option>
                                    <option value="hadir">Hadir</option>
                                    <option value="telat_1">Telat 1</option>
                                    <option value="telat_2">Telat 2</option>
                                    <option value="telat_3">Telat 3</option>
                                    <option value="izin">Izin</option>
                                    <option value="absen">Absen</option>
                                    <option value="dinas_luar">Dinas Luar</option>
                                    <option value="sakit">Sakit</option>
                                    <option value="cuti">Cuti</option>
                                    <option value="tugas">Tugas</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="kehadiran[<?php echo $pegawai['id']; ?>][catatan]" placeholder="Tambahkan catatan jika perlu">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <br>
            <button type="submit" class="btn btn-primary">Simpan Data Apel</button>
        </form>
    </div>
</main>

<script>
    function toggleFormFields() {
        var kondisiApel = document.getElementById('kondisi_apel').value;
        var formTidakAda = document.getElementById('form_tidak_ada');
        var formAdaLupa = document.getElementById('form_ada_lupa');
        var formFoto = document.getElementById('form_foto');
        var tabelKehadiran = document.querySelector('.data-table');
        
        // Dapatkan semua input yang ada di dalam form_ada_lupa
        var inputsAdaLupa = formAdaLupa.querySelectorAll('input, select, textarea');
        var inputsTidakAda = formTidakAda.querySelectorAll('input, select, textarea');
        
        // Dapatkan semua input di dalam tabel kehadiran
        var inputsKehadiran = tabelKehadiran.querySelectorAll('select, input');

        // Atur ulang semua properti required
        inputsAdaLupa.forEach(input => input.required = false);
        inputsTidakAda.forEach(input => input.required = false);
        inputsKehadiran.forEach(input => input.required = false);
        document.getElementById('foto_bukti').required = false; // Pastikan input foto juga direset

        // Atur display untuk semua form
        formTidakAda.style.display = 'none';
        formAdaLupa.style.display = 'none';
        formFoto.style.display = 'none';
        tabelKehadiran.style.display = 'none';

        if (kondisiApel === 'tidak_ada') {
            formTidakAda.style.display = 'block';
            document.getElementById('alasan_tidak_ada').required = true;
        } else { // kondisi 'ada' atau 'lupa_didokumentasikan'
            formAdaLupa.style.display = 'block';
            tabelKehadiran.style.display = 'table';
            
            // Atur input yang relevan menjadi required untuk kedua kondisi
            document.getElementById('petugas').required = true;
            document.getElementById('komando').required = true;
            document.getElementById('pemimpin_doa').required = true;
            document.getElementById('pembina_apel').required = true;

            if (kondisiApel === 'ada') {
                formFoto.style.display = 'block';
                document.getElementById('foto_bukti').required = true;
                inputsKehadiran.forEach(input => {
                    input.required = true;
                });
            }
        }
    }

    document.addEventListener('DOMContentLoaded', toggleFormFields);
</script>
<?php include '../includes/footer.php'; ?>