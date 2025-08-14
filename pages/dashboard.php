<?php
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Query untuk menghitung total pegawai
$sql = "SELECT COUNT(*) AS total FROM pegawai";
$result = $koneksi->query($sql);
$data = $result->fetch_assoc();
?>

<main class="main-content">
    <div class="header-content">
        <h2>Dashboard</h2>
        <strong><p>Wellcome To Web. Dashboard BPS.</p></strong>
    </div>

    <div class="card profile-bps">
        <div class="profile-header">
            <!-- <img src="../assets/img/logo/logo.png" alt="Profil BPS Kabupaten Tegal" class="profile-img-lg"> -->
            <div>
                <h3>Tentang BPS Kabupaten Tegal</h3>
                <p>Badan Pusat Statistik (BPS) Kabupaten Tegal adalah lembaga pemerintah non-kementerian yang bertanggung jawab menyediakan data statistik dasar untuk membantu pemerintah dalam perencanaan dan evaluasi pembangunan.</p>
            </div>
        </div>
        <hr>
        <div class="profile-info">
            <p><strong>Alamat:</strong> Jl. Ahmad Yani No.37, Slawi, Tegal</p>
            <p><strong>Telepon:</strong> (0283) 491060</p>
            <p><strong>Email:</strong> bps3328@bps.go.id</p>
        </div>
    </div>

    <div class="card-grid">
        <div class="card card-statistic">
            <h3>Total Pegawai</h3>
            <p class="count-number"><?php echo $data['total']; ?></p>
        </div>
        <div class="card card-statistic">
            <h3>Jadwal Piket</h3>
            <p class="count-number">0</p>
        </div>
    </div>
</main>

<?php
include '../includes/footer.php';
?>