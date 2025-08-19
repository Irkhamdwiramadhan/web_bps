<?php
// Masukkan file koneksi database dan layout utama
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Ambil data pegawai dari database untuk dropdown
$sql_pegawai = "SELECT id, nama FROM pegawai ORDER BY nama ASC";
$result_pegawai = $koneksi->query($sql_pegawai);
$pegawai_list = [];
if ($result_pegawai->num_rows > 0) {
    while($row = $result_pegawai->fetch_assoc()) {
        $pegawai_list[] = $row;
    }
}

// Daftar status kehadiran untuk radio button
$status_options = [
    'hadir_awal' => 'Hadir Awal',
    'hadir' => 'Hadir',
    'telat_1' => 'Telat 1',
    'telat_2' => 'Telat 2',
    'telat_3' => 'Telat 3',
    'izin' => 'Izin',
    'absen' => 'Absen',
    'dinas_luar' => 'Dinas Luar',
    'sakit' => 'Sakit',
    'cuti' => 'Cuti',
    'tugas' => 'Tugas',
];
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
                <select id="petugas" name="petugas" class="form-control">
                    <option value="">-- Pilih Petugas --</option>
                    <?php foreach ($pegawai_list as $pegawai): ?>
                        <option value="<?= htmlspecialchars($pegawai['nama']) ?>">
                            <?= htmlspecialchars($pegawai['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <label for="komando">Komando:</label>
                <select id="komando" name="komando" class="form-control">
                    <option value="">-- Pilih Komando --</option>
                    <?php foreach ($pegawai_list as $pegawai): ?>
                        <option value="<?= htmlspecialchars($pegawai['nama']) ?>">
                            <?= htmlspecialchars($pegawai['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <label for="pemimpin_doa">Pemimpin Doa:</label>
                <select id="pemimpin_doa" name="pemimpin_doa" class="form-control">
                    <option value="">-- Pilih Pemimpin Doa --</option>
                    <?php foreach ($pegawai_list as $pegawai): ?>
                        <option value="<?= htmlspecialchars($pegawai['nama']) ?>">
                            <?= htmlspecialchars($pegawai['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <label for="pembina_apel">Pembina Apel:</label>
                <select id="pembina_apel" name="pembina_apel" class="form-control">
                    <option value="">-- Pilih Pembina Apel --</option>
                    <?php foreach ($pegawai_list as $pegawai): ?>
                        <option value="<?= htmlspecialchars($pegawai['nama']) ?>">
                            <?= htmlspecialchars($pegawai['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <label for="keterangan">Catatan Umum:</label>
                <textarea id="keterangan" name="keterangan" rows="4"></textarea>

                <div id="form_foto">
                    <label for="foto_bukti">Foto Bukti Apel:</label>
                    <input type="file" id="foto_bukti" name="foto_bukti">
                </div>
                
                <h3>Status Kehadiran Pegawai</h3>
                <div class="data-table-container">
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
                                <td><?= htmlspecialchars($pegawai['nama']); ?></td>
                                <td>
                                    <div class="status-options">
                                        <?php foreach ($status_options as $value => $label): ?>
                                            <input type="radio" 
                                                   id="status-<?= $pegawai['id'] ?>-<?= $value ?>" 
                                                   name="kehadiran[<?= $pegawai['id'] ?>][status]" 
                                                   value="<?= $value ?>"
                                                   class="status-radio"
                                                   required>
                                            <label for="status-<?= $pegawai['id'] ?>-<?= $value ?>" class="status-label">
                                                <?= $label ?>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" name="kehadiran[<?= $pegawai['id'] ?>][catatan]" placeholder="Tambahkan catatan jika perlu">
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <br>
            <button type="submit" class="btn btn-primary">Simpan Data Apel</button>
        </form>
    </div>
</main>

<style>
/* Styling untuk radio button status kehadiran */
.data-table-container {
    overflow-x: auto;
}
.status-options {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}
.status-radio {
    display: none; /* Sembunyikan radio button asli */
}
.status-label {
    display: inline-block;
    padding: 6px 12px;
    background-color: #f0f0f0;
    border: 1px solid #ccc;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    line-height: 1;
    white-space: nowrap;
    transition: background-color 0.2s, color 0.2s;
}
.status-radio:checked + .status-label {
    background-color: #007bff;
    color: #fff;
    border-color: #007bff;
}
.status-label:hover {
    background-color: #e9e9e9;
}
.status-radio:checked + .status-label:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}
</style>

<script>
function toggleFormFields() {
    var kondisiApel = document.getElementById('kondisi_apel').value;
    var formFoto = document.getElementById('form_foto');
    var formTidakAda = document.getElementById('form_tidak_ada');
    var formAdaLupa = document.getElementById('form_ada_lupa');
    
    var tabelKehadiran = document.querySelector('.data-table');
    
    var inputsAdaLupa = formAdaLupa.querySelectorAll('input:not(.status-radio), select, textarea');
    var inputsTidakAda = formTidakAda.querySelectorAll('input, select, textarea');
    
    // Dapatkan semua radio button status kehadiran
    var statusRadioButtons = tabelKehadiran.querySelectorAll('.status-radio');

    // Reset semua required
    inputsAdaLupa.forEach(input => input.required = false);
    inputsTidakAda.forEach(input => input.required = false);
    statusRadioButtons.forEach(input => input.required = false);
    document.getElementById('foto_bukti').required = false;

    // Atur display awal
    formTidakAda.style.display = 'none';
    formAdaLupa.style.display = 'none';
    formFoto.style.display = 'none';
    tabelKehadiran.style.display = 'none';

    if (kondisiApel === 'tidak_ada') {
        // Apel tidak ada
        formTidakAda.style.display = 'block';
        document.getElementById('alasan_tidak_ada').required = true;
    } else {
        // Apel ada atau lupa dokumentasi
        formAdaLupa.style.display = 'block';
        tabelKehadiran.style.display = 'table';
        
        // Set input utama jadi required
        document.getElementById('petugas').required = true;
        document.getElementById('komando').required = true;
        document.getElementById('pemimpin_doa').required = true;
        document.getElementById('pembina_apel').required = true;

        if (kondisiApel === 'ada') {
            formFoto.style.display = 'block';
            document.getElementById('foto_bukti').required = true;
            
            // Semua radio status kehadiran wajib diisi
            statusRadioButtons.forEach(input => {
                input.required = true;
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', toggleFormFields);
</script>

<?php include '../includes/footer.php'; ?>
