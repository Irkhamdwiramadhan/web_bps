<?php
include '../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Metode tidak diizinkan.";
    exit;
}

// Ambil data dari form
$pegawaiId       = isset($_POST['pegawai_id']) ? (int)$_POST['pegawai_id'] : 0;
$totalPenjualan  = isset($_POST['total_penjualan']) ? (float)$_POST['total_penjualan'] : 0;
$items           = isset($_POST['produk']) ? $_POST['produk'] : [];

// Ambil tanggal transaksi dari form (datetime-local) atau gunakan waktu sekarang
if (!empty($_POST['tanggal_transaksi'])) {
    $tanggalTransaksi = date('Y-m-d H:i:s', strtotime($_POST['tanggal_transaksi']));
} else {
    $tanggalTransaksi = date('Y-m-d H:i:s');
}

// Validasi minimal
if ($pegawaiId <= 0) {
    echo "Gagal menyimpan penjualan: Pegawai belum dipilih.";
    exit;
}
if (empty($items)) {
    echo "Gagal menyimpan penjualan: Belum ada produk yang dipilih.";
    exit;
}

mysqli_begin_transaction($koneksi);

try {
    // 1) Insert ke sales (asumsi kolom: id, pegawai_id, date TIMESTAMP/DATETIME, total)
    $sqlSales = "INSERT INTO sales (pegawai_id, date, total) VALUES (?, ?, ?)";
    $stmtSales = mysqli_prepare($koneksi, $sqlSales);
    if (!$stmtSales) {
        throw new Exception("Gagal menyiapkan query sales.");
    }
    mysqli_stmt_bind_param($stmtSales, "isd", $pegawaiId, $tanggalTransaksi, $totalPenjualan);
    if (!mysqli_stmt_execute($stmtSales)) {
        throw new Exception("Gagal insert sales: " . mysqli_error($koneksi));
    }
    $saleId = mysqli_insert_id($koneksi);

    // Siapkan statement ambil harga + promo
    $sqlGetPrice = "SELECT price, is_promo, promo_price, stock FROM products WHERE id = ? FOR UPDATE";
    $stmtGetPrice = mysqli_prepare($koneksi, $sqlGetPrice);
    if (!$stmtGetPrice) {
        throw new Exception("Gagal menyiapkan query harga produk.");
    }

    // Siapkan insert sales_items
    $sqlItems = "INSERT INTO sales_items (sale_id, product_id, qty, price) VALUES (?, ?, ?, ?)";
    $stmtItems = mysqli_prepare($koneksi, $sqlItems);
    if (!$stmtItems) {
        throw new Exception("Gagal menyiapkan query sales_items.");
    }

    // Siapkan update stok
    $sqlUpdateStock = "UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?";
    $stmtUpdateStock = mysqli_prepare($koneksi, $sqlUpdateStock);
    if (!$stmtUpdateStock) {
        throw new Exception("Gagal menyiapkan query update stok.");
    }

    // 2) Loop setiap item
    foreach ($items as $productId => $data) {
        $productId = (int)$productId;
        $qty       = isset($data['qty']) ? (int)$data['qty'] : 0;
        if ($productId <= 0 || $qty <= 0) {
            throw new Exception("Data item tidak valid.");
        }

        // Ambil harga dan stok terkini (lock baris produk agar stok konsisten)
        mysqli_stmt_bind_param($stmtGetPrice, "i", $productId);
        mysqli_stmt_execute($stmtGetPrice);
        $res = mysqli_stmt_get_result($stmtGetPrice);
        $prod = mysqli_fetch_assoc($res);

        if (!$prod) {
            throw new Exception("Produk ID {$productId} tidak ditemukan.");
        }

        $price       = (float)$prod['price'];
        $promoActive = (int)$prod['is_promo'] === 1;
        $promoPrice  = isset($prod['promo_price']) ? (float)$prod['promo_price'] : 0.0;
        $stockNow    = (int)$prod['stock'];

        if ($stockNow < $qty) {
            throw new Exception("Stok produk ID {$productId} tidak cukup. Sisa: {$stockNow}, diminta: {$qty}.");
        }

        // Tentukan harga efektif saat transaksi
        $finalPrice = ($promoActive && $promoPrice > 0) ? $promoPrice : $price;

        // Insert item
        mysqli_stmt_bind_param($stmtItems, "iiid", $saleId, $productId, $qty, $finalPrice);
        if (!mysqli_stmt_execute($stmtItems)) {
            throw new Exception("Gagal insert item untuk produk {$productId}.");
        }

        // Update stok (pastikan tidak minus)
        mysqli_stmt_bind_param($stmtUpdateStock, "iii", $qty, $productId, $qty);
        if (!mysqli_stmt_execute($stmtUpdateStock) || mysqli_stmt_affected_rows($stmtUpdateStock) === 0) {
            throw new Exception("Gagal mengurangi stok produk {$productId} (kemungkinan stok tidak cukup).");
        }
    }

    // 3) Commit
    mysqli_commit($koneksi);
    header("Location: ../pages/history_penjualan.php?status=success");
    exit;

} catch (Exception $e) {
    mysqli_rollback($koneksi);
    // Tampilkan pesan yang jelas
    echo "Gagal menyimpan penjualan: " . $e->getMessage();
}
