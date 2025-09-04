<?php
session_start();
include "../includes/koneksi.php";

// Pastikan request method adalah POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../pages/tambah_kegiatan.php?status=error&message=" . urlencode("Akses tidak valid."));
    exit;
}

// Ambil input dari form
$survei_id = $_POST['survei_id'] ?? null;
$mitra_ids = $_POST['mitra_id'] ?? [];
$periode_jenis = $_POST['periode_jenis'] ?? null;
$periode_nilai = $_POST['periode_nilai'] ?? null;

// Validasi input
if (empty($survei_id) || empty($mitra_ids) || empty($periode_jenis) || empty($periode_nilai)) {
    header("Location: ../pages/tambah_kegiatan.php?status=error&message=" . urlencode("Data kegiatan tidak lengkap. Harap lengkapi semua field."));
    exit;
}

// Persiapan untuk menyimpan data
$stmt_select = null;
$stmt_insert = null;

try {
    // Memulai transaksi
    $koneksi->begin_transaction();

    // 1. Dapatkan nilai survey_ke_berapa yang berikutnya
    $sql_select = "SELECT MAX(survey_ke_berapa) AS max_survey FROM mitra_surveys WHERE survei_id = ?";
    $stmt_select = $koneksi->prepare($sql_select);
    if (!$stmt_select) {
        throw new Exception("Gagal menyiapkan statement SELECT: " . $koneksi->error);
    }
    $stmt_select->bind_param("i", $survei_id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    $row = $result->fetch_assoc();
    $survey_ke_berapa = ($row['max_survey'] !== null) ? $row['max_survey'] + 1 : 1;
    $stmt_select->close();

    // 2. Siapkan kueri INSERT
    // Nama tabel: `mitra_surveys`
    // Kolom: `mitra_id`, `survei_id`, `survey_ke_berapa`, `periode_jenis`, `periode_nilai`
    $sql_insert = "INSERT INTO mitra_surveys (mitra_id, survei_id, survey_ke_berapa, periode_jenis, periode_nilai) VALUES (?, ?, ?, ?, ?)";
    $stmt_insert = $koneksi->prepare($sql_insert);
    if (!$stmt_insert) {
        throw new Exception("Gagal menyiapkan statement INSERT: " . $koneksi->error);
    }
    
    // Binding parameter
    $stmt_insert->bind_param("iiiss", $mitra_id, $survei_id, $survey_ke_berapa, $periode_jenis, $periode_nilai);

    // Loop untuk setiap mitra yang dipilih
    foreach ($mitra_ids as $mitra_id) {
        // Eksekusi statement untuk setiap mitra
        if (!$stmt_insert->execute()) {
            throw new Exception("Gagal menyimpan data untuk mitra ID " . htmlspecialchars($mitra_id) . ": " . $stmt_insert->error);
        }
    }

    // Commit transaksi jika semua berhasil
    $koneksi->commit();
    
    // Alihkan ke halaman daftar kegiatan dengan pesan sukses
    header("Location: ../pages/kegiatan.php?status=success&message=" . urlencode("Kegiatan berhasil ditambahkan."));
    exit;

} catch (Exception $e) {
    // Rollback transaksi jika ada kesalahan
    $koneksi->rollback();
    
    // Alihkan dengan pesan error
    header("Location: ../pages/tambah_kegiatan.php?status=error&message=" . urlencode($e->getMessage()));
    exit;
} finally {
    // Pastikan statement dan koneksi selalu ditutup
    if ($stmt_select) {
        $stmt_select->close();
    }
    if ($stmt_insert) {
        $stmt_insert->close();
    }
    $koneksi->close();
}
?>