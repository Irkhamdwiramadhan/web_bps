<?php
session_start();
include '../includes/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('Location: ../pages/tambah_mitra.php?status=error&message=Metode_permintaan_tidak_valid');
    exit;
}

try {
    $id_mitra = $_POST['id_mitra'] ?? null;
    $nama_lengkap = $_POST['nama_lengkap'] ?? null;
    $nik = $_POST['nik'] ?? null;
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? null;
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? null;
    $agama = $_POST['agama'] ?? null;
    $status_perkawinan = $_POST['status_perkawinan'] ?? null;
    $pendidikan = $_POST['pendidikan'] ?? null;
    $pekerjaan = $_POST['pekerjaan'] ?? null;
    $deskripsi_pekerjaan_lain = $_POST['deskripsi_pekerjaan_lain'] ?? null;
    $npwp = $_POST['npwp'] ?? null;
    $no_telp = $_POST['no_telp'] ?? null;
    $email = $_POST['email'] ?? null;

    $alamat_provinsi = $_POST['alamat_provinsi'] ?? null;
    $alamat_kabupaten = $_POST['alamat_kabupaten'] ?? null;
    $nama_kecamatan = $_POST['nama_kecamatan'] ?? null;
    $alamat_desa = $_POST['alamat_desa'] ?? null;
    $nama_desa = $_POST['nama_desa'] ?? null;
    $alamat_detail = $_POST['alamat_detail'] ?? null;

    // Perbaikan: Gunakan ternary operator untuk mengubah nilai checkbox menjadi 1 atau 0
    $domisili_sama = isset($_POST['domisili_sama']) ? 1 : 0;
    
    $mengikuti_pendataan_bps = $_POST['mengikuti_pendataan_bps'] ?? 'Tidak';
    $posisi = $_POST['posisi'] ?? null;
    
    $sp = isset($_POST['sp']) ? 1 : 0;
    $st = isset($_POST['st']) ? 1 : 0;
    $se = isset($_POST['se']) ? 1 : 0;
    $susenas = isset($_POST['susenas']) ? 1 : 0;
    $sakernas = isset($_POST['sakernas']) ? 1 : 0;
    $sbh = isset($_POST['sbh']) ? 1 : 0;

    $foto_path = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $nama_file = basename($_FILES['foto']['name']);
        $ekstensi = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
        $nama_unik = uniqid('foto_') . '.' . $ekstensi;
        $target_file = $target_dir . $nama_unik;

        $check = getimagesize($_FILES['foto']['tmp_name']);
        if ($check === false) {
            throw new Exception("File yang diunggah bukan gambar.");
        }

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            $foto_path = $nama_unik;
        } else {
            throw new Exception("Gagal mengunggah foto.");
        }
    }

    $sql = "INSERT INTO mitra (
        id_mitra, foto, nama_lengkap, nik, tanggal_lahir, jenis_kelamin, agama,
        status_perkawinan, pendidikan, pekerjaan, deskripsi_pekerjaan_lain,
        npwp, no_telp, email, alamat_provinsi, alamat_kabupaten,
        nama_kecamatan, alamat_desa, nama_desa, alamat_detail, domisili_sama,
        mengikuti_pendataan_bps, posisi, sp, st, se, susenas, sakernas, sbh
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
    )";
    
    $stmt = $koneksi->prepare($sql);
    
    if ($stmt === false) {
        throw new Exception("Gagal menyiapkan statement: " . $koneksi->error);
    }
    
    // Perbaikan: Ubah tipe data untuk kolom TINYINT (i = integer)
    $types = "ssssssssssssssssssiisssiiiiii";

    $params = [
        $id_mitra,
        $foto_path,
        $nama_lengkap, $nik, $tanggal_lahir, $jenis_kelamin, $agama, $status_perkawinan,
        $pendidikan, $pekerjaan, $deskripsi_pekerjaan_lain, $npwp, $no_telp, $email,
        $alamat_provinsi, $alamat_kabupaten, $nama_kecamatan, $alamat_desa, $nama_desa,
        $alamat_detail, $domisili_sama, $mengikuti_pendataan_bps, $posisi,
        $sp, $st, $se, $susenas, $sakernas, $sbh
    ];
    
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        header('Location: ../pages/mitra.php?status=success&message=Mitra_berhasil_ditambahkan');
    } else {
        throw new Exception('Gagal menambahkan mitra: ' . $stmt->error);
    }

    $stmt->close();

} catch (Exception $e) {
    $errorMessage = urlencode($e->getMessage());
    header('Location: ../pages/tambah_mitra.php?status=error&message=' . $errorMessage);
} finally {
    if (isset($koneksi)) {
        $koneksi->close();
    }
    exit;
}
?>