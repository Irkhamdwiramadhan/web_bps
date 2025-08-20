<?php
// PASTIKAN BARIS INI ADA DI PALING ATAS
// Memulai sesi untuk menyimpan data login
session_start();

// Memasukkan file koneksi database
include 'includes/koneksi.php';

// Inisialisasi pesan error
$error_message = '';

// Memeriksa apakah form telah disubmit melalui metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil nilai role dari form
    $role = $_POST['role'];

    // Logika untuk Login Admin
    if ($role === 'admin') {
        // Hapus spasi di awal dan akhir username serta password
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        // Gunakan prepared statement untuk mencegah SQL Injection
        // SQL disesuaikan: kolom 'foto' dihapus
        $sql = "SELECT id, username, role FROM admin_users WHERE username = ? AND password = ?";
        $stmt = $koneksi->prepare($sql);
        
        if ($stmt) {
            // Bind parameter username dan password
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Periksa apakah ada 1 baris data yang ditemukan
            if ($result->num_rows === 1) {
                // Ambil data user
                $user = $result->fetch_assoc();
                
                // Kredensial cocok, buat variabel sesi
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_username'] = $user['username'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_foto'] = $user['foto'];
                
                // Alihkan ke dashboard menggunakan jalur yang benar
                header('Location: pages/dashboard.php');
                exit();
            } else {
                // Kredensial tidak cocok
                $error_message = "Username atau password salah.";
            }
            // Tutup statement
            $stmt->close();
        } else {
            // Jika terjadi kesalahan saat menyiapkan statement
            $error_message = "Terjadi kesalahan pada database (Admin).";
        }
    } 
    // Logika untuk Login Pegawai
    elseif ($role === 'pegawai') {
        $nama_pangkat = trim($_POST['nama_pangkat']);
        $nip_bps = trim($_POST['nip_bps']);

        // SQL disesuaikan: kolom 'foto' dihapus
        $sql = "SELECT id, nama, nip_bps, foto FROM pegawai WHERE nama = ? AND nip_bps = ?";
        $stmt = $koneksi->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("ss", $nama_pangkat, $nip_bps);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nama'] = $user['nama'];
                $_SESSION['user_role'] = 'Pegawai';
                $_SESSION['user_foto'] = $user['foto'];
                header('Location: pages/dashboard.php');
                exit();
            } else {
                $error_message = "Nama atau NIP BPS tidak cocok atau tidak terdaftar.";
            }
            $stmt->close();
        } else {
            $error_message = "Terjadi kesalahan pada database (Pegawai).";
        }
    }
    // Tutup koneksi database setelah semua proses selesai
    $koneksi->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login BPS Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-image: url('');
            background-size: cover;
        }
        .login-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-container h2 {
            margin-bottom: 25px;
            color: #333;
        }
        .logo {
            width: 80px;
            height: 80px;
            margin-bottom: 15px;
        }
        .input-group {
            position: relative;
            margin-bottom: 15px;
        }
        .input-group input, .input-group select {
            width: 100%;
            padding: 12px 12px 12px 45px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
            transition: border-color 0.3s;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background: #fff;
        }
        .input-group select {
            background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 16 16'%3E%3Cpath fill='none' stroke='%23333' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3E%3C/svg%3E") no-repeat right 12px center;
            background-size: 12px;
        }
        .input-group input:focus, .input-group select:focus {
            border-color: #007bff;
            outline: none;
        }
        .input-group .icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 15px;
        }
        .btn-login:hover {
            background-color: #0056b3;
        }
        .error {
            color: #e74c3c;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="assets/img/logo/logo1.png" alt="Logo BPS" class="logo">
        <h2>Login BPS Dashboard</h2>
        <?php if ($error_message): ?>
            <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <form action="" method="POST">
            <div class="input-group">
                <i class="fas fa-users icon"></i>
                <select id="roleSelect" name="role" onchange="toggleInputs()">
                    <option value="admin">Admin</option>
                    <option value="pegawai" selected>Pegawai</option>
                </select>
            </div>

            <div class="input-group" id="usernameAdminGroup">
                <i class="fas fa-user icon"></i>
                <input type="text" name="username" placeholder="Username Admin">
            </div>
            
            <div class="input-group" id="passwordGroup">
                <i class="fas fa-lock icon"></i>
                <input type="password" name="password" placeholder="Password">
            </div>

            <div class="input-group" id="namaPangkatGroup">
                <i class="fas fa-user-tag icon"></i>
                <input type="text" name="nama_pangkat" placeholder="Nama Lengkap dan Pangkat (e.g., John Doe III/c)">
            </div>

            <div class="input-group" id="nipBpsGroup">
                <i class="fas fa-id-card icon"></i>
                <input type="text" name="nip_bps" placeholder="NIP BPS">
            </div>

            <button type="submit" class="btn-login">Masuk</button>
        </form>
    </div>

    <script>
        function toggleInputs() {
            const role = document.getElementById('roleSelect').value;
            const usernameAdminGroup = document.getElementById('usernameAdminGroup');
            const passwordGroup = document.getElementById('passwordGroup');
            const namaPangkatGroup = document.getElementById('namaPangkatGroup');
            const nipBpsGroup = document.getElementById('nipBpsGroup');
            
            const usernameInput = usernameAdminGroup.querySelector('input');
            const passwordInput = passwordGroup.querySelector('input');
            const namaPangkatInput = namaPangkatGroup.querySelector('input');
            const nipBpsInput = nipBpsGroup.querySelector('input');

            if (role === 'admin') {
                usernameAdminGroup.style.display = 'block';
                passwordGroup.style.display = 'block';
                namaPangkatGroup.style.display = 'none';
                nipBpsGroup.style.display = 'none';

                usernameInput.setAttribute('required', '');
                passwordInput.setAttribute('required', '');
                namaPangkatInput.removeAttribute('required');
                nipBpsInput.removeAttribute('required');
            } else { // 'pegawai'
                usernameAdminGroup.style.display = 'none';
                passwordGroup.style.display = 'none';
                namaPangkatGroup.style.display = 'block';
                nipBpsGroup.style.display = 'block';

                usernameInput.removeAttribute('required');
                passwordInput.removeAttribute('required');
                namaPangkatInput.setAttribute('required', '');
                nipBpsInput.setAttribute('required', '');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            toggleInputs();
        });
    </script>
</body>
</html>