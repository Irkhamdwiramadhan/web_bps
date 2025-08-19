<?php
session_start();
include '../includes/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = trim($_POST['role']);
    
    if ($role === 'admin') {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        $sql = "SELECT id, username, password, role FROM admin_users WHERE username = ?";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['user_nama'] = $user['username'];
                $_SESSION['user_role'] = $user['role'];

                header("Location: ../pages/dashboard.php");
                exit();
            }
        }
    } elseif ($role === 'pegawai') {
        $nip_bps = trim($_POST['nip_bps']);
        $nama_pangkat = trim($_POST['nama_pangkat']);
        
        $sql = "SELECT nip_bps, nama FROM pegawai WHERE nip_bps = ?";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("s", $nip_bps);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $pegawai = $result->fetch_assoc();
            
            if ($pegawai['nama'] === $nama_pangkat) {
                $_SESSION['loggedin'] = true;
                $_SESSION['nip'] = $pegawai['nip_bps'];
                $_SESSION['user_nama'] = $pegawai['nama'];
                $_SESSION['user_role'] = "Pegawai";
                
                header("Location: ../pages/dashboard.php");
                exit();
            }
        }
    }
    
    header("Location: ../pages/login.php?status=error&message=NIP atau Nama salah.");
    exit();

} else {
    header("Location: ../pages/login.php");
    exit();
}
?>