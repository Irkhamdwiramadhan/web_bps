<?php
session_start();
include '../includes/koneksi.php';

// Pastikan request menggunakan metode POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('Location: ../pages/tambah_kegiatan.php?status=error&message=' . urlencode('Metode permintaan tidak valid.'));
    exit;
}

try {
    // Tangkap data dari form dengan validasi yang lebih kuat
    $mitra_id = filter_input(INPUT_POST, 'mitra_id', FILTER_VALIDATE_INT);
    $survei_id = filter_input(INPUT_POST, 'survei_id', FILTER_VALIDATE_INT);
    $survey_ke_berapa = filter_input(INPUT_POST, 'survey_ke_berapa', FILTER_VALIDATE_INT);

    // Validasi data: pastikan semua input valid dan tidak kosong
    if ($mitra_id === false || $survei_id === false || $survey_ke_berapa === false || $mitra_id === null || $survei_id === null || $survey_ke_berapa === null) {
        throw new Exception("Data mitra, survei, atau nomor survei tidak lengkap atau tidak valid.");
    }
    
    // Siapkan kueri INSERT untuk menyimpan data
    $sql_insert = "INSERT INTO mitra_surveys (mitra_id, survei_id, survey_ke_berapa) VALUES (?, ?, ?)";
    $stmt_insert = $koneksi->prepare($sql_insert);
    
    if (!$stmt_insert) {
        throw new Exception("Gagal menyiapkan statement insert: " . $koneksi->error);
    }

    // Ikat parameter ke kueri
    $stmt_insert->bind_param("iii", $mitra_id, $survei_id, $survey_ke_berapa);
    
    // Eksekusi kueri
    if ($stmt_insert->execute()) {
        // Jika berhasil, alihkan ke halaman kegiatan dengan pesan sukses
        $stmt_insert->close();
        header('Location: ../pages/kegiatan.php?status=success&message=' . urlencode('Kegiatan mitra berhasil ditambahkan.'));
        exit;
    } else {
        // Jika gagal, lemparkan Exception (termasuk duplikasi yang akan ditangani database)
        throw new Exception("Gagal menambahkan kegiatan mitra: " . $stmt_insert->error);
    }

} catch (Exception $e) {
    // Tangani semua kesalahan dan alihkan dengan pesan error
    header('Location: ../pages/tambah_kegiatan.php?status=error&message=' . urlencode($e->getMessage()));
    exit;
}
?>