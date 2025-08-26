<?php
session_start();
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap');
    
    body {
        font-family: 'Inter', sans-serif;
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

    .form-input {
        @apply mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2;
    }
    .form-label {
        @apply block text-sm font-medium text-gray-700;
    }
    .form-checkbox {
        @apply h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded;
    }

    .form-input.error {
        @apply border-red-500;
    }
    .error-message {
        @apply text-red-500 text-sm mt-1;
    }

    .modal {
        @apply fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900 bg-opacity-50;
    }
    .modal-content {
        @apply bg-white rounded-lg shadow-xl max-w-sm w-full p-6 space-y-4;
    }
</style>

<div class="content-wrapper bg-gray-100 min-h-screen">
    <div class="max-w-6xl mx-auto bg-white rounded-xl shadow-lg p-8 mt-14 mb-14">
        <h1 class="text-4xl font-extrabold text-center text-gray-800 mb-10">Formulir Tambah Mitra</h1>
        
        <?php
        if (isset($_GET['status']) && isset($_GET['message'])) {
            $status = $_GET['status'];
            $message = str_replace('_', ' ', $_GET['message']);
            if ($status === 'success') {
                echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert"><strong class="font-bold">Sukses!</strong><span class="block sm:inline"> ' . htmlspecialchars($message) . '</span></div>';
            } else if ($status === 'error') {
                echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert"><strong class="font-bold">Error!</strong><span class="block sm:inline"> ' . htmlspecialchars($message) . '</span></div>';
            }
        }
        ?>

        <form id="formTambahMitra" action="../proses/proses_mitra.php" method="POST" enctype="multipart/form-data" class="space-y-8">
            <div class="bg-gray-50 p-6 rounded-lg shadow-inner">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6 border-b pb-2">Data Pribadi</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label for="id_mitra" class="form-label">ID Mitra</label>
                        <input type="text" id="id_mitra" name="id_mitra" required class="form-input">
                    </div>
                    <div>
                        <label for="foto" class="form-label">Unggah Foto Profil</label>
                        <input type="file" id="foto" name="foto" accept="image/*" class="form-input text-gray-900 bg-white cursor-pointer focus:outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" required class="form-input">
                    </div>
                    <div>
                        <label for="nik" class="form-label">NIK</label>
                        <input type="text" id="nik" name="nik" required class="form-input">
                    </div>
                    <div>
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                        <input type="date" id="tanggal_lahir" name="tanggal_lahir" class="form-input">
                    </div>
                    <div>
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                        <select id="jenis_kelamin" name="jenis_kelamin" class="form-input">
                            <option value="">Pilih</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label for="agama" class="form-label">Agama</label>
                        <select id="agama" name="agama" class="form-input">
                            <option value="">Pilih</option>
                            <option value="Islam">Islam</option>
                            <option value="Kristen">Kristen</option>
                            <option value="Katolik">Katolik</option>
                            <option value="Hindu">Hindu</option>
                            <option value="Buddha">Buddha</option>
                            <option value="Konghucu">Konghucu</option>
                        </select>
                    </div>
                    <div>
                        <label for="status_perkawinan" class="form-label">Status Perkawinan</label>
                        <select id="status_perkawinan" name="status_perkawinan" class="form-input">
                            <option value="">Pilih</option>
                            <option value="Belum Menikah">Belum Menikah</option>
                            <option value="Menikah">Menikah</option>
                            <option value="Cerai Hidup">Cerai Hidup</option>
                            <option value="Cerai Mati">Cerai Mati</option>
                        </select>
                    </div>
                    <div>
                        <label for="pendidikan" class="form-label">Pendidikan Terakhir</label>
                        <input type="text" id="pendidikan" name="pendidikan" class="form-input">
                    </div>
                    <div>
                        <label for="pekerjaan" class="form-label">Pekerjaan Utama</label>
                        <input type="text" id="pekerjaan" name="pekerjaan" class="form-input">
                    </div>
                    <div>
                        <label for="deskripsi_pekerjaan_lain" class="form-label">Deskripsi Pekerjaan Lain</label>
                        <input type="text" id="deskripsi_pekerjaan_lain" name="deskripsi_pekerjaan_lain" class="form-input">
                    </div>
                    <div>
                        <label for="npwp" class="form-label">NPWP</label>
                        <input type="text" id="npwp" name="npwp" class="form-input">
                    </div>
                    <div>
                        <label for="no_telp" class="form-label">Nomor Telepon</label>
                        <input type="text" id="no_telp" name="no_telp" class="form-input">
                    </div>
                    <div class="md:col-span-2">
                        <label for="email" class="form-label">Alamat Email</label>
                        <input type="email" id="email" name="email" class="form-input">
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-6 rounded-lg shadow-inner">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6 border-b pb-2">Data Alamat</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="alamat_provinsi" class="form-label">Provinsi</label>
                        <input type="text" id="alamat_provinsi" name="alamat_provinsi" class="form-input">
                    </div>
                    <div>
                        <label for="alamat_kabupaten" class="form-label">Kabupaten</label>
                        <input type="text" id="alamat_kabupaten" name="alamat_kabupaten" class="form-input">
                    </div>
                    <div>
                        <label for="nama_kecamatan" class="form-label">Kecamatan</label>
                        <input type="text" id="nama_kecamatan" name="nama_kecamatan" class="form-input">
                    </div>
                    <div>
                        <label for="alamat_desa" class="form-label">Kode/Nama Desa</label>
                        <input type="text" id="alamat_desa" name="alamat_desa" class="form-input">
                    </div>
                    <div class="md:col-span-2">
                        <label for="nama_desa" class="form-label">Nama Desa</label>
                        <input type="text" id="nama_desa" name="nama_desa" class="form-input">
                    </div>
                    <div class="md:col-span-2">
                        <label for="alamat_detail" class="form-label">Detail Alamat</label>
                        <textarea id="alamat_detail" name="alamat_detail" rows="3" class="form-input"></textarea>
                    </div>
                    <div class="md:col-span-2 flex items-center">
                        <input type="checkbox" id="domisili_sama" name="domisili_sama" value="1" class="form-checkbox">
                        <label for="domisili_sama" class="ml-2 form-label !text-base">Alamat domisili sama dengan KTP</label>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-6 rounded-lg shadow-inner">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6 border-b pb-2">Pengalaman Survei</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="mengikuti_pendataan_bps" class="form-label">Mengikuti Pendataan BPS</label>
                        <select id="mengikuti_pendataan_bps" name="mengikuti_pendataan_bps" class="form-input">
                            <option value="">Pilih</option>
                            <option value="Ya">Ya</option>
                            <option value="Tidak">Tidak</option>
                        </select>
                    </div>
                    <div>
                        <label for="posisi" class="form-label">Posisi</label>
                        <select id="posisi" name="posisi" class="form-input">
                            <option value="">Pilih</option>
                            <option value="mitra_pendataan">Mitra Pendataan</option>
                            <option value="mitra_pengolahan">Mitra Pengolahan</option>
                            <option value="mitra_pendataan_dan_pengolahan">Mitra Pendataan dan Pengolahan</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="form-label">Pengalaman Survei Lainnya (Jika ada, beri tanda centang)</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-2">
                            <div class="flex items-center">
                                <input type="checkbox" id="sp" name="sp" **value="1"** class="form-checkbox">
                                <label for="sp" class="ml-2 form-label !text-base !mb-0">SP</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="st" name="st" **value="1"** class="form-checkbox">
                                <label for="st" class="ml-2 form-label !text-base !mb-0">ST</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="se" name="se" **value="1"** class="form-checkbox">
                                <label for="se" class="ml-2 form-label !text-base !mb-0">SE</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="susenas" name="susenas" **value="1"** class="form-checkbox">
                                <label for="susenas" class="ml-2 form-label !text-base !mb-0">Susenas</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="sakernas" name="sakernas" **value="1"** class="form-checkbox">
                                <label for="sakernas" class="ml-2 form-label !text-base !mb-0">Sakernas</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="sbh" name="sbh" **value="1"** class="form-checkbox">
                                <label for="sbh" class="ml-2 form-label !text-base !mb-0">SBH</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <button type="button" onclick="window.location.href='mitra.php'" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">Batal</button>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Simpan Mitra</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formTambahMitra');
        form.addEventListener('submit', function(event) {
            let isValid = true;
            const requiredFields = ['id_mitra', 'nama_lengkap', 'nik'];

            document.querySelectorAll('.form-input').forEach(input => {
                input.classList.remove('error');
            });
            document.querySelectorAll('.error-message').forEach(msg => {
                msg.remove();
            });

            requiredFields.forEach(fieldId => {
                const input = document.getElementById(fieldId);
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('error');
                    const errorMessage = document.createElement('p');
                    errorMessage.className = 'error-message';
                    errorMessage.textContent = 'Kolom ini wajib diisi.';
                    input.parentNode.appendChild(errorMessage);
                }
            });

            if (!isValid) {
                event.preventDefault();
                alert('Silakan lengkapi semua kolom yang wajib diisi.');
            }
        });
    });
</script>

<?php include '../includes/footer.php'; ?>