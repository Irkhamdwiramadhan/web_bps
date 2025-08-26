<?php
session_start();
include '../includes/koneksi.php';

// Pastikan request menggunakan metode GET dan ID mitra ada
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    
    // Ambil ID dari URL dan bersihkan dari karakter berbahaya
    $id_mitra = $_GET['id'];
    
    // Siapkan query DELETE dengan prepared statement untuk keamanan
    $sql = "DELETE FROM mitra WHERE id = ?";
    
    $stmt = $koneksi->prepare($sql);
    
    // Periksa apakah prepared statement berhasil
    if ($stmt) {
        // Ikat parameter id ke statement
        $stmt->bind_param("i", $id_mitra);
        
        // Eksekusi statement
        if ($stmt->execute()) {
            // Redirect kembali ke halaman mitra.php dengan pesan sukses
            header("Location: ../pages/mitra.php?status=success&message=Data_mitra_berhasil_dihapus");
            exit();
        } else {
            // Redirect dengan pesan error jika eksekusi gagal
            header("Location: ../pages/mitra.php?status=error&message=Gagal_menghapus_data_mitra");
            exit();
        }
    } else {
        // Redirect dengan pesan error jika prepared statement gagal
        header("Location: ../pages/mitra.php?status=error&message=Gagal_menyiapkan_statement");
        exit();
    }
} else {
    // Redirect dengan pesan error jika permintaan tidak valid
    header("Location: ../pages/mitra.php?status=error&message=Permintaan_tidak_valid");
    exit();
}
?>