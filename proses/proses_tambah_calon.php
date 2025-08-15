<?php
include '../includes/koneksi.php';

// Cek apakah data dikirim melalui metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari formulir
    $pegawai_id = $_POST['pegawai_id'];
    $tahun      = $_POST['tahun'];
    $triwulan   = $_POST['triwulan'];

    // Validasi sederhana
    if (empty($pegawai_id) || empty($tahun) || empty($triwulan)) {
        header("Location: ../pages/calon_berprestasi.php?status=error&message=Data tidak lengkap.");
        exit();
    }
    
    // Cek apakah calon sudah ada untuk triwulan dan tahun yang sama
    $sql_check = "SELECT id FROM calon_triwulan WHERE id_pegawai = ? AND tahun = ? AND triwulan = ?";
    $stmt_check = $koneksi->prepare($sql_check);
    $stmt_check->bind_param("iis", $pegawai_id, $tahun, $triwulan);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        header("Location: ../pages/calon_berprestasi.php?status=error&message=Pegawai sudah terdaftar sebagai calon pada triwulan ini.");
        exit();
    }
    $stmt_check->close();

    // Persiapkan query untuk insert data menggunakan prepared statement
    $sql_insert = "INSERT INTO calon_triwulan (id_pegawai, tahun, triwulan) VALUES (?, ?, ?)";
    $stmt_insert = $koneksi->prepare($sql_insert);

    // Bind parameter dan eksekusi
    // "iis" artinya integer, integer, string (untuk id_pegawai, tahun, dan triwulan)
    $stmt_insert->bind_param("iis", $pegawai_id, $tahun, $triwulan);

    if ($stmt_insert->execute()) {
        // Jika berhasil, redirect kembali ke halaman daftar calon dengan status sukses
        header("Location: ../pages/calon_berprestasi.php?status=success");
    } else {
        // Jika gagal, redirect dengan status error
        header("Location: ../pages/calon_berprestasi.php?status=error&message=" . urlencode($koneksi->error));
    }

    // Tutup statement
    $stmt_insert->close();
} else {
    // Jika bukan metode POST, kembalikan ke halaman sebelumnya
    header("Location: ../pages/calon_berprestasi.php");
}

// Tutup koneksi
mysqli_close($koneksi);
exit();
?>
