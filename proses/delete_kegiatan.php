<?php
// Sertakan file koneksi database
include '../includes/koneksi.php';

// Pastikan ID kegiatan dikirim melalui parameter GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Alihkan kembali jika ID tidak ditemukan
    header('Location: ../pages/kegiatan.php?status=error&message=ID_kegiatan_tidak_ditemukan');
    exit;
}

$id_kegiatan = $_GET['id'];

try {
    // Siapkan kueri SQL untuk menghapus data dari tabel mitra_surveys
    // Gunakan prepared statement untuk keamanan
    $sql = "DELETE FROM mitra_surveys WHERE id = ?";
    $stmt = $koneksi->prepare($sql);

    // Periksa jika prepared statement gagal
    if ($stmt === false) {
        throw new Exception('Gagal menyiapkan statement: ' . $koneksi->error);
    }

    // Ikat parameter ID ke kueri
    $stmt->bind_param("i", $id_kegiatan);

    // Jalankan kueri
    if ($stmt->execute()) {
        // Jika berhasil, alihkan ke halaman kegiatan dengan pesan sukses
        header('Location: ../pages/kegiatan.php?status=success&message=Kegiatan_mitra_berhasil_dihapus');
    } else {
        // Jika gagal, lemparkan Exception
        throw new Exception('Gagal menghapus kegiatan mitra: ' . $stmt->error);
    }

    // Tutup statement
    $stmt->close();

} catch (Exception $e) {
    // Tangani kesalahan dan alihkan dengan pesan error
    header('Location: ../pages/kegiatan.php?status=error&message=' . urlencode($e->getMessage()));
    exit;
}

// Tutup koneksi database
$koneksi->close();
?>