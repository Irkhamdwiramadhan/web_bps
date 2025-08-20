<?php
session_start();
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Ambil role pengguna dari sesi. Jika tidak ada, atur sebagai string kosong.
$user_role = $_SESSION['user_role'] ?? '';
?>

<main class="main-content">
    <div class="header-content">
        <h2>Data Apel Harian</h2>
        <?php if ($user_role === 'admin_apel' || $user_role === 'super_admin'): ?>
            <a href="tambah_apel.php" class="btn btn-primary">Tambah Apel</a>
        <?php endif; ?>
    </div>

    <div class="card card-description">
        <h3><i class="fas fa-info-circle"></i> Kondisi Apel dan Kehadiran</h3>
        <p>Berikut adalah penjelasan untuk setiap kondisi yang digunakan pada data apel:</p>
        <div class="description-grid">
            <div class="desc-item">
                <strong><i class="fas fa-user-check"></i> Status Kehadiran:</strong>
                <ul>
                    <li><i class="fas fa-star text-success"></i> <span class="status hadir-awal">Hadir Awal:</span> Hadir lebih awal dari jam apel.</li>
                    <li><i class="fas fa-check-circle text-success"></i> <span class="status hadir">Hadir:</span> Hadir tepat waktu saat apel dimulai.</li>
                    <li><i class="fas fa-hourglass-start text-warning"></i> <span class="status telat-1">Telat 1:</span> Datang terlambat, masih mengikuti amanat pembina.</li>
                    <li><i class="fas fa-hourglass-half text-warning"></i> <span class="status telat-2">Telat 2:</span> Datang terlambat, mengikuti apel setelah amanat pembina selesai.</li>
                    <li><i class="fas fa-hourglass-end text-warning"></i> <span class="status telat-3">Telat 3:</span> Datang terlambat, mengikuti apel saat waktu berdoa.</li>
                    <li><i class="fas fa-user-slash text-info"></i> <span class="status izin">Izin:</span> Tidak mengikuti apel dengan alasan yang sudah diketahui.</li>
                    <li><i class="fas fa-user-times text-danger"></i> <span class="status absen">Absen:</span> Tidak ada kabar dan tidak mengikuti apel.</li>
                    <li><i class="fas fa-plane text-primary"></i> <span class="status dinas-luar">Dinas Luar:</span> Tidak ikut apel karena sedang dalam tugas dinas luar.</li>
                    <li><i class="fas fa-bed text-danger"></i> <span class="status sakit">Sakit:</span> Tidak mengikuti apel karena sakit.</li>
                    <li><i class="fas fa-sun text-info"></i> <span class="status cuti">Cuti:</span> Tidak mengikuti apel karena sedang cuti.</li>
                    <li><i class="fas fa-tasks text-primary"></i> <span class="status tugas">Tugas:</span> Tidak mengikuti apel karena ada tugas khusus.</li>
                </ul>
            </div>
            <div class="desc-item">
                <strong><i class="fas fa-bullhorn"></i> Keterangan Apel:</strong>
                <ul>
                    <li><strong>Ada:</strong> Apel pagi dilaksanakan.</li>
                    <li><strong>Tidak Ada:</strong> Apel pagi tidak dilaksanakan.</li>
                    <li><strong>Lupa:</strong> Apel pagi dilaksanakan, namun lupa didokumentasikan.</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card">
        <h3>Rekapitulasi Laporan Apel Bulanan</h3>
        <div style="width: 100%; height: 500px;"> 
            <canvas id="rekapApelChart"></canvas>
        </div>
    </div>

    <div class="card">
        <h3>Rekapitulasi Status Kehadiran Apel</h3>
        <div id="kehadiran-charts-container">
        </div>
    </div>

    <div class="card">
        <h3>Data Apel</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kondisi Apel</th>
                    <th>Petugas Apel</th>
                    <th>Kehadiran</th>
                    <?php if ($user_role === 'admin_apel' || $user_role === 'super_admin'): ?>
                        <th>Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                // Mengambil data apel dari database, termasuk kolom kehadiran
                $sql = "SELECT id, tanggal, kondisi_apel, petugas, kehadiran FROM apel ORDER BY tanggal DESC";
                $result = $koneksi->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['tanggal']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['kondisi_apel']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['petugas']) . "</td>";
                        
                        echo "<td>";
                        // Menampilkan data kehadiran jika kondisi apel adalah 'ada' atau 'lupa_didokumentasikan'
                        if ($row['kondisi_apel'] === 'ada' || $row['kondisi_apel'] === 'lupa_didokumentasikan') {
                            $kehadiran_data = json_decode($row['kehadiran'], true);
                            
                            $status_counts = [];
                            if (is_array($kehadiran_data)) {
                                foreach ($kehadiran_data as $status) {
                                    $status_counts[$status['status']] = ($status_counts[$status['status']] ?? 0) + 1;
                                }
                            }

                            foreach ($status_counts as $status => $count) {
                                $class_name = str_replace('_', '-', $status);
                                $display_status = ucwords(str_replace('_', ' ', $status));
                                echo "<span class='status " . $class_name . "'>" . $display_status . ": " . $count . "</span> ";
                            }
                        } else {
                            echo "Tidak ada data kehadiran";
                        }
                        echo "</td>";

                        // Kontrol Aksi berdasarkan role
                        if ($user_role === 'admin_apel' || $user_role === 'super_admin') {
                            echo "<td>
                                    <a href='detail_apel.php?id={$row['id']}' class='btn-action detail'>Detail</a>
                                    <a href='../proses/proses_hapus_apel.php?id={$row['id']}'
                                    class='btn-action delete'
                                    onclick=\"return confirm('Apakah Anda yakin ingin menghapus data ini?');\">
                                    Hapus
                                    </a>
                                </td>";
                        } else {
                            echo "<td><a href='detail_apel.php?id={$row['id']}' class='btn-action detail'>Detail</a></td>";
                        }
                        
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' style='text-align:center;'>Tidak ada data apel.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    <?php
    // Logika untuk mengambil data rekapitulasi apel bulanan
    $rekap_data = [
        'labels' => [],
        'ada' => [],
        'tidak_ada' => [],
        'lupa_didokumentasikan' => [] 
    ];
    $sql_rekap = "SELECT DATE_FORMAT(tanggal, '%Y-%m') AS bulan, 
                          SUM(CASE WHEN kondisi_apel = 'ada' THEN 1 ELSE 0 END) as ada,
                          SUM(CASE WHEN kondisi_apel = 'tidak_ada' THEN 1 ELSE 0 END) as tidak_ada,
                          SUM(CASE WHEN kondisi_apel = 'lupa_didokumentasikan' THEN 1 ELSE 0 END) as lupa_didokumentasikan
                   FROM apel
                   GROUP BY bulan
                   ORDER BY bulan ASC";
    $result_rekap = $koneksi->query($sql_rekap);
    if ($result_rekap->num_rows > 0) {
        while($row_rekap = $result_rekap->fetch_assoc()) {
            $rekap_data['labels'][] = $row_rekap['bulan'];
            $rekap_data['ada'][] = $row_rekap['ada'];
            $rekap_data['tidak_ada'][] = $row_rekap['tidak_ada'];
            $rekap_data['lupa_didokumentasikan'][] = $row_rekap['lupa_didokumentasikan'];
        }
    }

    // Logika untuk mengambil data rekapitulasi kehadiran per bulan
    $monthly_kehadiran_data = [];
    $sql_kehadiran_monthly = "SELECT tanggal, kehadiran FROM apel WHERE kondisi_apel IN ('ada', 'lupa_didokumentasikan') ORDER BY tanggal ASC";
    $result_kehadiran_monthly = $koneksi->query($sql_kehadiran_monthly);

    if ($result_kehadiran_monthly->num_rows > 0) {
        while ($row_monthly = $result_kehadiran_monthly->fetch_assoc()) {
            $month = date('Y-m', strtotime($row_monthly['tanggal']));
            $kehadiran_json = json_decode($row_monthly['kehadiran'], true);
            if (is_array($kehadiran_json)) {
                if (!isset($monthly_kehadiran_data[$month])) {
                    $monthly_kehadiran_data[$month] = [
                        'hadir_awal' => 0, 'hadir' => 0, 'telat_1' => 0, 'telat_2' => 0, 'telat_3' => 0,
                        'izin' => 0, 'absen' => 0, 'dinas_luar' => 0, 'sakit' => 0, 'cuti' => 0, 'tugas' => 0
                    ];
                }
                foreach ($kehadiran_json as $kehadiran_item) {
                    if (isset($kehadiran_item['status'])) {
                        $status = $kehadiran_item['status'];
                        if (array_key_exists($status, $monthly_kehadiran_data[$month])) {
                            $monthly_kehadiran_data[$month][$status]++;
                        }
                    }
                }
            }
        }
    }

    // Ubah data menjadi format yang dibutuhkan Chart.js untuk setiap bulan
    $monthly_kehadiran_datasets = [];
    $kehadiran_status_labels = [
        'hadir_awal' => 'Hadir Awal', 'hadir' => 'Hadir', 'telat_1' => 'Telat 1',
        'telat_2' => 'Telat 2', 'telat_3' => 'Telat 3', 'izin' => 'Izin',
        'absen' => 'Absen', 'dinas_luar' => 'Dinas Luar', 'sakit' => 'Sakit',
        'cuti' => 'Cuti', 'tugas' => 'Tugas'
    ];
    $background_colors = [
        '#28a745', '#17a2b8', '#ffc107', '#fd7e14', '#e83e8c', 
        '#6f42c1', '#dc3545', '#007bff', '#20c997', '#6c757d', '#adb5bd'
    ];
    
    foreach ($monthly_kehadiran_data as $month => $counts) {
        $datasets = [];
        $data_points = array_values($counts);
        $datasets[] = [
            'label' => 'Jumlah Kehadiran',
            'data' => $data_points,
            'backgroundColor' => $background_colors,
            'borderColor' => 'rgba(255, 255, 255, 1)',
            'borderWidth' => 2
        ];
        $monthly_kehadiran_datasets[$month] = [
            'labels' => array_values($kehadiran_status_labels),
            'datasets' => $datasets
        ];
    }
    ?>

    const rekapApelChartData = {
        labels: <?php echo json_encode($rekap_data['labels']); ?>,
        datasets: [
            {
                label: 'Apel Dilaksanakan',
                data: <?php echo json_encode($rekap_data['ada']); ?>,
                backgroundColor: 'rgba(40, 167, 69, 0.7)',
                borderColor: '#28a745',
                borderWidth: 2
            },
            {
                label: 'Apel Tidak Dilaksanakan',
                data: <?php echo json_encode($rekap_data['tidak_ada']); ?>,
                backgroundColor: 'rgba(220, 53, 69, 0.7)',
                borderColor: '#dc3545',
                borderWidth: 2
            },
            {
                label: 'Apel Lupa Didokumentasikan',
                data: <?php echo json_encode($rekap_data['lupa_didokumentasikan']); ?>,
                backgroundColor: 'rgba(255, 193, 7, 0.7)',
                borderColor: '#ffc107',
                borderWidth: 2
            }
        ]
    };

    const ctxApel = document.getElementById('rekapApelChart').getContext('2d');
    const rekapApelChart = new Chart(ctxApel, {
        type: 'bar',
        data: rekapApelChartData,
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Rekapitulasi Kondisi Apel Bulanan'
                }
            }
        }
    });

    const monthlyKehadiranData = <?php echo json_encode($monthly_kehadiran_datasets); ?>;
    const container = document.getElementById('kehadiran-charts-container');

    for (const month in monthlyKehadiranData) {
        if (monthlyKehadiranData.hasOwnProperty(month)) {
            const chartData = monthlyKehadiranData[month];
            
            const chartWrapper = document.createElement('div');
            chartWrapper.className = 'chart-wrapper';

            const chartCanvas = document.createElement('canvas');
            chartCanvas.id = `kehadiranChart-${month}`;
            chartWrapper.appendChild(chartCanvas);
            container.appendChild(chartWrapper);
            
            const ctx = chartCanvas.getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: `Rekap Kehadiran Bulan ${month}`
                        }
                    }
                }
            });
        }
    }
</script>

<?php include '../includes/footer.php'; ?>
