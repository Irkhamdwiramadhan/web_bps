<?php
// Masukkan file koneksi database dan layout utama
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Ambil data calon pegawai untuk penilaian
$query_calon = "
    SELECT 
        ct.id,
        p.nama,
        p.nip,
        ct.triwulan,
        ct.tahun
    FROM calon_triwulan ct
    JOIN pegawai p ON ct.id_pegawai = p.id
    ORDER BY p.nama ASC";
$result_calon = mysqli_query($koneksi, $query_calon);

// Ambil data pegawai untuk dropdown penilai
$query_penilai = "SELECT id, nama FROM pegawai ORDER BY nama ASC";
$result_penilai = mysqli_query($koneksi, $query_penilai);

// Daftar warna box yang akan digunakan secara bergiliran
$box_colors = ['primary', 'info', 'success', 'warning', 'danger'];
?>

<div class="main-content">
    <section class="content-header">
        <h1>
            <i class="fas fa-edit"></i> Formulir Penilaian Massal
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Input Penilaian Pegawai</h3>
                    </div>
                    <div class="box-body">
                        <form id="form_penilaian" method="POST" action="../proses/proses_penilaian.php">
                            <div class="form-group">
                                <label for="penilai_id">Pilih Penilai:</label>
                                <select name="penilai_id" id="penilai_id" class="form-control" required>
                                    <option value="">-- Pilih Penilai --</option>
                                    <?php while ($row_penilai = mysqli_fetch_assoc($result_penilai)): ?>
                                        <option value="<?= $row_penilai['id'] ?>">
                                            <?= htmlspecialchars($row_penilai['nama']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <hr style="border-top: 2px solid #eee;">

                            <?php
                            if (mysqli_num_rows($result_calon) > 0) {
                                $counter = 0;
                                while ($calon = mysqli_fetch_assoc($result_calon)) {
                                    $color_index = $counter % count($box_colors);
                                    $box_class = "box-" . $box_colors[$color_index];
                                    ?>
                                    <div class="box <?= $box_class ?> box-solid">
                                        <div class="box-header">
                                            <h3 class="box-title">
                                                Penilaian Pegawai: <strong><?= htmlspecialchars($calon['nama']) ?></strong> 
                                                <small class="text-muted">(NIP: <?= htmlspecialchars($calon['nip']) ?> | T.<?= htmlspecialchars($calon['triwulan']) ?> <?= htmlspecialchars($calon['tahun']) ?>)</small>
                                            </h3>
                                        </div>
                                        <div class="box-body">
                                            <!-- Hidden input untuk ID calon pegawai -->
                                            <input type="hidden" name="calon_id[]" value="<?= $calon['id'] ?>">
                                            
                                            <!-- Kriteria Penilaian sebagai Range Slider -->
                                            <?php
                                            $kriteria = [
                                                'Berorientasi Pelayanan',
                                                'Akuntabel',
                                                'Kompeten',
                                                'Harmonis',
                                                'Loyal',
                                                'Adaptif',
                                                'Kolaboratif'
                                            ];
                                            foreach ($kriteria as $index => $nama_kriteria) {
                                                $input_name = "kriteria[{$calon['id']}][]";
                                                ?>
                                                <div class="form-group" style="margin-bottom: 20px;">
                                                    <label for="range-<?= $calon['id'] ?>-<?= $index ?>" style="font-weight: 600;">
                                                        <?= htmlspecialchars($nama_kriteria) ?>: 
                                                        <span id="label-<?= $calon['id'] ?>-<?= $index ?>" class="badge bg-blue" style="min-width: 30px; text-align: center;">0</span>
                                                    </label>
                                                    <input type="range" 
                                                           name="<?= $input_name ?>" 
                                                           id="range-<?= $calon['id'] ?>-<?= $index ?>" 
                                                           class="form-control range-slider" 
                                                           min="0" max="100" value="0">
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php
                                $counter++;
                                }
                            } else {
                                echo '<p class="text-center">Belum ada calon pegawai yang terdaftar untuk dinilai.</p>';
                            }
                            ?>

                            <div class="box-footer text-right" style="padding: 20px;">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-save"></i> Simpan Penilaian
                                </button>
                                <a href="calon_berprestasi.php" class="btn btn-default btn-lg">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Script untuk mengupdate nilai slider dan CSS tambahan untuk memperbesar slider -->
<style>
/* CSS untuk memperbesar slider */
.range-slider {
    -webkit-appearance: none;
    width: 100%;
    height: 15px; /* Tinggi slider track */
    background: #d3d3d3;
    outline: none;
    opacity: 0.7;
    -webkit-transition: .2s;
    transition: opacity .2s;
    border-radius: 8px;
}

.range-slider:hover {
    opacity: 1;
}

/* Chrome, Safari, Edge, Opera */
.range-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 25px; /* Ukuran thumb */
    height: 25px; /* Ukuran thumb */
    background: #007bff;
    cursor: pointer;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 5px rgba(0,0,0,0.2);
}

/* Firefox */
.range-slider::-moz-range-thumb {
    width: 25px;
    height: 25px;
    background: #007bff;
    cursor: pointer;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 5px rgba(0,0,0,0.2);
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sliders = document.querySelectorAll('.range-slider');
        sliders.forEach(slider => {
            const [_, id, index] = slider.id.split('-');
            const label = document.getElementById(`label-${id}-${index}`);
            
            if (label) {
                label.textContent = slider.value;
            }

            slider.addEventListener('input', function() {
                if (label) {
                    label.textContent = this.value;
                }
            });
        });
    });
</script>

<?php 
include '../includes/footer.php'; 
mysqli_close($koneksi);
?>
