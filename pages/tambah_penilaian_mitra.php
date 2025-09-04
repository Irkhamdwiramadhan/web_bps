<?php
// Mulai sesi dan lakukan pengecekan peran pengguna di awal
session_start();

// Perubahan: Lakukan pengecekan peran dan pengalihan di sini, sebelum meng-include file lain
// Ini untuk mencegah error "Headers already sent"


// Setelah pengecekan, baru include file-file lain yang berisi HTML
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Inisialisasi variabel untuk penilai otomatis
$penilai_nama_otomatis = $_SESSION['user_nama'] ?? null;
$penilai_id_otomatis = $_SESSION['user_id'] ?? null;

// Ambil daftar mitra dari database
$sql_mitra_surveys = "SELECT ms.id, m.nama_lengkap AS nama_mitra, s.nama_survei, ms.survey_ke_berapa, s.singkatan_survei
                     FROM mitra_surveys ms
                     INNER JOIN mitra m ON ms.mitra_id = m.id
                     INNER JOIN surveys s ON ms.survei_id = s.id
                     ORDER BY m.nama_lengkap ASC";
$result_mitra_surveys = $koneksi->query($sql_mitra_surveys);

$mitra_surveys_list = [];
if ($result_mitra_surveys && $result_mitra_surveys->num_rows > 0) {
    while ($row = $result_mitra_surveys->fetch_assoc()) {
        $mitra_surveys_list[] = $row;
    }
}

// Ambil data riwayat penilaian dari database
// PERUBAHAN: Pertahankan kolom 'beban_kerja' dan tambahkan 'keterangan'
$sql_penilaian_history = "SELECT 
                            p.tanggal_penilaian,
                            p.beban_kerja,
                            p.kualitas,
                            p.volume_pemasukan,
                            p.perilaku,
                            p.keterangan,
                            m.nama_lengkap AS nama_mitra,
                            s.nama_survei,
                            peg.nama AS nama_penilai,
                            ms.id AS mitra_survey_id
                          FROM mitra_penilaian_kinerja p
                          INNER JOIN mitra_surveys ms ON p.mitra_survey_id = ms.id
                          INNER JOIN mitra m ON ms.mitra_id = m.id
                          INNER JOIN surveys s ON ms.survei_id = s.id
                          INNER JOIN pegawai peg ON p.penilai_id = peg.id
                          ORDER BY p.tanggal_penilaian DESC";
$result_penilaian_history = $koneksi->query($sql_penilaian_history);

$penilaian_history_list = [];
if ($result_penilaian_history && $result_penilaian_history->num_rows > 0) {
    while ($row = $result_penilaian_history->fetch_assoc()) {
        $penilaian_history_list[] = $row;
    }
}
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    
    body { font-family: 'Poppins', sans-serif; background: #eef2f5; }
    .content-wrapper { padding: 1rem; transition: margin-left 0.3s ease; }
    @media (min-width: 640px) { .content-wrapper { margin-left: 16rem; padding-top: 2rem; } }
    .card { background-color: #ffffff; border-radius: 1rem; padding: 2rem; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); }
    .form-label { font-weight: 500; color: #4b5563; }
    .form-input, .form-select, .form-textarea {
        display: block;
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 1rem;
        margin-top: 0.5rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-input:focus, .form-select:focus, .form-textarea:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        outline: none;
    }
    .form-input[readonly] {
        background-color: #f3f4f6;
        cursor: not-allowed;
    }
    .btn-primary { background-color: #2563eb; color: #fff; padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-weight: 600; transition: background-color 0.2s; }
    .btn-primary:hover { background-color: #1d4ed8; }
    .btn-secondary { background-color: #6b7280; color: #fff; padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-weight: 600; transition: background-color 0.2s; }
    .btn-secondary:hover { background-color: #4b5563; }
    .select-search-container {
        position: relative;
        width: 100%;
    }
    .search-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 1rem;
        margin-top: 0.5rem;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-sizing: border-box;
    }
    .select-dropdown {
        width: 100%;
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid #d1d5db;
        border-top: none;
        border-radius: 0 0 0.5rem 0.5rem;
        position: absolute;
        z-index: 10;
        background-color: #fff;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        display: none;
    }
    .select-dropdown-item {
        padding: 0.75rem 1rem;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .select-dropdown-item:hover {
        background-color: #f3f4f6;
    }
    .select-dropdown-item.hidden {
        display: none;
    }
    .table-container {
        overflow-x: auto;
        margin-top: 2rem;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #e5e7eb;
    }
    th {
        background-color: #f3f4f6;
        color: #4b5563;
        font-weight: 600;
    }
    tr:nth-child(even) {
        background-color: #f9fafb;
    }
</style>

<div class="content-wrapper">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Tambah Penilaian Kinerja Mitra</h1>
        <div class="card">
            <form action="../proses/proses_tambah_penilaian.php" method="POST">
                
                <div class="mb-6">
                    <label for="mitra_survey_id" class="form-label">Nama Mitra & Jenis Survei</label>
                    <div class="select-search-container">
                        <input type="text" class="search-input" placeholder="Cari mitra atau survei..." id="mitra-survey-search-input">
                        
                        <div id="mitra-survey-dropdown" class="select-dropdown">
                            <?php foreach ($mitra_surveys_list as $item) : ?>
                                <div class="select-dropdown-item" 
                                    data-id="<?= htmlspecialchars($item['id']) ?>"
                                    data-mitra-name="<?= htmlspecialchars($item['nama_mitra']) ?>"
                                    data-survey-name="<?= htmlspecialchars($item['nama_survei']) ?>">
                                    <?= htmlspecialchars($item['nama_mitra']) ?> - <?= htmlspecialchars($item['nama_survei']) ?> (Survey ke-<?= htmlspecialchars($item['survey_ke_berapa']) ?>)
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" id="mitra_survey_id" name="mitra_survey_id" required>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="penilai_nama" class="form-label">Nama Penilai</label>
                    <input type="text" 
                           class="form-input" 
                           id="penilai_nama" 
                           value="<?= htmlspecialchars($penilai_nama_otomatis) ?>" 
                           readonly>
                    <input type="hidden" 
                           id="penilai_id" 
                           name="penilai_id" 
                           value="<?= htmlspecialchars($penilai_id_otomatis) ?>" 
                           required>
                </div>
                
                <hr class="my-8">
                <h3 class="text-xl font-semibold text-gray-700 mb-4">Kategori Penilaian</h3>
                
                <div class="mb-6">
                    <label for="beban_kerja" class="form-label">Beban Kerja (Skala 1-10)</label>
                    <input type="number" id="beban_kerja" name="beban_kerja" class="form-input" min="1" max="10" required>
                </div>

                <h3 class="text-xl font-semibold text-gray-700 mb-4">Kategori Penilaian (Skala 1-4)</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="kualitas" class="form-label">Kualitas</label>
                        <input type="number" id="kualitas" name="kualitas" class="form-input" min="1" max="4" required>
                    </div>
                    <div>
                        <label for="volume_pemasukan" class="form-label">Volume Pemasukan</label>
                        <input type="number" id="volume_pemasukan" name="volume_pemasukan" class="form-input" min="1" max="4" required>
                    </div>
                    <div>
                        <label for="perilaku" class="form-label">Perilaku</label>
                        <input type="number" id="perilaku" name="perilaku" class="form-input" min="1" max="4" required>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea id="keterangan" name="keterangan" class="form-input" rows="4"></textarea>
                </div>
                
                <div class="flex justify-end space-x-4 mt-8">
                    <a href="penilaian_mitra.php" class="btn-secondary">Batal</a>
                    <button type="submit" class="btn-primary">Simpan Penilaian</button>
                </div>
            </form>
        </div>

        <div id="history-section" class="card mt-8" style="display: none;">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Riwayat Penilaian</h2>
            <div class="table-container">
                <table id="history-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Penilai</th>
                            <th>Beban Kerja</th>
                            <th>Kualitas</th>
                            <th>Volume</th>
                            <th>Perilaku</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        const message = urlParams.get('message');

        if (status && message) {
            if (status === 'error') {
                alert('Error: ' + decodeURIComponent(message).replace(/\+/g, ' '));
            } else if (status === 'success') {
                alert('Success: ' + decodeURIComponent(message).replace(/\+/g, ' '));
            }
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    });

    const penilaianHistory = <?= json_encode($penilaian_history_list); ?>;
    
    const mitraSurveySearchInput = document.getElementById('mitra-survey-search-input');
    const mitraSurveyDropdown = document.getElementById('mitra-survey-dropdown');
    const mitraSurveyHiddenInput = document.getElementById('mitra_survey_id');
    const mitraSurveyItems = mitraSurveyDropdown.querySelectorAll('.select-dropdown-item');

    // Fungsionalitas pencarian untuk Mitra & Survei
    mitraSurveySearchInput.addEventListener('focus', () => { mitraSurveyDropdown.style.display = 'block'; });
    mitraSurveySearchInput.addEventListener('blur', () => { setTimeout(() => { mitraSurveyDropdown.style.display = 'none'; }, 200); });
    mitraSurveySearchInput.addEventListener('keyup', () => {
        const filter = mitraSurveySearchInput.value.toLowerCase();
        mitraSurveyItems.forEach(item => {
            const mitraName = item.getAttribute('data-mitra-name').toLowerCase();
            const surveyName = item.getAttribute('data-survey-name').toLowerCase();
            const textContent = item.textContent.toLowerCase();
            if (mitraName.includes(filter) || surveyName.includes(filter) || textContent.includes(filter)) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    });
    
    mitraSurveyItems.forEach(item => {
        item.addEventListener('mousedown', (e) => {
            e.preventDefault();
            const id = item.getAttribute('data-id');
            const fullText = item.textContent.trim();
            mitraSurveySearchInput.value = fullText;
            mitraSurveyHiddenInput.value = id;
            mitraSurveyDropdown.style.display = 'none';
            displayHistory(id); // Memanggil fungsi untuk menampilkan riwayat
        });
    });

    function displayHistory(mitraSurveyId) {
        const historyTableBody = document.querySelector('#history-table tbody');
        const historySection = document.getElementById('history-section');
        historyTableBody.innerHTML = ''; // Kosongkan tabel sebelumnya
        
        const filteredHistory = penilaianHistory.filter(item => item.mitra_survey_id == mitraSurveyId);

        if (filteredHistory.length > 0) {
            filteredHistory.forEach(item => {
                const row = document.createElement('tr');
                // PERUBAHAN: Tambah kolom 'beban_kerja' dan 'keterangan'
                row.innerHTML = `
                    <td>${item.tanggal_penilaian}</td>
                    <td>${item.nama_penilai}</td>
                    <td>${item.beban_kerja}</td>
                    <td>${item.kualitas}</td>
                    <td>${item.volume_pemasukan}</td>
                    <td>${item.perilaku}</td>
                    <td>${item.keterangan || '-'}</td>
                `;
                historyTableBody.appendChild(row);
            });
            historySection.style.display = 'block';
        } else {
            historySection.style.display = 'none';
        }
    }
</script>

<?php 
if ($result_mitra_surveys) $result_mitra_surveys->close();
if ($result_penilaian_history) $result_penilaian_history->close();
$koneksi->close();
include '../includes/footer.php'; 
?>