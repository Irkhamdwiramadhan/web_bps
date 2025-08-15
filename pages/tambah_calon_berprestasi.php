<?php
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Ambil data pegawai dari database untuk dropdown
$query_pegawai = "SELECT id, nama, nip FROM pegawai ORDER BY nama ASC";
$result_pegawai = mysqli_query($koneksi, $query_pegawai);

$current_year = date('Y');
?>

<div class="main-content">
    <section class="content-header">
        <h1>
            <i class="fas fa-trophy"></i> Tambah Calon Pegawai Berprestasi
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Formulir Tambah Calon</h3>
                    </div>
                    <div class="box-body">
                        <form id="form_tambah_calon" method="POST" action="../proses/proses_tambah_calon.php">
                            <div class="form-group">
                                <label for="pegawai_id">Pilih Pegawai:</label>
                                <select name="pegawai_id" id="pegawai_id" class="form-control" required>
                                    <option value="">-- Pilih Pegawai --</option>
                                    <?php while ($row = mysqli_fetch_assoc($result_pegawai)): ?>
                                        <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nama']) ?> (<?= htmlspecialchars($row['nip']) ?>)</option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="tahun">Tahun:</label>
                                <select name="tahun" id="tahun" class="form-control" required>
                                    <?php for ($y = 2020; $y <= 2030; $y++): ?>
                                        <option value="<?= $y ?>"><?= $y ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="triwulan">Triwulan:</label>
                                <select name="triwulan" id="triwulan" class="form-control" required>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                </select>
                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-success">Simpan</button>
                                <a href="calon_berprestasi.php" class="btn btn-default">Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php 
include '../includes/footer.php'; 
mysqli_close($koneksi);
?>
