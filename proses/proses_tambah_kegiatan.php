<?php
session_start();
include '../includes/koneksi.php';

// Pastikan request menggunakan metode POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('Location: ../pages/tambah_kegiatan.php?status=error&message=Metode_permintaan_tidak_valid');
    exit;
}

try {
    // Tangkap data dari form
    $mitra_id = $_POST['mitra_id'] ?? null;
    $survei_id = $_POST['survei_id'] ?? null;
    $survey_ke_berapa = $_POST['survey_ke_berapa'] ?? null;

    // Validasi data
    if (empty($mitra_id) || empty($survei_id) || empty($survey_ke_berapa)) {
        throw new Exception("Data mitra, survei, atau nomor survei tidak lengkap.");
    }
    
    // Periksa apakah entri sudah ada untuk menghindari duplikasi
    $sql_check = "SELECT COUNT(*) FROM mitra_surveys WHERE mitra_id = ? AND survei_id = ?";
    $stmt_check = $koneksi->prepare($sql_check);
    $stmt_check->bind_param("ii", $mitra_id, $survei_id);
    $stmt_check->execute();
    $stmt_check->bind_result($count);
    $stmt_check->fetch();
    $stmt_check->close();

    if ($count > 0) {
        throw new Exception("Mitra ini sudah terdaftar dalam survei yang sama.");
    }

    // Siapkan kueri INSERT untuk menyimpan data
    $sql_insert = "INSERT INTO mitra_surveys (mitra_id, survei_id, survey_ke_berapa) VALUES (?, ?, ?)";
    $stmt_insert = $koneksi->prepare($sql_insert);
    
    if (!$stmt_insert) {
        throw new Exception("Gagal menyiapkan statement: " . $koneksi->error);
    }

    // Ikat parameter ke kueri
    $stmt_insert->bind_param("iii", $mitra_id, $survei_id, $survey_ke_berapa);
    
    // Eksekusi kueri
    if ($stmt_insert->execute()) {
        // Jika berhasil, alihkan ke halaman kegiatan dengan pesan sukses
        header('Location: ../pages/kegiatan.php?status=success&message=Kegiatan_mitra_berhasil_ditambahkan');
    } else {
        // Jika gagal, lemparkan Exception
        throw new Exception("Gagal menambahkan kegiatan mitra: " . $stmt_insert->error);
    }
    
    // Tutup statement
    $stmt_insert->close();

} catch (Exception $e) {
    // Tangani semua kesalahan dan alihkan dengan pesan error
    header('Location: ../pages/tambah_kegiatan.php?status=error&message=' . urlencode($e->getMessage()));
    exit;
}
?>