<?php
session_start();
include 'includes/koneksi.php'; // Pastikan koneksi ke database

// --- KONFIGURASI SSO ---
$sso_client_id = 'YOUR_CLIENT_ID';
$sso_client_secret = 'YOUR_CLIENT_SECRET'; // Dapatkan dari tim IT BPS
$sso_redirect_uri = 'http://localhost/bps_dashboard/sso_callback.php';
$sso_token_endpoint = 'https://sso.bps.go.id/token'; // Ganti dengan Token Endpoint BPS
$sso_userinfo_endpoint = 'https://sso.bps.go.id/userinfo'; // Ganti dengan Userinfo Endpoint BPS

// Cek apakah ada 'code' otorisasi dari SSO
if (!isset($_GET['code'])) {
    // Jika tidak ada 'code', alihkan kembali ke halaman login
    header("Location: login.php");
    exit();
}

$code = $_GET['code'];

// 1. Tukar 'code' dengan token
$token_data = [
    'grant_type' => 'authorization_code',
    'client_id' => $sso_client_id,
    'client_secret' => $sso_client_secret,
    'redirect_uri' => $sso_redirect_uri,
    'code' => $code
];

$ch = curl_init($sso_token_endpoint);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Jika Anda menggunakan HTTPS, pastikan untuk mengkonfigurasi verifikasi SSL
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); 
$token_response = curl_exec($ch);
curl_close($ch);

$tokens = json_decode($token_response, true);
$access_token = $tokens['access_token'] ?? null;

if (!$access_token) {
    // Tangani error jika gagal mendapatkan token
    die("Failed to get access token from SSO.");
}

// 2. Gunakan access token untuk mendapatkan data profil pengguna
$ch = curl_init($sso_userinfo_endpoint);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer " . $access_token]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$user_response = curl_exec($ch);
curl_close($ch);

$user_data = json_decode($user_response, true);

if (!isset($user_data['nip'])) {
    die("Failed to get user data from SSO. NIP not found.");
}

$nip_pegawai = $user_data['nip'];

// 3. Cari peran pengguna di database Anda berdasarkan NIP
// Asumsi tabel Anda bernama `user_roles` dengan kolom `nip` dan `role_name`
$sql = "SELECT role_name FROM user_roles WHERE nip = ? AND is_active = TRUE";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("s", $nip_pegawai);
$stmt->execute();
$result = $stmt->get_result();

$roles = [];
while ($row = $result->fetch_assoc()) {
    $roles[] = $row['role_name'];
}

// Tentukan peran utama dan simpan ke sesi
if (in_array('super_admin', $roles)) {
    $_SESSION['user_role'] = 'super_admin';
} else if (!empty($roles)) {
    $_SESSION['user_role'] = $roles[0]; // Ambil salah satu peran jika ada
} else {
    $_SESSION['user_role'] = 'pegawai'; // Default role jika tidak ada peran admin
}

// Simpan semua peran ke sesi untuk manajemen hak akses yang lebih granular
$_SESSION['user_roles'] = $roles;
$_SESSION['user_nama'] = $user_data['nama'] ?? 'Pengguna BPS'; // Ambil nama dari SSO
$_SESSION['user_nip'] = $nip_pegawai;
$_SESSION['loggedin'] = true;

// 4. Arahkan pengguna ke dashboard
header("Location: pages/dashboard.php");
exit();
?>