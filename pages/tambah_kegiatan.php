<?php
// Mulai sesi dan sertakan file koneksi database, header, dan sidebar
session_start();
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Ambil daftar mitra dari database
$sql_mitra = "SELECT id, nama_lengkap FROM mitra ORDER BY nama_lengkap ASC";
$result_mitra = $koneksi->query($sql_mitra);

$mitra_list = [];
if ($result_mitra && $result_mitra->num_rows > 0) {
    while ($row = $result_mitra->fetch_assoc()) {
        $mitra_list[] = $row;
    }
}

// Ambil daftar survei dari database
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
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    
    body {
        font-family: 'Poppins', sans-serif;
        background: #eef2f5;
    }
    .content-wrapper {
        padding: 1rem;
    }
    @media (min-width: 640px) {
        .content-wrapper {
            margin-left: 16rem;
            padding-top: 2rem;
        }
    }
    .form-container {
        background-color: #ffffff;
        border-radius: 1rem;
        padding: 2rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    .form-label {
        font-weight: 500;
        color: #4b5563;
    }
    .form-input, .form-select {
        display: block;
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 1rem;
        margin-top: 0.5rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-input:focus, .form-select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        outline: none;
    }
    .btn-primary {
        background-color: #2563eb;
        color: #fff;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        transition: background-color 0.2s;
    }
    .btn-primary:hover {
        background-color: #1d4ed8;
    }
    .btn-secondary {
        background-color: #6b7280;
        color: #fff;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        transition: background-color 0.2s;
    }
    .btn-secondary:hover {
        background-color: #4b5563;
    }
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
</style>

<div class="content-wrapper">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Tambah Kegiatan Mitra</h1>
        <div class="form-container">
            <form action="../proses/proses_tambah_kegiatan.php" method="POST">
                
                <div class="mb-6">
                    <label for="mitra_id" class="form-label">Nama Mitra</label>
                    <select id="mitra_id" name="mitra_id" class="form-select" required>
                        <option value="">Pilih Mitra</option>
                        <?php foreach ($mitra_list as $mitra) : ?>
                            <option value="<?= htmlspecialchars($mitra['id']) ?>">
                                <?= htmlspecialchars($mitra['nama_lengkap']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-6">
                    <label for="survei_id" class="form-label">Jenis Survei</label>
                    <div class="select-search-container">
                        <input type="text" class="search-input" placeholder="Cari survei..." id="survey-search-input">
                        
                        <div id="survey-dropdown" class="select-dropdown">
                            <?php foreach ($surveys_list as $survei) : ?>
                                <div class="select-dropdown-item" 
                                     data-id="<?= htmlspecialchars($survei['id']) ?>"
                                     data-name="<?= htmlspecialchars($survei['nama_survei']) ?>"
                                     data-abbr="<?= htmlspecialchars($survei['singkatan_survei']) ?>">
                                    <?= htmlspecialchars($survei['nama_survei']) ?> (<?= htmlspecialchars($survei['singkatan_survei']) ?>)
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <input type="hidden" id="survei_id" name="survei_id" required>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="survey_ke_berapa" class="form-label">Survey Ke Berapa</label>
                    <input type="number" id="survey_ke_berapa" name="survey_ke_berapa" class="form-input" min="1" required>
                </div>
                
                <div class="flex justify-end space-x-4 mt-8">
                    <a href="kegiatan.php" class="btn-secondary">Batal</a>
                    <button type="submit" class="btn-primary">Simpan Kegiatan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const searchInput = document.getElementById('survey-search-input');
    const dropdown = document.getElementById('survey-dropdown');
    const hiddenInput = document.getElementById('survei_id');
    const items = dropdown.querySelectorAll('.select-dropdown-item');

    // Menampilkan dropdown saat input diklik
    searchInput.addEventListener('focus', () => {
        dropdown.style.display = 'block';
    });

    // Menyembunyikan dropdown saat input kehilangan fokus (kecuali saat mengklik item)
    searchInput.addEventListener('blur', () => {
        setTimeout(() => {
            dropdown.style.display = 'none';
        }, 200); // Penundaan untuk memungkinkan klik pada item
    });

    // Filter item berdasarkan input pencarian
    searchInput.addEventListener('keyup', () => {
        const filter = searchInput.value.toLowerCase();
        items.forEach(item => {
            const name = item.getAttribute('data-name').toLowerCase();
            const abbr = item.getAttribute('data-abbr').toLowerCase();
            if (name.includes(filter) || abbr.includes(filter)) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    });

    // Menangani klik pada item dropdown
    items.forEach(item => {
        item.addEventListener('mousedown', (e) => {
            e.preventDefault(); // Mencegah blur event
            const id = item.getAttribute('data-id');
            const name = item.getAttribute('data-name');
            const abbr = item.getAttribute('data-abbr');
            
            // Mengisi input pencarian dengan nama yang dipilih
            searchInput.value = `${name} (${abbr})`;
            
            // Mengisi input tersembunyi dengan ID yang benar
            hiddenInput.value = id;
            
            // Menyembunyikan dropdown
            dropdown.style.display = 'none';
        });
    });
</script>

<?php 
$result_mitra->close();
$result_surveys->close();
$koneksi->close();
include '../includes/footer.php'; 
?>