<?php
// Masukkan file koneksi database
include '../includes/koneksi.php';

// Cek apakah request datang dari metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari formulir
    $calon_id = $_POST['calon_id'];
    $penilai_id = $_POST['penilai_id'];
    
    // Ambil 7 skor kriteria yang relevan dengan struktur tabel
    $skor_berorientasi   = isset($_POST['kriteria_1']) ? (int)$_POST['kriteria_1'] : 0;
    $skor_akuntabel      = isset($_POST['kriteria_2']) ? (int)$_POST['kriteria_2'] : 0;
    $skor_kompeten       = isset($_POST['kriteria_3']) ? (int)$_POST['kriteria_3'] : 0;
    $skor_harmonis       = isset($_POST['kriteria_4']) ? (int)$_POST['kriteria_4'] : 0;
    $skor_loyal          = isset($_POST['kriteria_5']) ? (int)$_POST['kriteria_5'] : 0;
    $skor_adaptif        = isset($_POST['kriteria_6']) ? (int)$_POST['kriteria_6'] : 0;
    $skor_kolaboratif    = isset($_POST['kriteria_7']) ? (int)$_POST['kriteria_7'] : 0;

    // Validasi sederhana untuk memastikan semua data terisi
    if (empty($calon_id) || empty($penilai_id)) {
        header("Location: ../pages/form_penilaian.php?status=error&message=Semua field harus diisi.");
        exit();
    }

    // Pastikan nilai kriteria dalam rentang yang valid (0-100)
    if ($skor_berorientasi < 0 || $skor_berorientasi > 100 || 
        $skor_akuntabel < 0 || $skor_akuntabel > 100 ||
        $skor_kompeten < 0 || $skor_kompeten > 100 ||
        $skor_harmonis < 0 || $skor_harmonis > 100 ||
        $skor_loyal < 0 || $skor_loyal > 100 ||
        $skor_adaptif < 0 || $skor_adaptif > 100 ||
        $skor_kolaboratif < 0 || $skor_kolaboratif > 100) {
        header("Location: ../pages/form_penilaian.php?status=error&message=Nilai kriteria harus antara 0-100.");
        exit();
    }
    
    // Periksa apakah penilai sudah memberikan nilai untuk calon yang sama
    $sql_check = "SELECT id FROM penilaian_triwulan WHERE id_calon = ? AND id_penilai = ?";
    $stmt_check = $koneksi->prepare($sql_check);
    $stmt_check->bind_param("ii", $calon_id, $penilai_id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        header("Location: ../pages/form_penilaian.php?status=error&message=Anda sudah memberikan penilaian untuk calon ini.");
        exit();
    }
    $stmt_check->close();

    // Ambil data triwulan dan tahun dari tabel calon_triwulan
    $sql_calon = "SELECT triwulan, tahun FROM calon_triwulan WHERE id = ?";
    $stmt_calon = $koneksi->prepare($sql_calon);
    $stmt_calon->bind_param("i", $calon_id);
    $stmt_calon->execute();
    $result_calon = $stmt_calon->get_result();
    $data_calon = $result_calon->fetch_assoc();
    $triwulan = $data_calon['triwulan'];
    $tahun = $data_calon['tahun'];
    $stmt_calon->close();

    // Hitung total skor dari 7 kriteria
    $total_skor = $skor_berorientasi + $skor_akuntabel + $skor_kompeten + $skor_harmonis + $skor_loyal + $skor_adaptif + $skor_kolaboratif;

    // Persiapkan query untuk insert data ke tabel 'penilaian_triwulan'
    $sql_insert = "
        INSERT INTO penilaian_triwulan (id_calon, id_penilai, triwulan, tahun, skor_berorientasi, skor_akuntabel, skor_kompeten, skor_harmonis, skor_loyal, skor_adaptif, skor_kolaboratif, total_skor) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $koneksi->prepare($sql_insert);

    // Bind parameter dan eksekusi
    // "iisisiiiiiii" artinya: int, int, string, int, dan delapan integer lainnya
    $stmt_insert->bind_param("iisisiiiiiii", $calon_id, $penilai_id, $triwulan, $tahun, $skor_berorientasi, $skor_akuntabel, $skor_kompeten, $skor_harmonis, $skor_loyal, $skor_adaptif, $skor_kolaboratif, $total_skor);
    
    if ($stmt_insert->execute()) {
        // Jika berhasil, redirect dengan status sukses
        header("Location: ../pages/hasil_penilaian.php?status=success_penilaian");
    } else {
        // Jika gagal, redirect dengan status error
        header("Location: ../pages/form_penilaian.php?status=error&message=" . urlencode($koneksi->error));
    }

    // Tutup statement
    $stmt_insert->close();
} else {
    // Jika bukan metode POST, kembalikan ke halaman sebelumnya
    header("Location: ../pages/form_penilaian.php");
}

// Tutup koneksi
mysqli_close($koneksi);
exit();
?>
