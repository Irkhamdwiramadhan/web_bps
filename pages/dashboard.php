    <?php
    // Pastikan sesi dimulai di setiap halaman yang membutuhkan data sesi
    session_start();

    // Memastikan koneksi dan komponen utama terpasang.
    include '../includes/koneksi.php';

    // Mengambil total pegawai dari database
    $query_pegawai = "SELECT COUNT(*) AS total_pegawai FROM pegawai";
    $result_pegawai = $koneksi->query($query_pegawai);
    $data_pegawai = $result_pegawai->fetch_assoc();

    // Menentukan salam sapaan berdasarkan waktu
    date_default_timezone_set('Asia/Jakarta');
    $jam = date('H');
    $salam = "Selamat Malam";
    if ($jam >= 5 && $jam < 12) {
        $salam = "Selamat Pagi";
    } elseif ($jam >= 12 && $jam < 17) {
        $salam = "Selamat Siang";
    } elseif ($jam >= 17 && $jam < 20) {
        $salam = "Selamat Sore";
    }

    // Mengambil nama pengguna dari sesi untuk ditampilkan di header
    $nama_tampil = '';
    $role_tampil = $_SESSION['user_role'] ?? 'Pengguna';

    if ($role_tampil === 'admin') {
        $nama_tampil = $_SESSION['user_username'] ?? 'Admin';
    } else { // Role 'pegawai'
        $nama_tampil = $_SESSION['user_nama'] ?? 'Pegawai';
    }
    ?>

    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard BPS</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="../assets/css/style.css">
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            .main-content {
                background-color: #f3f4f6;
            }
            .elegant-card {
                border: 1px solid #e5e7eb;
                background: linear-gradient(145deg, #ffffff, #f9fafb);
            }
        </style>
    </head>
    <body>
        <div class="dashboard-wrapper">
            
            <?php include '../includes/sidebar.php'; ?>

            <main class="main-content p-8 md:p-12 transition-all duration-300">
                <div class="header-content flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                    <h2 class="text-3xl font-bold text-gray-800"><?php echo $salam; ?>, <?php echo htmlspecialchars($nama_tampil); ?>!</h2>
                    <p class="text-gray-600 mt-2 md:mt-0 text-sm md:text-base">Ringkasan Statistik Kantor BPS Kabupaten Tegal</p>
                </div>

                <div class="card elegant-card p-6 rounded-lg shadow-md flex flex-col lg:flex-row gap-8 items-center">
                    
                    <div class="w-full lg:w-1/2 flex-shrink-0 rounded-lg overflow-hidden">
                        <img src="../assets/img/logo/profil.jpeg" alt="Kantor BPS Kabupaten Tegal" class="w-full h-auto object-cover rounded-lg shadow-md">
                    </div>
                    
                    <div class="w-full lg:w-1/2">
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Tentang BPS Kabupaten Tegal</h3>
                        <p class="text-gray-600 mb-4 text-justify">
                            Badan Pusat Statistik (BPS) Kabupaten Tegal adalah lembaga pemerintah non-kementerian yang bertanggung jawab menyediakan data statistik dasar untuk membantu pemerintah dalam perencanaan dan evaluasi pembangunan. BPS berkomitmen untuk menghasilkan data yang akurat, mutakhir, dan relevan.
                        </p>
                        <div class="space-y-2 text-gray-700 mb-8">
                            <p class="flex items-center">
                                <svg class="w-5 h-5 text-gray-500 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                                <strong>Alamat:</strong> Jl. Ahmad Yani No.37, Slawi, Tegal
                            </p>
                            <p class="flex items-center">
                                <svg class="w-5 h-5 text-gray-500 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 3.318a1 1 0 01-.54 1.06l-1.548.774a11.042 11.042 0 005.516 5.516l.774-1.548a1 1 0 011.059-.54l3.318.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path></svg>
                                <strong>Telepon:</strong> (0283) 491060
                            </p>
                            <p class="flex items-center">
                                <svg class="w-5 h-5 text-gray-500 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path></svg>
                                <strong>Email:</strong> bps3328@bps.go.id
                            </p>
                        </div>
                        <br>
                        <br>
                        
                        <div class="card card-statistic bg-white p-6 rounded-lg shadow-md text-center mt-8">
                            <div class="flex flex-col items-center">
                                <svg class="h-12 w-12 text-blue-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h-5v-5a2 2 0 012-2h2a2 2 0 012 2v5zM4 15h3a2 2 0 012 2v2a2 2 0 01-2 2H4a2 2 0 01-2-2v-2a2 2 0 012-2zM9 20v-5a2 2 0 012-2h2a2 2 0 012 2v5M4 11h3a2 2 0 012-2V7a2 2 0 01-2-2H4a2 2 0 01-2 2v4a2 2 0 012 2zM9 11v-4a2 2 0 012-2h2a2 2 0 012 2v4M17 11h3a2 2 0 012-2v-2a2 2 0 01-2-2h-3a2 2 0 01-2 2v2a2 2 0 012 2z"></path></svg>
                                <div>
                                    <h5 class="text-xl font-semibold text-gray-700">Total Pegawai</h5>
                                    <p class="count-number text-5xl font-bold text-blue-600 mt-2"><?php echo $data_pegawai['total_pegawai']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <script>
            // JavaScript untuk sidebar (diambil dari file sidebar.php Anda)
            const collapseToggle = document.getElementById('collapse-toggle');
            const sidebar = document.getElementById('sidebar');
            const menuBtn = document.getElementById('menu-toggle');
            const backdrop = document.getElementById('backdrop');

            collapseToggle.addEventListener('click', () => {
                document.body.classList.toggle('sidebar-collapsed');
            });

            // Function to handle mobile sidebar
            function openSidebarMobile(open) {
                if (open) {
                    sidebar.classList.add('active');
                    document.body.classList.add('sidebar-open');
                    backdrop.hidden = false;
                    menuBtn.setAttribute('aria-expanded','true');
                } else {
                    sidebar.classList.remove('active');
                    document.body.classList.remove('sidebar-open');
                    backdrop.hidden = true;
                    menuBtn.setAttribute('aria-expanded','false');
                }
            }
            menuBtn.addEventListener('click', () => {
                openSidebarMobile(!sidebar.classList.contains('active'));
            });
            backdrop.addEventListener('click', () => openSidebarMobile(false));
            window.addEventListener('keydown', (e) => { if (e.key === 'Escape') openSidebarMobile(false); });

            const MQ = 992;
            window.addEventListener('resize', () => {
                if (window.innerWidth > MQ) openSidebarMobile(false);
            });

            const path = location.pathname.split('/').pop();
            document.querySelectorAll('.sidebar-nav a.nav-item').forEach(a => {
                const href = a.getAttribute('href');
                if (href && href === path) a.classList.add('active');
            });

        </script>
    </body>
    </html>
    <?php
    $koneksi->close();
    ?>  