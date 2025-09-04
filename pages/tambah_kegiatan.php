<?php
session_start();
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Ambil daftar mitra
$sql_mitra = "SELECT id, nama_lengkap FROM mitra ORDER BY nama_lengkap ASC";
$result_mitra = $koneksi->query($sql_mitra);
$mitra_list = [];
if ($result_mitra && $result_mitra->num_rows > 0) {
    while ($row = $result_mitra->fetch_assoc()) {
        $mitra_list[] = $row;
    }
}

// Ambil daftar survei
$sql_surveys = "SELECT id, nama_survei, singkatan_survei FROM surveys ORDER BY nama_survei ASC";
$result_surveys = $koneksi->query($sql_surveys);
$surveys_list = [];
if ($result_surveys && $result_surveys->num_rows > 0) {
    while ($row = $result_surveys->fetch_assoc()) {
        $surveys_list[] = $row;
    }
}
?>

<style>
/* --- DESAIN TAMPILAN MODERN --- */
body {
    background-color: #e2e8f0; /* Latar belakang abu-abu muda */
}
.main-content {
    padding: 2rem;
}
.card {
    background-color: #ffffff;
    padding: 2.5rem;
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}
.form-group {
    margin-bottom: 1.5rem;
}
label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #4a5568;
}
.form-input, .form-select, .select-search-input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #cbd5e1;
    border-radius: 0.5rem;
    background-color: #f7fafc;
    transition: all 0.2s ease-in-out;
}
.form-input:focus, .form-select:focus, .select-search-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
}
.select-search-container {
    position: relative;
}
.select-search-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 20;
    background-color: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    max-height: 200px;
    overflow-y: auto;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    display: none;
}
.select-search-dropdown-item {
    padding: 0.75rem 1rem;
    cursor: pointer;
    transition: background-color 0.2s;
}
.select-search-dropdown-item:hover {
    background-color: #eef2ff;
}
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}
.alert {
    padding: 1rem;
    margin-bottom: 1.5rem;
    border-radius: 0.5rem;
    display: flex;
    gap: 1rem;
}
.alert-danger {
    background-color: #fee2e2;
    color: #991b1b;
}
.btn-primary, .btn-secondary {
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s;
    border: none;
}
.btn-primary {
    background-color: #3b82f6;
    color: #ffffff;
}
.btn-primary:hover {
    background-color: #2563eb;
}
.btn-secondary {
    background-color: #e5e7eb;
    color: #4b5563;
}
.btn-secondary:hover {
    background-color: #d1d5db;
}
.btn-group {
    margin-top: 1.5rem;
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
}
.btn-add-mitra {
    background-color: #28a745;
    color: #fff;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 600;
    transition: background-color 0.2s;
    cursor: pointer;
    border: none;
}
.btn-add-mitra:hover {
    background-color: #218838;
}
.mitra-input-group {
    display: flex;
    gap: 10px;
    align-items: center;
}
.mitra-input-group .form-input {
    flex-grow: 1;
}
</style>

<div class="main-content">
    <div class="card">
        <h3>Tambah Kegiatan Baru</h3>
        <p class="text-sm text-gray-500">Isi formulir di bawah ini untuk menambahkan kegiatan baru.</p>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'error') : ?>
            <div class="alert alert-danger">
                <strong>Error!</strong> <?= htmlspecialchars(str_replace('_',' ', $_GET['message'])); ?>
            </div>
        <?php endif; ?>

        <form action="../proses/proses_tambah_kegiatan.php" method="POST" id="kegiatan-form">
            <div class="form-group select-search-container">
                <label for="survey-search-input">Jenis Survei</label>
                <input type="text" id="survey-search-input" class="select-search-input" placeholder="Cari Jenis Survei..." autocomplete="off" required>
                <input type="hidden" name="survei_id" id="survei_id">
                <div id="survey-dropdown" class="select-search-dropdown">
                    <?php foreach ($surveys_list as $survey) : ?>
                        <div class="select-search-dropdown-item" data-id="<?= htmlspecialchars($survey['id']) ?>" data-name="<?= htmlspecialchars($survey['nama_survei']) ?>" data-abbr="<?= htmlspecialchars($survey['singkatan_survei']) ?>">
                            <?= htmlspecialchars($survey['nama_survei']) ?> (<?= htmlspecialchars($survey['singkatan_survei']) ?>)
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="periode_nilai_main">Periode Survei</label>
                <select name="periode_nilai_main" id="periode_nilai_main" class="form-select" required>
                    <option value="">-- Pilih Periode --</option>
                    <option value="Tahunan">Tahunan</option>
                    <option value="4 Bulanan">Subround</option>
                    <option value="Triwulan">Triwulan</option>
                    <option value="Bulanan">Bulanan</option>
                </select>
            </div>

            <div id="dynamic_periode_input" class="mb-6" style="display:none;"></div>
            
            <div class="form-group">
                <label class="form-label">Nama Mitra</label>
                <div id="mitra-container">
                    <div class="mitra-input-group mb-2">
                        <div class="select-search-container flex-grow-1">
                            <input type="text" id="mitra-search-input-0" class="select-search-input" placeholder="Cari Nama Mitra..." autocomplete="off" required>
                            <input type="hidden" name="mitra_id[]" id="mitra_id-0">
                            <div id="mitra-dropdown-0" class="select-search-dropdown">
                                <?php foreach ($mitra_list as $mitra) : ?>
                                    <div class="select-search-dropdown-item" data-id="<?= htmlspecialchars($mitra['id']) ?>" data-name="<?= htmlspecialchars($mitra['nama_lengkap']) ?>">
                                        <?= htmlspecialchars($mitra['nama_lengkap']) ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-add-mitra mt-2" onclick="addMitraInput()">+ Tambah Mitra</button>
            </div>

            <input type="hidden" name="periode_jenis" id="periode_jenis">
            <input type="hidden" name="periode_nilai" id="periode_nilai">

            <div class="btn-group">
                <button type="button" onclick="window.location.href='kegiatan.php'" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary">Simpan Kegiatan</button>
            </div>
        </form>
    </div>
</div>

<script>
let mitraCounter = 1;
let selectedMitraIds = new Set(); // Set untuk melacak ID mitra yang sudah dipilih

// Helper untuk setup select-search
function setupSelectSearch(input, dropdown, hidden, isMitra) {
    const items = dropdown.querySelectorAll('.select-search-dropdown-item');

    input.addEventListener('focus', () => { dropdown.style.display = 'block'; });
    input.addEventListener('blur', () => { setTimeout(()=> dropdown.style.display='none', 150); });

    input.addEventListener('input', function() {
        const q = this.value.toLowerCase();
        items.forEach(it => {
            const txt = (it.getAttribute('data-name') || '').toLowerCase();
            const ab = (it.getAttribute('data-abbr') || '').toLowerCase();
            if (txt.includes(q) || ab.includes(q)) {
                it.style.display = 'block';
            } else { it.style.display = 'none'; }
        });
    });

    items.forEach(item => {
        item.addEventListener('mousedown', function(e) {
            e.preventDefault();
            const selectedId = item.getAttribute('data-id');
            
            // Validasi duplikasi hanya untuk mitra
            if (isMitra && selectedMitraIds.has(selectedId)) {
                alert('Nama mitra ini sudah dipilih. Silakan pilih nama mitra yang berbeda.');
                return;
            }

            input.value = item.getAttribute('data-name') + (item.getAttribute('data-abbr') ? ' ('+item.getAttribute('data-abbr')+')' : '');
            hidden.value = selectedId;
            dropdown.style.display = 'none';

            // Tambahkan ID ke Set jika ini adalah mitra
            if (isMitra) {
                selectedMitraIds.add(selectedId);
            }
        });
    });
}

// Fungsi untuk menambahkan input mitra baru
function addMitraInput() {
    const container = document.getElementById('mitra-container');
    const newMitraGroup = document.createElement('div');
    newMitraGroup.className = 'mitra-input-group mb-2';
    newMitraGroup.innerHTML = `
        <div class="select-search-container flex-grow-1">
            <input type="text" id="mitra-search-input-${mitraCounter}" class="select-search-input" placeholder="Cari Nama Mitra..." autocomplete="off" required>
            <input type="hidden" name="mitra_id[]" id="mitra_id-${mitraCounter}">
            <div id="mitra-dropdown-${mitraCounter}" class="select-search-dropdown">
                <?php foreach ($mitra_list as $mitra) : ?>
                    <div class="select-search-dropdown-item" data-id="<?= htmlspecialchars($mitra['id']) ?>" data-name="<?= htmlspecialchars($mitra['nama_lengkap']) ?>">
                        <?= htmlspecialchars($mitra['nama_lengkap']) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <button type="button" class="btn btn-danger btn-sm" onclick="removeMitraInput(this)">-</button>
    `;
    container.appendChild(newMitraGroup);
    
    // Setup select-search untuk input baru
    const input = document.getElementById(`mitra-search-input-${mitraCounter}`);
    const dropdown = document.getElementById(`mitra-dropdown-${mitraCounter}`);
    const hidden = document.getElementById(`mitra_id-${mitraCounter}`);
    setupSelectSearch(input, dropdown, hidden, true);
    
    mitraCounter++;
}

// Fungsi untuk menghapus input mitra
function removeMitraInput(button) {
    const container = button.parentNode;
    const hiddenInput = container.querySelector('input[type="hidden"]');
    const mitraId = hiddenInput.value;
    
    // Hapus ID mitra dari Set jika ada
    if (mitraId) {
        selectedMitraIds.delete(mitraId);
    }
    container.remove();
}

document.addEventListener('DOMContentLoaded', function() {
    // Setup select-search untuk input survei
    setupSelectSearch(document.getElementById('survey-search-input'), document.getElementById('survey-dropdown'), document.getElementById('survei_id'), false);

    // Setup select-search untuk input mitra pertama
    const mitraDropdownHtml = document.getElementById('mitra-dropdown-0').innerHTML;
    document.getElementById('mitra-container').querySelector('#mitra-dropdown-0').innerHTML = mitraDropdownHtml;
    setupSelectSearch(document.getElementById('mitra-search-input-0'), document.getElementById('mitra-dropdown-0'), document.getElementById('mitra_id-0'), true);


    // Dinamis periode
    const periodeSelect = document.getElementById('periode_nilai_main');
    const dynamicInputDiv = document.getElementById('dynamic_periode_input');

    function renderDynamic(selected) {
        dynamicInputDiv.innerHTML = '';
        dynamicInputDiv.style.display = selected ? 'block' : 'none';
        if (!selected) return;

        let dynamicHtml = '';
        if (selected === 'Tahunan') {
            dynamicHtml = `<div class="form-group"><label for="tahun">Tahun</label><input type="number" id="tahun" name="tahun" class="form-input" placeholder="2025" required></div>`;
        } else if (selected === '4 Bulanan') {
            dynamicHtml = `<div class="form-group"><label for="four_month">4-Bulanan (pilih group)</label><select id="four_month" name="four_month" class="form-select" required><option value="">-- Pilih --</option><option value="1">Subround I </option><option value="2">Subround II</option><option value="3">Subround III</option></select></div><div class="form-group"><label for="tahun_4b">Tahun</label><input type="number" id="tahun_4b" name="tahun_4b" class="form-input" placeholder="2025" required></div>`;
        } else if (selected === 'Triwulan') {
            dynamicHtml = `<div class="form-group"><label for="triwulan">Triwulan</label><select id="triwulan" name="triwulan" class="form-select" required><option value="">-- Pilih Triwulan --</option><option value="1">Triwulan I</option><option value="2">Triwulan II</option><option value="3">Triwulan III</option><option value="4">Triwulan IV</option></select></div><div class="form-group"><label for="tahun_trw">Tahun</label><input type="number" id="tahun_trw" name="tahun_trw" class="form-input" placeholder="2025" required></div>`;
        } else if (selected === 'Bulanan') {
            dynamicHtml = `<div class="form-group"><label for="bulan_bulanan">Bulan</label><select id="bulan_bulanan" name="bulan_bulanan" class="form-select" required><option value="">-- Pilih Bulan --</option><option value="Januari">Januari</option><option value="Februari">Februari</option><option value="Maret">Maret</option><option value="April">April</option><option value="Mei">Mei</option><option value="Juni">Juni</option><option value="Juli">Juli</option><option value="Agustus">Agustus</option><option value="September">September</option><option value="Oktober">Oktober</option><option value="November">November</option><option value="Desember">Desember</option></select></div><div class="form-group"><label for="tahun_bln">Tahun</label><input type="number" id="tahun_bln" name="tahun_bln" class="form-input" placeholder="2025" required></div>`;
        }
        dynamicInputDiv.innerHTML = dynamicHtml;
    }

    periodeSelect.addEventListener('change', function() {
        renderDynamic(this.value);
    });

    // Saat submit, gabungkan periode_jenis + periode_nilai ke hidden fields
    document.getElementById('kegiatan-form').addEventListener('submit', function(e) {
        // Cek semua input utama
        const surveiId = document.getElementById('survei_id').value;
        const periodeMain = document.getElementById('periode_nilai_main').value;
        
        // Cek input mitra
        const mitraInputs = document.querySelectorAll('input[name="mitra_id[]"]');
        let hasMitra = false;
        mitraInputs.forEach(input => {
            if (input.value) {
                hasMitra = true;
            }
        });

        if (!hasMitra || !surveiId || !periodeMain) {
            e.preventDefault();
            alert('Harap lengkapi semua data wajib (Nama Mitra, Jenis Survei, dan Periode).');
            return;
        }

        // build periode_jenis & periode_nilai
        document.getElementById('periode_jenis').value = periodeMain;
        let nilai = '';
        switch (periodeMain) {
            case 'Tahunan':
                nilai = document.getElementById('tahun') ? document.getElementById('tahun').value : '';
                break;
            case '4 Bulanan':
                const four = document.getElementById('four_month') ? document.getElementById('four_month').value : '';
                const tahun4 = document.getElementById('tahun_4b') ? document.getElementById('tahun_4b').value : '';
                if (four && tahun4) nilai = `4B-${four} / ${tahun4}`;
                break;
            case 'Triwulan':
                const tr = document.getElementById('triwulan') ? document.getElementById('triwulan').value : '';
                const tahun_tr = document.getElementById('tahun_trw') ? document.getElementById('tahun_trw').value : '';
                if (tr && tahun_tr) nilai = `Q${tr} / ${tahun_tr}`;
                break;
            case 'Bulanan':
                const bl = document.getElementById('bulan_bulanan') ? document.getElementById('bulan_bulanan').value : '';
                const tahun_b = document.getElementById('tahun_bln') ? document.getElementById('tahun_bln').value : '';
                if (bl && tahun_b) nilai = `${bl} / ${tahun_b}`;
                break;
        }

        if (!nilai) {
            e.preventDefault();
            alert('Harap lengkapi detail Periode yang dipilih.');
            return;
        }
        document.getElementById('periode_nilai').value = nilai;
    });
});
</script>

<?php
if ($result_mitra instanceof mysqli_result) { $result_mitra->free(); }
if ($result_surveys instanceof mysqli_result) { $result_surveys->free(); }

include '../includes/footer.php';
?>